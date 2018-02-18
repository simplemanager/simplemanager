<?php
namespace App\Account\Form;

use Osf\Form\OsfForm as Form;
use Osf\Form\Element\ElementSubmit;
use Sma\Db\CompanyTable;
use DB;

/**
 * Login / Pass
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 20 nov. 2013
 * @package common
 * @subpackage forms
 */
class FormCompany extends Form
{
    public function init()
    {
        $this->setTitle('Ma société', 'edit');
        
        $fields = ['legal_status', 'title', 'description', 'email', 'tel', 'fax', 'url'];
        $companyForm = DB::getCompanyTable()->getForm()->onlyFields($fields)->displayLabels(false);
        $addressForm = DB::getAddressTable()->getForm()->setOptional()->displayLabels(false);
        
        $this->setPrefix('c');
        
        $this->add($companyForm
                ->getElement('legal_status')
                ->setDescription(__("Vos paramètres seront automatiquement ajustés sur votre statut juridique (franchise TVA, etc.).")))
             ->add($companyForm->getElement('title')->setRequired(false))
             ->add($companyForm->getElement('description')->setRelevanceLow());
        foreach ($addressForm->getElements() as $elt) {
            if ($elt->getName() == 'submit') { continue; }
            if ($elt->getName() == 'country') { $elt->setRelevanceLow(); }
            $this->add($elt->setPrefix('a'));
        }
        $this->add($companyForm->getElement('email')
                ->setDescription(__("Spécifiez un e-mail pour remplacer celui de votre compte dans vos documents (lettres, factures...)."))
                ->setRelevanceLow())
            ->add($companyForm->getElement('tel'))
            ->add($companyForm->getElement('fax'))
            ->add($companyForm->getElement('url')->setRelevanceLow());
        
        $this->add((new ElementSubmit('submit'))->setValue(__("Mettre à jour")));
    }
    
    public function isValid($values = null)
    {
        $valid = parent::isValid($values);
        
        if ($valid) {
            $values = $this->getValues();        
            if ($values['c']['legal_status'] !== '' 
             && $values['c']['legal_status'] !== 'ei' 
             && trim($values['c']['title']) === '') {
                $statusName = CompanyTable::STATUS_TITLES_SHORT[$values['c']['legal_status']];
                $this->getElement('title')->addError(sprintf(__("Une personne morale de type %s doit avoir un nom."), $statusName));
                $valid = false;
            }
        }
        
        return $valid;
    }
}