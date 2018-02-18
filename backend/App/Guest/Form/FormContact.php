<?php
namespace App\Guest\Form;

use Osf\Form\Element\ElementSubmit;
use App\Recipient\Form\FormRecipient;

/**
 * Mise à jour de contact utilisateur
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package app
 * @subpackage guest
 */
class FormContact extends FormRecipient
{
    public function init()
    {
        parent::init();
        $this->setTitle(__("A propos de moi"), 'user');
        
        // Nettoyages
        $this->removeElement('description')
             ->removeElement('submit');
        
        // Mise à jour du bouton
        $this->add((new ElementSubmit('submit'))->setValue(__("Mettre à jour")));
    }
}
