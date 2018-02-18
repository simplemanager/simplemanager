<?php
namespace App\Account\Form;

use Osf\Form\OsfForm as Form;
use Osf\Form\Element\ElementInput;
use Osf\Form\Element\ElementSubmit;
use Osf\Filter\Filter as F;
use DB;

/**
 * Retrieve password form
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 20 nov. 2013
 * @package common
 * @subpackage forms
 */
class FormRetrievePasswordAsk extends Form
{
    public function init()
    {
        $this->setTitle(__("Réinitialisation du mot de passe"), 'edit')->setStarsForRequired(false);
        
        $this->add((new ElementInput('email'))
                ->setTypeEmail()
                ->setRequired(true)
                ->setAddonLeft(null, 'at')
                ->setPlaceholder(__("Adresse e-mail de votre compte"))
                ->addFilter(F::getStringTrim())
                ->addFilter(F::getStringToLower()));
        $this->add((new ElementSubmit('submit'))->setValue(__("Réinitialiser le mot de passe")));
     }
     
     public function isValid($values = null) {
        $valid = parent::isValid($values);
        if ($valid) {
            $email = $this->getElement('email')->getValue();
        $emailExists = DB::getAccountTable()->select(['email' => $email, 'status' => ['enabled', 'draft']])->count();
            if (!$emailExists) {
                $this->getElement('email')->addError(sprintf(__("Cette adresse n'est liée à aucun compte %s."), APP_NAME));
                $valid = false;
            }
        }
        return $valid;
    }
}
