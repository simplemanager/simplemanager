<?php
namespace App\Account\Form;

use Osf\Form\OsfForm as Form;
use Osf\Form\Element\ElementInput;
use Osf\Form\Element\ElementSubmit;
use Osf\Validator\Validator as V;
use Osf\Filter\Filter as F;
use H, L;

use Sma\Db\AddressTable as AT;

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
class FormLogin extends Form
{
    const BIND_COLS = [
        AT::COL_COUNTRY, 
        AT::COL_COUNTRY, 
        AT::COL_POSTAL_CODE
    ];
    
    public function init()
    {
        $this->setAction(H::url('account', 'login'));
        $this->setTitle(__("Identifiez-vous"), 'fa-key');
        $this->setStarsForRequired(false);
        
        $this->add((new ElementInput('email'))
                ->setTypeEmail()
                ->setAddonLeft(null, L::ICON_USER)
                ->setPlaceholder(__("Adresse e-mail"))
                ->add(F::getStringTrim())
                ->add(F::getStringToLower())
                ->add(V::getEmailAddress())
                ->setRequired());
        
        $this->add((new ElementInput('password'))
                ->setTypePassword()
                ->setAddonLeft(null, L::ICON_KEY)
                ->setPlaceholder(__("Mot de passe"))
                ->setRequired());
        
        $this->add((new ElementSubmit('submit'))->setValue("S'identifier"));
        
//        $this->bindModel(\Sma\Db\DbContainer::getAddressTable(), self::BIND_COLS);
    }
}