<?php
namespace App\Document\Model\Pdf;

use Osf\Exception\ArchException;
use Osf\Pdf\Document\Bean\LetterBean;
use Osf\Pdf\Document\Bean\ContactBean;
use Sma\Db\CompanyRow;
use Sma\Db\ContactRow;
use Sma\Db\LetterTemplateRow;

/**
 * Hydrateur de lettre pdf à partir des données de la base
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 12 nov. 2013
 * @package company
 * @subpackage pdf
 */
class LetterDbHydrator
{
    /**
     * @var \Sma\Db\CompanyRow
     */
    protected $companyProvider;
    
    /**
     * @var \Sma\Db\CompanyRow
     */
    protected $companyReceiver;
    
    /**
     * @var \Sma\Db\ContactRow
     */
    protected $contactProvider;
    
    /**
     * @var \Sma\Db\ContactRow
     */
    protected $contactReceiver;

    /**
     * @var \Sma\Db\LetterTemplateRow
     */
    protected $letterTemplate;
    
    /**
     * @param CompanyRow $company
     * @return \App\Document\Model\Pdf\LetterDbHydrator
     */
    public function setCompanyProvider(CompanyRow $company)
    {
        $this->companyProvider = $company;
        return $this;
    }
    
    /**
     * @param CompanyRow $company
     * @return \App\Document\Model\Pdf\LetterDbHydrator
     */
    public function setCompanyReceiver(CompanyRow $company)
    {
        $this->companyReceiver = $company;
        return $this;
    }
    
    /**
     * @param ContactRow $contact
     * @return \App\Document\Model\Pdf\LetterDbHydrator
     */
    public function setContactProvider(ContactRow $contact)
    {
        $this->contactProvider = $contact;
        return $this;
    }
    
    /**
     * @param ContactRow $contact
     * @return \App\Document\Model\Pdf\LetterDbHydrator
     */
    public function setContactReceiver(ContactRow $contact)
    {
        $this->contactReceiver = $contact;
        return $this;
    }
    
    /**
     * @param LetterTemplateRow $letterTemplate
     * @return \App\Document\Model\Pdf\LetterDbHydrator
     */
    public function setLetterTemplate(LetterTemplateRow $letterTemplate)
    {
        $this->letterTemplate = $letterTemplate;
        return $this;
    }
    
    /**
     * @param LetterBean $bean
     * @return LetterBean
     */
    public function hydrate(LetterBean $bean)
    {
        // Vérifications
        if (!$this->contactProvider && !$this->companyProvider) {
            throw new ArchException('Please select at least a contact provider or a company provider');
        }
        if (!$this->contactReceiver && !$this->companyReceiver) {
            throw new ArchException('Please select at least a contact receiver or a company receiver');
        }
        if (!$this->letterTemplate) {
            throw new ArchException('No letter template specified');
        }
        
        // Contact principal de l'entreprise si pas de contact spécifié
        $providerPriority = HelperContactDbHydrator::PRIORITY_COMPANY;
        $receiverPriority = HelperContactDbHydrator::PRIORITY_COMPANY;
        if (!$this->contactProvider) {
            $this->contactProvider = $this->companyProvider->getRelatedContactRowFromIdContactFk();
        }
        if (!$this->contactReceiver) {
            $this->contactReceiver = $this->companyReceiver->getRelatedContactRowFromIdContactFk();
        }
        if (!$this->companyProvider) {
            $providerPriority = HelperContactDbHydrator::PRIORITY_CONTACT;
            $this->companyProvider = $this->contactProvider->getRelatedCompanyRowFromIdCompanyFk();
        }
        if (!$this->companyReceiver) {
            $receiverPriority = HelperContactDbHydrator::PRIORITY_CONTACT;
            $this->companyReceiver = $this->contactReceiver->getRelatedCompanyRowFromIdCompanyFk();
        }
        
        // Récupération du sous-bean contact du provider
        $contactProviderHydrator = new HelperContactDbHydrator();
        if ($this->companyProvider) {
            $contactProviderHydrator->setCompany($this->companyProvider);
        }
        if ($this->contactProvider) {
            $contactProviderHydrator->setContact($this->contactProvider);
        }
        $contactProviderHydrator->setPriority($providerPriority)->setLoadCompanyLogo(true);
        $contactProviderBean = $contactProviderHydrator->hydrate(new ContactBean());
        
        // Récupération du sous-bean contact du receiver
        $contactReceiverHydrator = new HelperContactDbHydrator();
        if ($this->companyReceiver) {
            $contactReceiverHydrator->setCompany($this->companyReceiver);
        }
        if ($this->contactReceiver) {
            $contactReceiverHydrator->setContact($this->contactReceiver);
        }
        $contactReceiverHydrator->setPriority($receiverPriority);
        $contactReceiverBean = $contactReceiverHydrator->hydrate(new ContactBean());
        
        // Remplissage du bean
        $bean->populate($this->letterTemplate->toArray(array('dear', 'body', 'signature')));
        $bean->setProvider($contactProviderBean);
        $bean->setRecipient($contactReceiverBean);
        
        return $bean;
    }
}
