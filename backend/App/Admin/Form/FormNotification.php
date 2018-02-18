<?php
namespace App\Admin\Form;

use Osf\Form\Element\ElementCheckbox;
use Osf\Form\Element\ElementSelect;
use Osf\Form\OsfForm as Form;
use App\Admin\Model\AccountDbManager as ADM;
use DB, H;

/**
 * Génération de notifications
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package app
 * @subpackage admin
 */
class FormNotification extends Form
{
    function init()
    {
        $this->setTitle('Ajout de notifications', 'bell-o');
        
        // Broadcast
        $this->add((new ElementCheckbox('broadcast'))
             ->setLabel(__("Broadcast (envoi à tous les comptes actifs)"))
        );
        
        // Ou sélection du compte
        $this->add((new ElementSelect('account_ids'))
            ->setAutocompleteAdapter(new ADM(20))
            ->setMultiple()
            ->setValue([])
        );
        
        // @task [UX] généraliser le sytème des hide/show
        $js  = "\$(document).on('change', '#broadcast', function () {if (\$('#broadcast').is(':checked')){\$('#account_ids').parent().fadeOut(200);}else{\$('#account_ids').parent().fadeIn(200);}});";
        $js .= "\$(document).ready(function(){if (\$('#broadcast').is(':checked')){\$('#account_ids').parent().hide();}});";
        H::layout()->appendScripts($js);
        
        // Champs de la table des notifications
        $notifForm = DB::getNotificationTable()->getForm()->displayLabels(false);
        foreach ($notifForm->getElements() as $elt) {
            $this->add($elt);
        }
    }
    
    public function isValid($values = null)
    {
        $valid = parent::isValid($values);
        if (!$this->getElement('broadcast')->getValue() && !$this->getElement('account_ids')->getValue()) {
            $this->getElement('account_ids')->addError('Spécifier au moins un compte');
            $valid = false;
        }
        
        return $valid;
    }
}
