<?php
namespace App\Account\Form;

use Osf\Form\TableForm;
use Osf\Form\Element\ElementCheckboxes;
use Osf\Form\Element\ElementSubmit;
use Osf\Form\Element\ElementInput;
use Sma\Db\ContactRow;
// use App\Common\Container;
use DB, H;

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
class FormRegistration extends TableForm
{
    public function init()
    {
        // On rattache le formulaire à la table account et on extrait les éléments
        $this->setTitle(__("Inscription"), 'edit')
             ->setTable(DB::getAccountTable())
             ->onlyFields(['firstname', 'lastname', 'email', 'password'])
             ->displayLabels(false)
             ->build();
        
        // Pas d'étoile pour les éléments requis
        $this->setStarsForRequired(false);
        
        // Etoile pour le mot de passe
        $this->getElement('password')->setPlaceholder('Mot de passe *');
        
        // Répéter le mot de passe
        $this->add((new ElementInput('passwordbis'))
                ->setTypePassword()
                ->setPlaceholder(__("Confirmer le mot de passe *"))
                ->setAddonLeft(null, 'key')
            );
        
        // Case à cocher "lu et approuvé"
        $this->add((new ElementCheckboxes('conditions'))
                ->setOptions([
                    'conditions' => __("J'ai lu et approuvé les ") . H::modalLink(__("conditions d'utilisation"), 'cdt')->setLoadUrl(H::url('info', 'conditions', ['no' => 'layout']))
                ]));
        
        $submit = (new ElementSubmit('submit'))->setValue(__("S'inscrire"));
//        $config = Container::getConfig()->getConfig('recaptcha');
//        if (is_array($config) && isset($config['sitekey']) && $config['sitekey']) {
//            $submit->setRecaptchaSitekey($config['sitekey']);
//        }
        $this->add($submit);
    }
    
    public function isValid($values = null) {
        $output = parent::isValid($values);
        if ($output) {
            if ($this->getElement('password')->getValue() != $this->getElement('passwordbis')->getValue()) {
                $this->getElement('passwordbis')->addError(__("Les mots de passe ne correspondent pas"));
                $output = false;
            }
            if (!is_array($this->getElement('conditions')->getValue()) 
             || !in_array('conditions', $this->getElement('conditions')->getValue())) {
                $this->getElement('conditions')->addError(__("Vous devez accepter les conditions d'utilisation"));
                $output = false;
            }
        }
        if ($output) {
            $whereEmail = ['email' => $this->getElement('email')->getValue()];
            $account = DB::getAccountTable()->select($whereEmail)->current();
            if ($account) {
                $this->getElement('email')->addError(__("Cet e-mail est déjà enregistré."));
                switch ($account->getStatus()) {
                    case 'enabled' :
                        $this->getElement('email')
                             ->addError(sprintf(__("Avez-vous %s ?"), H::link(__("oublié votre mot de passe"), 'account', 'password')));
                        break;
                    case 'draft' : 
                        $this->getElement('email')
                            ->addError(sprintf(__("Un message vous a déjà été envoyé pour activer votre compte. Vérifiez qu'il n'a pas été considéré comme un SPAM ou attendez quelques minutes si vous venez de vous inscrire. Sinon, %s pour réactiver votre compte."), H::link(__("réinitialisez votre mot de passe"), 'account', 'password')));
                        break;
                    case 'disabled' : 
                        $this->getElement('email')
                            ->addError(__("Votre compte est désactivé."));
                        break;
                    case 'suspended' : 
                        $this->getElement('email')
                            ->addError(__("Votre compte est suspendu."));
                        break;
                }
                $output = false;
            }
        }
        return $output;
    }
    
    /**
     * Insert registration values, set account to "draft" and return account id
     * @param array $fields
     * @return int
     */
    public function insertValues(array $fields = [])
    {
        // Account insert
        $values = $this->getValues($fields);
        $values['status'] = 'draft';
        $this->getTable()->insert($values);
        $id = $this->getTable()->lastInsertValue;
        
        // Contact insert
        $values = $this->getValues(['firstname', 'lastname', 'email']);
        $values['id_account'] = $this->getTable()->lastInsertValue;
        $values['is_account'] = $values['id_account'];
        (new ContactRow())->populate($values)->save();
        
        return $id;
    }
}
