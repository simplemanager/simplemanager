<?php
namespace App\Recipient\Form;

use Osf\Form\OsfForm as Form;
use Osf\Form\Element\ElementSubmit;
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
class FormContact extends Form
{
    public function init()
    {
        $contactForm  = DB::getContactTable()->getForm()->displayLabels(false);
        $addressForm  = DB::getAddressTable()->getForm()->setOptional()->displayLabels(false);
        
        $this->setPrefix('c');
        
        $this->add($contactForm->getElement('civility'))
             ->add($contactForm->getElement('firstname'))
             ->add($contactForm->getElement('lastname'))
             ->add($contactForm->getElement('function'));
        foreach ($addressForm->getElements() as $elt) {
            $this->add($elt->setPrefix('a'));
        }
        $this->add($contactForm->getElement('email'))
             ->add($contactForm->getElement('tel'))
             ->add($contactForm->getElement('gsm'))
             ->add($contactForm->getElement('fax'));
        
        $this->add((new ElementSubmit('submit'))->setValue(__("Mettre à jour")));
    }
}
