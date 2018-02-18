<?php
namespace Sma\Bean;

use Osf\View\Helper\Bootstrap\AbstractViewHelper as AVH;
use Osf\Pdf\Document\Bean\ContactBean as OCB;
use Osf\Pdf\Document\Bean\BankDetailsBean;
use Osf\Pdf\Document\Bean\AddressBean;
use Osf\Exception\DisplayedException;
use Osf\Pdf\Document\Bean\ImageBean;
use Osf\Exception\ArchException;
use Osf\Stream\Text;
use Sma\Session\Identity as I;
use Sma\Bean\ContactBean;
use Sma\Db\ContactRow;
use Sma\Db\CompanyRow;
use Sma\Image;
use Sma\Db\CompanyTable;
use DB;

/**
 * Extension du ContactBean OSF
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage bean
 */
class ContactBean extends OCB implements Addon\WarningInterface, ExchangeableBeanInterface
{
    use Addon\WarningTools;
    
    /**
     * Utilisé pour la création des beans letter / invoice entre les formulaires et les documents
     * @param ContactRow $contact
     * @param CompanyRow $company
     * @param bool $includeIdentityInfo
     * @return \Sma\Bean\ContactBean
     */
    protected static function buildContactBean(
            ContactRow $contact, 
            CompanyRow $company, 
            bool $includeIdentityInfo = false): ContactBean
    {
        $address = $company->getRelatedAddressRowFromIdAddressFk();
        $address = $address && !$address->isEmpty() ? $address : $contact->getRelatedAddressRowFromIdAddressFk();
        $address = $address ?: DB::buildAddressRow();
        
        $delivAddress = $company->getRelatedAddressRowFromIdAddressDeliveryFk();
        $delivAddress = $delivAddress && !$delivAddress->isEmpty() ? $delivAddress : null;
        $delivAddress = $delivAddress ?: DB::buildAddressRow();
        
        // Informations générales sur le contact et l'entreprise
        $contactBean = new ContactBean();
        $contactBean
                ->setId($contact->getId())
                ->setIdCompany($company->getId())
                ->setFirstname($contact->getFirstname())
                ->setLastname($contact->getLastname())
                ->setCivility($contact->getCivility())
                ->setTel($contact->getTel())
                ->setGsm($contact->getGsm())
                ->setFax($contact->getFax())
                ->setEmail($contact->getEmail())
                ->setAddress((new AddressBean())
                        ->setId($address->getId())
                        ->setAddress($address->getAddress())
                        ->setCity($address->getCity())
                        ->setCountry($address->getCountry())
                        ->setPostalCode($address->getPostalCode()))
                ->setAddressDelivery((new AddressBean())
                        ->setId($delivAddress->getId())
                        ->setAddress($delivAddress->getAddress())
                        ->setCity($delivAddress->getCity())
                        ->setCountry($delivAddress->getCountry())
                        ->setPostalCode($delivAddress->getPostalCode()))
                ->setCompanyName($company->getTitle())
                ->setCompanyDesc($company->getDescription())
                ->setCompanyTel($company->getTel())
                ->setCompanyFax($company->getFax())
                ->setCompanyEmail($company->getEmail())
                ->setCompanyTvaIntra($company->getTvaIntra())
                ->setChargeWithTax((bool) (int) $company->getChargeWithTax())
                ->setFunction($contact->getFunction())
                ->setUrl($company->getUrl())
                ->setCompanyLogo($company->getIdLogo() ? new ImageBean(Image::getImageFile($company->getIdLogo())) : null);
        
        // Ajout d'informations spécifique au compte courant (siret, etc.) pour le provider
        if ($includeIdentityInfo) {
            if ($contact->getId() !== I::getIdContact()) {
                throw new ArchException('Can not include identity info, contact is not current user');
            }
            
            // Company
            $companyIntro = null;
            if (I::getParam('company', 'capital')) {
                $companyIntro = sprintf(__("%s au capital de %d €"), CompanyTable::STATUS_TITLES_SHORT[$company->getLegalStatus()], I::getParam('company', 'capital'));
            } else if ($company->getLegalStatus() === 'a' && (I::getParam('company', 'rna') || I::getParam('company', 'prefecture'))) {
                $prefType = I::getParam('company', 'sspref') ? __("enregistrée à la sous-préfecture de") : __("enregistrée à la préfecture de");
                $companyIntro = __("Association Loi 1901");
                $companyIntro .= I::getParam('company', 'rna') ? ' ' . __("n°") . I::getParam('company', 'rna') : '';
                $companyIntro .= I::getParam('company', 'prefecture') ? ' ' . $prefType . ' ' . I::getParam('company', 'prefecture') : '';
            }
            $companyRegistration = I::getParam('company', 'rcs') ? __("RCS de ") . I::getParam('company', 'rcs') : null;
            $contactBean
                ->setCompanySiret(I::getParam('company', 'siret'))
                ->setCompanyIntro($companyIntro)
                ->setCompanyTvaIntra(I::getParam('company', 'tva_intra'))
                ->setCompanyRegistration($companyRegistration)
                ->setCompanyApe(I::getParam('company', 'ape'));
            
            // Bank
            $contactBean->setBankDetails((new BankDetailsBean())
                ->setAccountOwnerName(I::getParam('company', 'rib_owner'))
                ->setDomiciliation(I::getParam('company', 'rib_domicil'))
                ->setIban(I::getParam('company', 'rib_iban'))
                ->setBic(I::getParam('company', 'rib_bic'))
            );
            
        }
        
        return $contactBean;
    }
    
    /**
     * Crée et hydrate un bean contact à partir d'un id de contact
     * @param int $contactId
     * @param bool $includeIdentityInfo
     * @param bool $safe
     * @return \Sma\Bean\ContactBean
     */
    public static function buildContactBeanFromContactId(int $contactId = null, bool $includeIdentityInfo = false, bool $safe = true): ContactBean
    {
        $contactId = $contactId ?? I::getIdContact();
        $contact = $safe 
                ? DB::getContactTable()->findSafe($contactId) 
                : DB::getContactTable()->find($contactId);
        if (!$contact) {
            throw new DisplayedException(__("Ce contact n'existe plus. L'opération demandée est impossible."));
        }
        $company = $contact->getIdCompany() 
                ? ($safe 
                    ? DB::getCompanyTable()->findSafe($contact->getIdCompany()) 
                    : DB::getCompanyTable()->find($contact->getIdCompany())) 
                : null;
        $company = $company ?: DB::buildCompanyRow();
        return self::buildContactBean($contact, $company, $includeIdentityInfo);
    }
    
    /**
     * Crée et hydrate un bean contact à partir d'un id de company
     * @param int $idCompany
     * @param bool $includeIdentityInfo
     * @return ContactBean
     */
    public static function buildContactBeanFromCompanyId(int $idCompany = null, bool $includeIdentityInfo = false): ContactBean
    {
        $idCompany = $idCompany ?? I::getIdCompany();
        $company = DB::getCompanyTable()->findSafe($idCompany);
        if (!$company) {
            throw new DisplayedException(__("Le contact est incomplet ou inexistant. Cette opération est impossible."));
        }
        $contact = $company->getRelatedContactRowFromIdContactFk();
        $contact = $contact ?: DB::buildContactRow();
        return self::buildContactBean($contact, $company, $includeIdentityInfo);
    }
    
    /**
     * Crée et hydrate un bean contact à partir d'un hash de company
     * @param string $hash
     * @param bool $includeIdentityInfo
     * @return ContactBean
     */
    public static function buildContactBeanFromCompanyHash(string $hash, bool $includeIdentityInfo = false, bool $returnNullIfNotFound = false): ?ContactBean
    {
        $company = DB::getCompanyTable()->select(['hash' => $hash, 'type' => 'client'])->current();
        if (!$company) {
            if ($returnNullIfNotFound) {
                return null;
            }
            throw new ArchException('Company [' . $hash . '] not found');
        }
        $contact = $company->getRelatedContactRowFromIdContactFk();
        $contact = $contact ?: DB::buildContactRow();
        return self::buildContactBean($contact, $company, $includeIdentityInfo);
    }
    
    /**
     * Warnings liés aux contacts
     * @param bool $firstWarnOnly
     * @param bool $disallowSendWarnings uniquement les alertes qui empêchent un envoi
     * @param bool $withAdvices inclus des conseils non critiques
     * @param bool $html retourner la version HTML
     * @return array|null Texte du warning
     */
    public function getWarnings(bool $firstWarnOnly = false, bool $disallowSendWarnings = false, bool $withAdvices = false, bool $html = false): ?array
    {
        $warns = [];
        
        // Pas d'email
        if (!$disallowSendWarnings && !$this->getEmail()) {
            $warns[] = $this->newWarn(__("Spécifiez un email (nécessaire pour les envois)"), 'warning', AVH::STATUS_ERROR, 
                    $html, 'recipient', 'edit', ['id' => $this->getIdCompany(), 'f' => 'email']);
            if ($firstWarnOnly) { return $warns[0]; }
        }
        
        // Adresse incomplète
        if (!$this->getAddress()->isFull()) {
            $warns[] = $this->newWarn(__("Complétez l'adresse postale (obligatoire pour les factures)"), 'warning', null, 
                    $html, 'recipient', 'edit', ['id' => $this->getIdCompany(), 'f' => 'a~' . $this->getAddress()->getNotFullField()]);
            if ($firstWarnOnly) { return $warns[0]; }
        }
        
        // TVA intra obligatoire
        if (!$this->getChargeWithTax() && !$this->getCompanyTvaIntra()) {
            $warns[] = $this->newWarn(__("Si vous facturez hors taxe, la tva intracommunautaire du client est obligatoire"), 'warning',
                    $html, 'recipient', 'edit', ['id' => $this->getIdCompany(), 'f' => 'tvaIntra']);
            if ($firstWarnOnly) { return $warns[0]; }
        }
        
        // Pas de contact pour une entreprise
        if ($withAdvices && !$this->getComputedFullname()) {
            $warns[] = $this->newWarn(__("Vous devriez spécifier un contact pour cette entreprise"), 'info', AVH::STATUS_INFO,
                    $html, 'recipient', 'edit', ['id' => $this->getIdCompany(), 'f' => 'firstname']);
            if ($firstWarnOnly) { return $warns[0]; }
        }
        
        // Conseil : mettre la tva intra
        if ($withAdvices && !in_array(Text::toUpper($this->getAddress()->getCountry()), ['', 'FRANCE', 'FR']) && !$this->getCompanyTvaIntra()) {
            $warns[] = $this->newWarn(__("Client étranger : spécifiez la TVA Intracommunautaire et vérifiez s'il faut facturer HT."), 'info', AVH::STATUS_INFO, 
                    $html, 'recipient', 'edit', ['id' => $this->getIdCompany(), 'f' => 'tvaIntra']);
            if ($firstWarnOnly) { return $warns[0]; }
        }
        
        return $warns;
    }
    
    // IMPORT / EXPORT
    
    /**
     * @return array
     */
    public function exportToArray(): array
    {
        return [
            'email' => $this->getEmail(),
            'company_name' => $this->getCompanyName(),
            'tel' => $this->getCompanyTel(),
            'fax' => $this->getCompanyFax(),
            'url' => $this->getUrl(),
            'contact_civility' => $this->getCivility(),
            'contact_firstname' => $this->getFirstname(),
            'contact_lastname' => $this->getLastname(),
            'contact_gsm' => $this->getGsm(),
            'bill_address' => $this->getAddress()->getAddress(),
            'bill_postal_code' => $this->getAddress()->getPostalCode(),
            'bill_city' => $this->getAddress()->getCity(),
            'bill_country' => $this->getAddress()->getCountry(),
            'deli_address' => $this->getAddressDelivery()->getAddress(),
            'deli_postal_code' => $this->getAddressDelivery()->getPostalCode(),
            'deli_city' => $this->getAddressDelivery()->getCity(),
            'deli_country' => $this->getAddressDelivery()->getCountry(),
            'charge_with_tax' => $this->getChargeWithTax() ? __("oui") : __("non"),
            'tva_intra' => $this->getCompanyTvaIntra(),
            'keywords' => $this->getCompanyDesc(),
            'id' => $this->getId() // @task remplacer par UID
        ];
    }
    
    /**
     * @return array
     */
    public static function exportLegend(): array
    {
        return [
            'email' => __("Email"),
            'company_name' => __("Nom société"),
            'tel' => __("Tel"),
            'fax' => __("Fax"),
            'url' => __("Url"),
            'contact_civility' => __("Civilité contact"),
            'contact_firstname' => __("Prénom contact"),
            'contact_lastname' => __("Nom contact"),
            'contact_gsm' => __("Gsm"),
            'bill_address' => __("Facturation: rue"),
            'bill_postal_code' => __("Facturation: code postal"),
            'bill_city' => __("Facturation: ville"),
            'bill_country' => __("Facturation: pays"),
            'deli_address' => __("Livraison: rue"),
            'deli_postal_code' => __("Livraison: code postal"),
            'deli_city' => __("Livraison: ville"),
            'deli_country' => __("Livraison: pays"),
            'charge_with_tax' => __("Facturer avec TVA ?"),
            'tva_intra' => __("TVA Intracommunautaire"),
            'keywords' => __("Mots clés"),
            'id' => __("Identifiant"),
        ];
    }
}