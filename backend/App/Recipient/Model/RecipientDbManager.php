<?php
namespace App\Recipient\Model;

use Osf\Exception\DisplayedException;
use Osf\Form\Element\ElementSelect;
use Osf\Validator\Validator as V;
use Osf\Filter\Filter as F;
use Osf\Stream\Text as T;
use Osf\Helper\Mysql;
use Osf\Helper\Tab;
use Sma\Form\AbstractAutocompleteAdapter as AAA;
use Sma\Bean\InvoiceBean as IB;
use Sma\Session\Identity as I;
use Sma\Db\DbRegistry as DBR;
use Sma\Bean\ContactBean;
use Sma\Container as C;
use Sma\Db\CompanyRow;
use Sma\Log;
use App\Guest\Controller as GuestController;
use DB;

/**
 * Recipient db management
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package app
 * @subpackage recipient
 */
class RecipientDbManager extends AAA
{
    const CATEGORY = 'recipient'; // Pour les items de recherche / autocomplétion
    const SEARCH_FIELDS = ['id', 'code', 'title', 'price', 'price_type', 'tax', 'unit', 'discount'];
    
    protected $autocompleteLimit = 10;
    
    public function __construct(?int $autocompleteLimit = null)
    {
        if ($autocompleteLimit !== null) {
            $this->autocompleteLimit = $autocompleteLimit;
        }
    }
    
    public static function addContact(array $values)
    {
        Log::info("Ajout d'un contact", 'DB', $values);
        $idAccount = I::getIdAccount();
        self::filterValues($values);
        
        try {

            DB::getCompanyTable()->beginTransaction();
            
            // Company address
            $data = isset($values['a']) ? $values['a'] : [];
            $idAddress = null;
            if (!Tab::allValuesEmpty($data)) {
                $data['id_account'] = $idAccount;
                DB::getAddressTable()->insert($data);
                $idAddress = DB::getAddressTable()->lastInsertValue;
            }

            // Company contact
            $data = isset($values['u']) ? $values['u']: [];
            $idContact = null;
            $data['id_account'] = $idAccount;
            $data = array_merge($data, Tab::reduce($values['c'], ['email', 'tel', 'fax']));
            DB::getContactTable()->insert($data);
            $idContact = DB::getContactTable()->lastInsertValue;
            $data['id_account'] = $idAccount;

            // Company
            $data = $values['c'];
            $data['id_account'] = $idAccount;
            $data['type'] = isset($data['type']) && $data['type'] ? $data['type'] : 'client';
            $data['title'] = $data['title'] ?? '';
            
            $data['id_company'] = I::getIdCompany();
            if ($idAddress) {
                $data['id_address'] = $idAddress;
            }
            if ($idContact) {
                $data['id_contact'] = $idContact;
            }
            $data['uid'] = DB::getSequenceTable()->nextValue('company');
            $data['hash'] = DB::getCompanyTable()->generateHash();
            DB::getCompanyTable()->insert($data);
            $idCompany = DB::getCompanyTable()->lastInsertValue;

            // Mise à jour du contact
            if ($idContact) {
                DB::getContactTable()->find($idContact)->setIdCompany($idCompany)->save();
            }
            
            DB::getCompanyTable()->commit();
            
            // Construction et mise à jour du bean
            $bean = ContactBean::buildContactBeanFromContactId($idContact);
            
            // Enregistrement du bean
            DB::getContactTable()->find($idContact)->setBean($bean)->save();
            self::updateSearchIndex($idCompany);
            return $idCompany;
            
        } catch (Exception $e) {
            DB::getCompanyTable()->rollback();
            Log::error('Recipient insertion error: ' . $e->getMessage(), 'DB', $e);
        }
        return false;
    }
    
    /**
     * @param array $values
     * @param int $idCompany
     * @param int $updateByEndUser
     * @return CompanyRow|null
     */
    public static function updateContact(array $values, int $idCompany, int $updateByEndUser = null): ?CompanyRow
    {
        self::filterValues($values);
        Log::info("Mise à jour d'un contact", 'DB', $values);
        
        // Récupération de la société à modifier
        $company = DB::getCompanyTable()->find($idCompany);
        if (!$company) {
            Log::error('Contact introuvable', 'DB', [$values, $idCompany, $company]);
            return null;
        }
        
        // Vérifications de sécurité
        $idAccount = $updateByEndUser ? $company->getIdAccount() : I::getIdAccount();
        if (!$company || $idAccount !== $company->getIdAccount()) {
            Log::error("Tentative d'update interdite", 'DB', [$values, $idCompany, $company]);
            return null;
        }
        if ($updateByEndUser && $company->getId() !== $updateByEndUser) {
            Log::error("Tentative d'update interdite, mauvais end user", 'DB', [$values, $idCompany, $company, $updateByEndUser]);
            return null;
        }
        
        try {

            DB::getCompanyTable()->beginTransaction();
            
            // Adresse de facturation
            $data = $values['a'];
            if ($company->getIdAddress()) {
                DB::getAddressTable()->find($company->getIdAddress())
                                     ->setValues($data, true)
                                     ->save();
            } else if (!Tab::allValuesEmpty($data)) {
                $data['id_account'] = $idAccount;
                DB::getAddressTable()->insert($data);
                $company->setIdAddress(DB::getAddressTable()->lastInsertValue);
            }
            
            // Adresse de livraison
            $data = $values['d'];
            if ($company->getIdAddressDelivery()) {
                DB::getAddressTable()->find($company->getIdAddressDelivery())
                                     ->setValues($data, true)
                                     ->save();
            } else if (!Tab::allValuesEmpty($data)) {
                $data['id_account'] = $idAccount;
                DB::getAddressTable()->insert($data);
                $company->setIdAddressDelivery(DB::getAddressTable()->lastInsertValue);
            }

            // Company contact
            $data = $values['u'];
            $data = array_merge($data, Tab::reduce($values['c'], ['email', 'tel', 'fax']));
            if ($company->getIdContact()) {
                DB::getContactTable()->find($company->getIdContact())
                                     ->setValues($data, true)
                                     ->save();
            } else if (!Tab::allValuesEmpty($data)) {
                $data['id_account'] = $idAccount;
                DB::getContactTable()->insert($data);
                $company->setIdContact(DB::getContactTable()->lastInsertValue);
            }

            // Company
            $data = $values['c'];
            $company->setValues($data)->save();
            
            // Mise à jour du contact
            if ($company->getIdContact()) {
                DB::getContactTable()
                        ->find($company->getIdContact())
                        ->setIdCompany($company->getId())
                        ->save();
            }
            
            DB::getCompanyTable()->commit();
            
            // Reconstruction du bean
            $safe = !GuestController::isLogged();
            $bean = ContactBean::buildContactBeanFromContactId($company->getIdContact(), false, $safe);
            DB::getContactTable()->find($company->getIdContact())->setBean($bean)->save();

            self::updateSearchIndex($idCompany, true, $company->getIdAccount());
            return $company;
            
        } catch (Exception $e) {
            DB::getCompanyTable()->rollback();
            Log::error('Recipient update error: ' . $e->getMessage(), 'DB', $e);
        }
        
        return null;
    }
    
    /**
     * Filtrage des valeurs avant envoi dans la base
     * @param array $values
     * @return void
     */
    protected static function filterValues(array &$values): void
    {
        $values['c'] = isset($values['c']) ? $values['c'] : [];
        if (isset($values['b']['ht'])) {
            $values['c']['charge_with_tax'] = (int) !$values['b']['ht'];
        }
    }
    
    /**
     * @return \Zend\Db\Adapter\Driver\ResultInterface
     */
    public static function getContactsForTable(array $settings, array $columns = null)
    {
        $sorts = [
            'na'  => 'company.title ASC', 
            'nd'  => 'company.title DESC', 
            'ca'  => 'contact.lastname ASC, contact.firstname ASC', 
            'cd'  => 'contact.lastname DESC, contact.firstname DESC', 
            'ea'  => 'company.email ASC', 
            'ed'  => 'company.email DESC', 
            'dca' => 'company.date_insert ASC',
            'dcd' => 'company.date_insert DESC',
            'dua' => 'company.date_update ASC',
            'dud' => 'company.date_update DESC',
        ];
        
        $sortBy = isset($settings['s']) && isset($sorts[$settings['s']]) ? $sorts[$settings['s']] : 'company.id DESC';
        
        if (is_array($columns)) {
            $cols = implode(', ', $columns);
        } else {
            $cols  = 'company.id as id, company.url as url, company.title as title, ';
            $cols .= 'contact.firstname as firstname, contact.lastname as lastname, ';
            $cols .= 'company.email as email, address.address as address, ';
            $cols .= 'address.postal_code as postal_code, address.city as city, ';
            $cols .= 'address.country as country, company.tel as tel, ';
            $cols .= 'contact.gsm as gsm, contact.bean as bean ';
        }
        
        $params = [];
        $sql  = 'SELECT ' . $cols . ' ';
        $sql .= 'FROM company ';
        $sql .= 'LEFT JOIN address ON company.id_address = address.id ';
        $sql .= 'LEFT JOIN contact ON company.id_contact = contact.id ';
        $sql .= 'WHERE company.id_account=' . (int) I::getIdAccount() . ' ';
        $sql .= '  AND company.type="client" ';
        if (isset($settings['q']) && $settings['q'] !== '') {
            $sql .= 'AND (company.title LIKE ? ';
            $sql .= 'OR company.description LIKE ? ';
            $sql .= 'OR company.email LIKE ? ';
            $sql .= 'OR contact.firstname LIKE ? ';
            $sql .= 'OR contact.lastname LIKE ? ';
            $sql .= 'OR address.city LIKE ?) ';
            $like = Mysql::like($settings['q']);
            for ($i = 0; $i < 6; $i++) {
                $params[] = $like;
            }
        }
        if (isset($settings['f']) && $settings['f']) {
            $sql .= 'AND company.date_update >= ? ';
            $params[] = Mysql::dateToMysql($settings['f']);
        }
        if (isset($settings['t']) && $settings['t']) {
            $sql .= 'AND company.date_update <= ? ';
            $params[] = Mysql::dateToMysql($settings['t']) . ' 23:99:99';
        }
        $sql .= 'ORDER BY ' . $sortBy;
        return DB::getCompanyTable()->prepare($sql)->execute($params);
    }
    
    /**
     * Tableau du contact pour le formulaire d'édition
     * @param int $idCompany
     * @return array
     */
    public static function getContactForForm(int $idCompany, bool $safe = true): array
    {
        /* @var $company \Sma\Db\CompanyRow */
        /* @var $contact \Sma\Db\ContactRow */
        $company = $safe ? DB::getCompanyTable()->findSafe($idCompany) : DB::getCompanyTable()->find($idCompany);
        $contact = $company->getIdContact() 
                 ? $safe ? DB::getContactTable()->findSafe($company->getIdContact()) : DB::getContactTable()->find($company->getIdContact()) 
                 : DB::buildContactRow();
        $address = $company->getIdAddress()
                 ? DB::getAddressTable()->find($company->getIdAddress())
                 : DB::buildAddressRow();
        $addDeli = $company->getIdAddressDelivery()
                 ? DB::getAddressTable()->find($company->getIdAddressDelivery())
                 : DB::buildAddressRow();
        
        return [
            'u' => [
                'civility'    => $contact->getCivility(),
                'firstname'   => $contact->getFirstname(),
                'lastname'    => $contact->getLastname(),
                'gsm'         => $contact->getGsm()
            ],
            'c' => [
                'title'       => $company->getTitle(),
                'email'       => $company->getEmail(),
                'tel'         => $company->getTel(),
                'fax'         => $company->getFax(),
                'url'         => $company->getUrl(),
                'tva_intra'   => $company->getTvaIntra(),
                'description' => $company->getDescription()
            ],
            'a' => [
                'address'     => $address->getAddress(),
                'postal_code' => $address->getPostalCode(),
                'city'        => $address->getCity(),
                'country'     => $address->getCountry()
            ],
            'd' => [
                'address'     => $addDeli->getAddress(),
                'postal_code' => $addDeli->getPostalCode(),
                'city'        => $addDeli->getCity(),
                'country'     => $addDeli->getCountry()
            ],
            'b' => [
                'ht'          => !$company->getChargeWithTax()
            ]
        ];
    }
    
    public static function getContactForSearch(int $idCompany)
    {
        $company = DB::getCompanyTable()->find($idCompany);
        $contact = $company->getIdContact() 
                 ? DB::getContactTable()->find($company->getIdContact())
                 : DB::buildContactRow();
        $address = $company->getIdAddress()
                 ? DB::getAddressTable()->find($company->getIdAddress())
                 : DB::buildAddressRow();
        
        // Données à indexer pour la recherche
        $searchData = [
            $company->getUid() . '.',
            $contact->getFirstname(),
            $contact->getLastname(),
            $company->getTitle(),
            $company->getEmail(),
            $company->getDescription(),
            $address->getCity()
        ];
        
        // Recherche d'un téléphone
        $tel = $company->getTel() ? 'tel. ' . T::phoneFormat($company->getTel()): null;
        $tel = $tel ?: ($contact->getGsm() ? 'mob. ' . T::phoneFormat($contact->getGsm()): null);
        $tel = $tel ?: ($company->getFax() ? 'fax. ' . T::phoneFormat($company->getFax()): '');
        
        // Valeurs pour l'affichage
        $sep = $company->getTitle() && ($contact->getFirstname() || $contact->getLastname()) ? ' - ' : ' ';
        $values = [
            'id' => $company->getId(),
            'uid' => $company->getUid(),
            'title' => T::crop(trim($company->getTitle() . $sep . trim(T::ucPhrase($contact->getFirstname()) . ' ' . T::toUpper($contact->getLastname()))), 255, ''),
            'tel' => $tel
        ];
        
        return [implode(' ', array_filter($searchData)), $values];
    }
    
    /**
     * Suppression complète d'un destinataire (contact + company + adresses)
     * @param int $idCompany
     * @return void
     * @throws DisplayedException
     */
    public static function deleteRecipient(int $idCompany): void
    {
        $idContact = DB::getCompanyTable()->find($idCompany)->getIdContact();
        $invoiceCount = DB::getDocumentHistoryCurrentTable()->buildSelect(['type' => IB::TYPE_INVOICE, 'id_recipient' => $idContact])->execute()->count();
        if ($invoiceCount) {
            throw new DisplayedException(sprintf(__("Vous ne pouvez pas supprimer ce contact car il est lié à %d facture%s. Les conditions d'inaltérabilité nous obligent à maintenir ces informations. Les destinataires de vos factures doivent avoir accès à leur espace afin de consulter les documents originaux."), $invoiceCount, $invoiceCount > 1 ? 's' : ''));
        }
        DBR::deleteCompany($idCompany, true, true);
        C::getSearch()->cleanAutocomplete(self::CATEGORY, $idCompany);
    }
    
    /**
     * Mise à jour d'un produit dans les données de recherche
     * @param int $id
     * @param bool $cleanItem
     * @param type $idAccount
     * @return void
     */
    protected static function updateSearchIndex(int $id, bool $cleanItem = true, $idAccount = null): void
    {
        list ($searchData, $values) = self::getContactForSearch($id);
        if ($cleanItem) {
            C::getSearch()->cleanAutocomplete(self::CATEGORY, $id, $idAccount);
        }
        $url = C::getRouter()->buildUri(['id' => $id], 'recipient', 'view');
        C::getSearch()->indexAutocompleteItem($searchData, $values['title'], $values, self::CATEGORY, $id, $url, 10, $idAccount);
    }
    
    /**
     * Update products in search table
     * @param int $idAccount
     * @return void
     */
    public static function updateAllRecipientsForSearchEngine(int $idAccount = null): void
    {
        $idAccount = $idAccount ?? I::getIdAccount();
        C::getSearch()->clean(self::CATEGORY, $idAccount);
        $sql = 'SELECT id FROM company WHERE id_account=? AND type != \'mine\'';
        $items = DB::getCompanyTable()->prepare($sql)->execute([$idAccount]);
        foreach ($items as $item) {
            self::updateSearchIndex($item['id'], false, $idAccount);
        }
    }
    
    /**
     * Attache à l'élément select une autocomplétion sur les contacts
     * @param ElementSelect $elt
     * @param int $limit Nombre d'éléments initiaux
     * @return ElementSelect
     */
    public function registerAutocomplete(ElementSelect $elt = null, ?int $limit = null): ElementSelect
    {
        // Récupération des données liées à la valeur de l'élément et création de l'option
        $items = $this->registerAutocompleteOptions($elt, self::CATEGORY);
        
        // Liste d'items proposé par défaut, auquel on ajout les items courants s'ils 
        // existent et ne sont pas dans la liste des items proposés (à optimiser)
        $initialItems = $this->appendInitialOptions($limit ?? $this->autocompleteLimit, $items, self::CATEGORY);
        
        // Enregistrement de l'autocomplétion
        $template = "'<div>' + '<strong>' + escape(item.title) + '</strong> '"
                . " + '<span class=\"pull-right\">' + escape(item.tel) + '</span>'"
                . " + '</div>'";
        $url = C::getViewHelper()->url('event', 'ac') . '/' . self::CATEGORY . '/';
        $elt = $elt ?: new ElementSelect(self::CATEGORY);
        $elt->setAutocomplete($url, $template, $initialItems);
        
        // Placeholder en fonction du contexte
        if (!$elt->getPlaceholder()) {
            if ($elt->isMultiple()) {
                $elt->setPlaceholder(__("Choisir un ou plusieurs contacts"));
            } else {
                $elt->setPlaceholder(__("Choisir un contact"));
            }
        }
        
        return $elt;
    }
    
    /**
     * Décompose la chaîne en tableau destiné à ajouter un contact
     * @param string $contactString
     * @return array
     */
    public static function parseString($contactString)
    {
        $civilities = [
            'm' => 'M.',
            'g' => 'M.',
            'f' => 'Mme',
            'mme' => 'Mme',
            '' => ''
        ];
        
        $tab = [];
        $values = explode(';', trim($contactString));
        
        // Décompositions
        foreach ($values as $value) {
            $value = trim($value);
            $matches = [];
            switch (true) {
                case V::getEmailAddress()->isValid($value) : 
                    $tab['c']['email'] = F::getStringToLower()->filter($value);
                    break;
                case preg_match('/^(.{2,250}) ([0-9]{4,10}) ([^,]{2,80}) *(,.+)?$/i', $value, $matches) : 
                    $tab['a']['address'] = preg_replace("/ *# */", "\n", $matches[1]);
                    $tab['a']['postal_code'] = $matches[2];
                    $tab['a']['city'] = T::ucPhrase($matches[3]);
                    $country = isset($matches[4]) ? T::ucPhrase($matches[4]) : null;
                    if ($country) {
                        $tab['a']['country'] = trim($country, ' ,');
                    }
                    break;
                case preg_match('/^(m\.? |g |f |mme )?([^,]+),([^,]+)$/i', $value, $matches) : 
                    $civility = $civilities[trim(T::toLower($matches[1]))];
                    if ($civility) {
                        $tab['u']['civility'] = $civility;
                    }
                    $tab['u']['firstname'] = T::ucPhrase($matches[2]);
                    $tab['u']['lastname']  = T::ucPhrase($matches[3]);
                    break;
                case preg_match('/^\+?[0-9]{8,12}$/', str_replace(' ', '', $value)) :
                    $tel = str_replace(' ', '', $value);
                    if (substr($tel, 0, 2) === '06' && strlen($tel) === 10) {
                        $tab['u']['gsm'] = $tel;
                    } else {
                        $tab['c']['tel'] = $tel;
                    }
                    break;  
                default : 
                    $tab['c']['title'] = trim($value);
                    break;
            }
        }
        
        // Détermine un titre si celui-ci n'existe pas
        if (!isset($tab['u']['firstname']) && !isset($tab['u']['lastname']) && !isset($tab['c']['title'])) {
            if (isset($tab['c']['email'])) {
                $tab['c']['title'] = $tab['c']['email'];
            } else {
                $tab = [];
            }
        }
        
        return $tab;
    }
    
    /**
     * Construit un titre à partir des valeurs à ajouter (pour la liste déroulante)
     * @param array $valuesToAdd
     * @return string
     */
    public static function getTitle(array $valuesToAdd)
    {
        $title = '';
        if (isset($valuesToAdd['c']['title'])) {
            $title .= $valuesToAdd['c']['title'];
        }
        if ((isset($valuesToAdd['u']['firstname']) && $valuesToAdd['u']['firstname']) || 
            (isset($valuesToAdd['u']['lastname'])  && $valuesToAdd['u']['lastname'])) {
            $title .= $title ? ' - ' : '';
            $title .= $valuesToAdd['u']['firstname'] . ' ' . $valuesToAdd['u']['lastname'];
        }
        return trim($title);
    }
    
    /**
     * Mise à jour temporaire des contact beans
     * @return void
     */
    public static function fixContactBeans(): void
    {
        // Companies sans contact
        $sql = 'SELECT id, id_account FROM company WHERE id_contact IS NULL';
        $rows = DB::getCompanyTable()->prepare($sql)->execute();
        foreach ($rows as $row) {
            DB::getContactTable()->insert(['id_company' => $row['id'], 'firstname' => '', 'lastname' => '', 'id_account' => $row['id_account']]);
            $idContact = DB::getContactTable()->lastInsertValue;
            DB::getCompanyTable()->find($row['id'])->setIdContact($idContact)->save();
        }
        
        // Construction des beans inexistants dans les contacts
        $sql = 'SELECT id FROM contact WHERE bean IS NULL';
        $rows = DB::getContactTable()->prepare($sql)->execute();
        foreach ($rows as $row) {
            $bean = ContactBean::buildContactBeanFromContactId($row['id'], false, false);
            DB::getContactTable()->find($row['id'])->setBean($bean)->save();
        }
        
        // Extraction des tva_intra et charge_with_tax depuis les beans pour mise à jour dans la base
        $sql = 'SELECT id, bean FROM contact WHERE bean IS NOT NULL';
        $rows = DB::getContactTable()->select();
        
        /* @var $row \Sma\Db\ContactRow */
        foreach ($rows as $row) {
            $bean = $row->getBean();
            if ($bean instanceof ContactBean && method_exists($bean, 'getTvaIntra') && $bean->getTvaIntra()) {
                $bean->setCompanyTvaIntra($bean->getTvaIntra());
                DB::getCompanyTable()->find($row->getIdCompany())
                        ->setTvaIntra($bean->getTvaIntra())
                        ->setChargeWithTax((int) $bean->getChargeWithTax())
                        ->save();
                $row->setBean($bean)->save();
            }
            if ($bean instanceof ContactBean && method_exists($bean, 'getCompanyTvaIntra') && $bean->getCompanyTvaIntra()) {
                DB::getCompanyTable()->find($row->getIdCompany())->setTvaIntra($bean->getTvaIntra())->save();
            }
        }
    }
}
