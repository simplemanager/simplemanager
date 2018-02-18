<?php
namespace App\Account\Form;

use Osf\Form\OsfForm as Form;
use Osf\Form\Element\ElementSubmit;
use Osf\Stream\Text;
use Sma\Session\Identity;
use DB;

/**
 * Email update
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 20 nov. 2013
 * @package common
 * @subpackage forms
 */
class FormEmail extends Form
{
    public function init()
    {
        $this->setTitle(__("Changer d'adresse e-mail"), 'at');
        
        $emailElement = DB::getAccountTable()
                ->getForm()
                ->displayLabels(false)
                ->getElement('email');
        
        $this->add($emailElement->setName('enew')->setPlaceholder(__("Nouvelle adresse e-mail")));
        $this->add((new ElementSubmit('submit'))->setValue(__("Envoyer un message de confirmation")));
    }
    
    public function isValid($values = null) {
        $valid = parent::isValid($values);
        if ($valid) {
            $email = $this->getElement('enew')->getValue();
            if (Text::toLower($email) === Text::toLower(Identity::get('email'))) {
                $this->getElement('enew')->addError(__("Cet e-mail est déjà celui de votre compte courant."));
                $valid = false;
            } else {
                $row = DB::getAccountTable()->select(['email' => $email]);
                if ($row->count()) {
                    $this->getElement('enew')->addError(__("Cet e-mail est lié à un compte existant. Si vous ne connaissez pas le mot de passe, déconnectez vous et utilisez le lien 'mot de passe perdu'. Sinon, déconnectez-vous puis reconnectez-vous avec cet e-mail."));
                    $valid = false;
                }
            }
        }
        return $valid;
    }
}
