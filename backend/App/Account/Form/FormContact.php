<?php
namespace App\Account\Form;

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
        $this->setTitle('A propos de moi', 'edit');
        
        $contactForm = DB::getContactTable()->getForm()->displayLabels(false);
        
        $this->setPrefix('c');
        
        $this->add($contactForm->getElement('civility')->setRequired())
             ->add($contactForm->getElement('firstname'))
             ->add($contactForm->getElement('lastname'))
             ->add($contactForm->getElement('function')->setRelevanceLow());
        $this->add($contactForm->getElement('gsm')->setRelevanceLow()->getHelper()->setSize(12)->getElement());
        
        $this->add((new ElementSubmit('submit'))->setValue(__("Mettre à jour")));
    }
}
