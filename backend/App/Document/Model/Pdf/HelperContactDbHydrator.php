<?php
namespace App\Document\Model\Pdf;

use Osf\Image\ImageInfo;
use Osf\Exception\ArchException;
use Osf\Pdf\Document\Bean\AddressBean;
use Osf\Pdf\Document\Bean\ContactBean;
use Osf\Pdf\Document\Bean\ImageBean;
use Sma\Db\ContactRow;
use Sma\Db\CompanyRow;
use Sma\Db\AddressRow;
use Sma\Db\ImageRow;
use Sma\Db\DbContainer;

/**
 * Hydrateur de contact à partir des données de la base
 * 
 * Utilisé pour créer le bean à insérer dans la base
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 12 nov. 2013
 * @package company
 * @subpackage pdf
 */
class HelperContactDbHydrator
{
    const PRIORITY_COMPANY = 'company';
    const PRIORITY_CONTACT = 'contact';
    
    /**
     * @var \Sma\Db\ContactRow
     */
    protected $contact;
    
    /**
     * @var \Sma\Db\CompanyRow
     */
    protected $company;
    
    protected $loadCompanyLogo = false;
    
    protected $priority = null;

    /**
     * @param \Sma\Db\CompanyRow $company
     * @return \App\Document\Model\Pdf\HelperContactDbHydrator
     */
    public function setCompany(CompanyRow $company)
    {
        $this->company = $company;
        if ($this->priority === null) {
            $this->priority = self::PRIORITY_COMPANY;
        }
        return $this;
    }
    
    /**
     * @param \Sma\Db\ContactRow $contact
     * @return \App\Document\Model\Pdf\HelperContactDbHydrator
     */
    public function setContact(ContactRow $contact) {
        $this->contact = $contact;
        if ($this->priority === null) {
            $this->priority = self::PRIORITY_CONTACT;
        }
        return $this;
    }

    /**
     * @param ContactBean $bean
     * @param AddressRow $addressRow
     * @param string $title
     * @return \App\Document\Model\Pdf\HelperContactDbHydrator
     */
    protected function setAddress(ContactBean $bean, AddressRow $addressRow, $title)
    {
        $addressHydrator = new HelperAddressDbHydrator();
        $addressHydrator->setAddress($addressRow);
        $addressHydrator->setTitle($title);
        $bean->setAddress($addressHydrator->hydrate(new AddressBean()));
        return $this;
    }
    
    /**
     * Set priority (see PRIORITY_*)
     * @param string $priority
     * @throws ArchException
     * @return \App\Document\Model\Pdf\HelperContactDbHydrator
     */
    public function setPriority($priority)
    {
        if (!in_array($priority, array(self::PRIORITY_COMPANY, self::PRIORITY_CONTACT))) {
            throw new ArchException('Bad priority');
        }
        $this->priority = $priority;
        return $this;
    }
    
    /**
     * @param bool $loadCompanyLogo
     * @return \App\Document\Model\Pdf\HelperContactDbHydrator
     */
    public function setLoadCompanyLogo($loadCompanyLogo) {
        $this->loadCompanyLogo = (bool) $loadCompanyLogo;
        return $this;
    }
    
    public function hydrate(ContactBean $bean)
    {
        // Vérifications
        if (!$this->contact && !$this->company) {
            throw new ArchException('Contact from db is required to hydrate');
        }
        
        switch ($this->priority) {
            
            // Hydratation de l'entreprise
            case self::PRIORITY_COMPANY : 
                $bean
                    ->setCompanyName($this->company->getTitle())
                    ->setCompanyDesc($this->company->getDescription())
                    ->setTel($this->company->getTel())
                    ->setFax($this->company->getFax())
                    ->setEmail($this->company->getEmail());
                $addressRow = $this->company->getRelatedAddressRowFromIdAddressFk();
                if ($addressRow) {
                    $this->setAddress($bean, $addressRow, $this->company->getTitle());
                }
                if ($this->contact) {
                    $bean
                       ->setCivility($this->contact->getCivility())
                       ->setFirstname($this->contact->getFirstname())
                       ->setLastname($this->contact->getLastname())
                       ->setGsm($this->contact->getGsm())
                       ->setFunction($this->contact->getFunction());
                    if (!$bean->getTel()) {
                        $bean->setTel($this->contact->getTel());
                    }
                    if (!$bean->getFax()) {
                        $bean->setFax($this->contact->getFax());
                    }
                    if (!$bean->getEmail()) {
                        $bean->setEmail($this->contact->getEmail());
                    }
                }   
                break;
                
            // Hydratation du contact
            case self::PRIORITY_CONTACT : 
                $fields = array('civility', 'firstname', 'lastname', 'function', 
                                'company_name', 'tel', 'fax', 'gsm', 'email');
                $bean->populate($this->getContact()->toArray($fields));
                $addressRow = $this->contact->getRelatedAddressRowFromIdAddressFk();
                if ($this->company) {
                    if (!$addressRow) {
                        $addressRow = $this->company->getRelatedAddressRowFromIdAddressFk();
                    }
                    if ($addressRow) {
                        $addressRow->setAddress(trim($this->company->getTitle() . "\n" . $addressRow->getAddress()));
                    }
                    $bean->setCompanyName($this->company->getTitle())
                         ->setCompanyDesc($this->company->getDescription());
                    if (!$bean->getTel()) {
                        $bean->setTel($this->company->getTel());
                    }
                    if (!$bean->getFax()) {
                        $bean->setFax($this->company->getFax());
                    }
                    if (!$bean->getEmail()) {
                        $bean->setEmail($this->company->getEmail());
                    }
                }
                if ($addressRow) {
                    $title = trim(str_replace('  ', ' ', $bean->getCivility() . ' '
                       . $bean->getFirstname() . ' '
                       . $bean->getLastname()));
                    $this->setAddress($bean, $addressRow, $title);
                }
                break;
        }
        
        // @task mieux gérer les adresses vides
        if (!$bean->getAddress()) {
            $emptyAddress = new AddressBean();
            $line = '..........................................................................';
            $emptyAddress->setTitle($bean->getCompanyName() ?: $line);
            $emptyAddress->setAddress($line . "\n" . $line);
            $bean->setAddress($emptyAddress);
        }
        
        // Chargement du logo si mentionné
        // En essayant de ne pas charger le contenu de l'image
        // @task [LOGO] solution plus élégante ?
        if ($this->loadCompanyLogo && $this->company->getIdLogo()) {
            $sql = DbContainer::getImageTable()->getSql();
            $select = $sql
                ->select()
                ->columns(array('bean'))
                ->where(array('id' => $this->company->getIdLogo(), 'type' => 'logo'));
            $results = $sql->getAdapter()->query($sql->getSqlStringForSqlObject($select));
            $row = $results->execute()->current();
            if (is_array($row)) {
                $logo = new ImageRow();
                $logo->setId($this->company->getIdLogo());
                $imageBean = new ImageBean($logo->getImageFile());
                if ($row['bean']) {
                    $imageInternalBean = unserialize($row['bean']);
                    if ($imageInternalBean instanceof ImageInfo) {
                        $imageBean->setColors($imageInternalBean->getColors());
                    }
                }
                $bean->setCompanyLogo($imageBean);
            }
        }

        return $bean;
    }

    /**
     * @return \Sma\Db\ContactRow
     */
    public function getContact() {
        return $this->contact;
    }
    
    /**
     * @return \Sma\Db\CompanyRow
     */
    public function getCompany()
    {
        return $this->company;
    }
    
    /**
     * @return boolean
     */
    public function getLoadCompanyLogo() {
        return $this->loadCompanyLogo;
    }
}