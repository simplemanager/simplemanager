<?php
namespace App\Ticket\Form;

use Osf\Form\OsfForm as Form;
use Osf\Form\Element\ElementInput;
use Osf\Form\Element\ElementSubmit;
use Osf\Filter\Filter as F;
use L;

/**
 * Log form
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package common
 * @subpackage forms
 */
class FormLog extends Form
{
    public function init()
    {
        $this->setTitle(__("Ajouter un log"), L::ICON_CALLOUT);
        
        $this->add((new ElementInput('log'))
                ->setRequired()
                ->add(F::getStringTrim())
                ->setPlaceholder(__("Message")));

        $this->add((new ElementSubmit('save'))->setValue(__("Ajouter")));
    }
}
