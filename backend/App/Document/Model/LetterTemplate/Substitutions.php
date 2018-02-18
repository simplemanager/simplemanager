<?php
namespace App\Document\Model\LetterTemplate;

use Osf\Exception\DisplayedException;
use Osf\Exception\ArchException;
use Osf\Bean\AbstractBean;
use Osf\Stream\Text as T;
use Sma\Bean\Example\InvoiceBeanExample;
use Sma\Bean\Example\ContactBeanExample;
use Sma\Db\DocumentHistoryRow;
use Sma\Session\Identity as I;
use Sma\Bean\ContactBean;
use Sma\Bean\InvoiceBean;
use Sma\Db\CompanyRow;
use DB, H;

/**
 * Données de substitution
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package app
 * @subpackage document
 */
class Substitutions
{
    /**
     * Retourne les valeurs de substitution correspondant au type de bean
     * @param AbstractBean $bean
     * @param string $rootKey
     * @return array
     * @throws ArchException
     */
    public static function getValues(AbstractBean $bean, $rootKey = null)
    {
        switch (true) {
            case $bean instanceof ContactBean : 
                $rootKey = $rootKey ?? 'dest';
                $values = self::getContactArray($bean, true);
                break;
            case $bean instanceof InvoiceBean : 
                $values = self::getInvoiceArray($bean);
                break;
            default: 
                throw new ArchException('Unknown bean type for substitution');
        }
        $vals = $rootKey === null ? $values : [(string) $rootKey => $values];
        self::addIdentity($vals);
        return $vals;
    }
    
    /**
     * Valeurs de substitution pour un contact
     * @param ContactBean $contact
     * @return array
     */
    protected static function getContactArray(ContactBean $contact, bool $withUrl)
    {
        $isExample = $contact instanceof ContactBeanExample;
        $companyRow = $withUrl && !$isExample ? DB::getCompanyTable()->findSafe($contact->getIdCompany()) : false;
        if ($withUrl && !$companyRow && !$isExample) {
            throw new DisplayedException(sprintf(__("Le destinataire %s lié à ce document n'existe plus."), $contact->getComputedTitle()));
        }
        return [
            'civ' => $contact->getCivility(false),
            'civilite' => $contact->getCivility(true),
            'prenom' => $contact->getFirstname(),
            'nom' => $contact->getLastname(),
            'prenom_nom' => $contact->getComputedFullname(false),
            'nom_complet' => $contact->getComputedFullname(true),
            'cher' => $contact->getComputedCivilityWithLastname(),
            'adresse' => str_replace("\n", "  \n", $contact->getAddress()->getComputedAddress()),
            'titre' => $contact->getComputedTitle(true),
            'rue' => $contact->getAddress()->getAddress(false),
            'code_postal' => $contact->getAddress()->getPostalCode(),
            'ville' => $contact->getAddress()->getCity(),
            'pays' => $contact->getAddress()->getCountry(),
            'email' => $contact->getComputedEmail(),
            'tel' => T::phoneFormat($contact->getComputedTel()),
            'fax' => T::phoneFormat($contact->getComputedFax()),
            'gsm' => T::phoneFormat($contact->getGsm()),
            'function' => $contact->getFunction(),
            'nom_societe' => $contact->getCompanyName(),
            'email_societe' => $contact->getCompanyEmail(),
            'url' => $contact->getUrl(),
            'login_url' => ($companyRow instanceof CompanyRow 
                    ? $companyRow->buildLoginUrl()
                    : '#')
        ];
    }
    
    /**
     * Valeurs de substitution pour une facture et ses dérivés
     * @param InvoiceBean $invoice
     * @return array
     */
    protected static function getInvoiceArray(InvoiceBean $invoice)
    {
        $isExample = $invoice instanceof InvoiceBeanExample;
        $docHistoryRow = $isExample ? null : DB::getDocumentHistoryTable()->findSafe($invoice->getIdDocumentHistory());
        return [
            'exp' => self::getContactArray($invoice->getProvider(), false),
            'dest' => self::getContactArray($invoice->getRecipient(), true),
            'doc' => [
                'code' => $invoice->getCode(),
                'titre' => $invoice->getTitle(),
                'description' => $invoice->getDescription(),
                'date_emis' => T::formatDate($invoice->getDateSending()),
                'date_emission' => T::formatDateLong($invoice->getDateSending()),
                //'dateheure_emission' => T::formatDateTime($invoice->getDateSending()),
                'date_val' => T::formatDate($invoice->getDateValidity()),
                'date_validite' => T::formatDateLong($invoice->getDateValidity()),
                //'dateheure_validite' => T::formatDateTime($invoice->getDateValidity()),
                'introduction' => $invoice->getMdBefore(),
                'conclusion' => $invoice->getMdAfter(),
                'lieu' => $invoice->getPlace(),
                'code_etat' => $invoice->getStatus(),
                'etat' => $invoice->getStatusName(),
                'sujet' => $invoice->getSubject(),
                'franchise_tva' => (int) $invoice->getTaxFranchise(),
                'code_type' => $invoice->getType(),
                'type' => $invoice->getTypeName(),
                'le_type' => $invoice->getTypeName(false, ['invoice' => 'la', 'order' => 'la', 'quote' => 'le']),
                'un_type' => $invoice->getTypeName(false, ['invoice' => 'une', 'order' => 'une', 'quote' => 'un']),
                'total_ht' => T::currencyFormat($invoice->getTotalHtWithDiscount()),
                'remise_ht' => T::currencyFormat($invoice->getTotalHtWithoutDiscount() - $invoice->getTotalHtWithDiscount()),
                'total_ttc' => T::currencyFormat($invoice->getTotalTtcWithDiscount()),
                'remise_ttc' => T::currencyFormat($invoice->getTotalTtcWithoutDiscount() - $invoice->getTotalTtcWithDiscount()),
                // 'produits' => $invoice->getProducts(),
                'url' => ($docHistoryRow instanceof DocumentHistoryRow 
                    ? $docHistoryRow->buildUrlWithHash($invoice->getRecipientCompanyHash())
                    : '#'),
            ],
        ];
    }
    
    /**
     * Ajout des informations sur mon identité
     * @param array $vals
     */
    protected static function addIdentity(array &$vals)
    {
        // $bean = DB::getContactTable()->getBean(I::getIdContact()); // @task remplacer par cela à terme... 
        $bean = ContactBean::buildContactBeanFromContactId(I::getIdContact(), true);
        if (!isset($vals['exp'])) {
            $vals['exp'] = self::getContactArray($bean, false);
        }
        $vals['exp']['titre'] = $bean->getCompanyName() ?: $bean->getComputedFullname();
        $vals['exp']['intro'] = $bean->getCompanyIntro();
        $vals['exp']['slogan'] = $bean->getCompanyDesc();
        $vals['exp']['rcs'] = $bean->getCompanyRegistration();
        $vals['exp']['siret'] = T::formatSiret($bean->getCompanySiret());
        $vals['exp']['siren'] = T::siretToSiren($bean->getCompanySiret(), true);
        $vals['exp']['ape'] = $bean->getCompanyApe();
        $vals['exp']['tva_intra'] = $bean->getCompanyTvaIntra();
        $vals['exp']['rna'] = I::getParam('company', 'rna');
        $vals['exp']['date_creation'] = I::getParam('company', 'creation');
        $vals['exp']['delai_paiement'] = (int) I::getParam('invoice', 'delay');
        $vals['exp']['facture_objet'] = I::getParam('invoice', 'object');
        $vals['exp']['facture_intro'] = I::getParam('invoice', 'intro');
        $vals['exp']['facture_conclu'] = I::getParam('invoice', 'conclu');
        $vals['exp']['url'] = $bean->getUrl();
    }
}
