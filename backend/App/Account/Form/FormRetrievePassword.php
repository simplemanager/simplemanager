<?php
namespace App\Account\Form;

use Osf\Form\OsfForm as Form;
use Osf\Form\Element\ElementInput;
use Osf\Form\Element\ElementSubmit;
use DB, L;

/**
 * Password update
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 20 nov. 2013
 * @package common
 * @subpackage forms
 */
class FormRetrievePassword extends Form
{
    public function init()
    {
        $this->setTitle(__("Nouveau mot de passe"), 'edit')->setStarsForRequired(false);
        
        $passElement = DB::getAccountTable()
                ->getForm()
                ->displayLabels(false)
                ->buildIfNotAlreadyBuilded()
                ->getElement('password');
        
        $this->add((new ElementInput('pnew'))
                ->setTypePassword()
                ->setAddonLeft(null, 'key')
                ->setRequired(true)
                ->setValidators($passElement->getValidators())
                ->setFilters($passElement->getFilters())
                ->setLabel(__("Spécifiez un nouveau mot de passe :")));
        $this->add((new ElementInput('ptwo'))
                ->setTypePassword()
                ->setAddonLeft(null, 'key')
                ->setRequired(true)
                ->setFilters($passElement->getFilters())
                ->setLabel(__("Confirmer le nouveau mot de passe :")));
        
        $this->add((new ElementSubmit('submit'))->setValue(__("Mettre à jour")));
    }
    
    public function isValid($values = null) {
        $valid = parent::isValid($values);
        
        // Vérification de l'ancien mot de passe et de la correspondance
        if ($valid) {
            if ($this->getElement('pnew')->getValue() !== $this->getElement('ptwo')->getValue()) {
                $this->getElement('ptwo')->addError(__("Les mots de passe ne correspondent pas"));
                $valid = false;
            }
        }
        
        return $valid;
    }
}
