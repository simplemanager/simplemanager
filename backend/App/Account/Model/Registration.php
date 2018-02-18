<?php
namespace App\Account\Model;

use Sma\Mail;
use App\Account\Form\FormRegistration;
use App\Admin\Controller as AC;
use H;

/**
 * Registration tools
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package app
 * @subpackage account
 */
class Registration
{
    /**
     * Notifie l'administrateur qu'un nouveau compte a été créé
     * @param FormRegistration $form
     * @param int $idAccount
     */
    public static function notifyRegistered(FormRegistration $form, int $idAccount)
    {
        $values = $form->getValues();
        $mail = new Mail();
        $mail
            ->addToAdmin()
            ->setSubject(sprintf(__("[%s] Nouvelle inscription : %s"), APP_SNAM, $values['email']))
            ->addTitle(__("Nouvel utilisateur inscrit :"))
            ->addBullet(sprintf(__("Prénom: %s"), $values['firstname']))
            ->addBullet(sprintf(__("Nom: %s"), $values['lastname']))
            ->addBullet(sprintf(__("E-mail: %s"), $values['email']))
            ->addBullet(sprintf(__("Id du compte: %d"), $idAccount))
            ->addParagraph(__("Actions :"))
            ->addLinkBullet(__("Gérer le compte"), H::baseUrl(H::url('admin', 'board', ['id' => $idAccount]), true))
            ->addLinkBullet(__("Confirmer"),  H::baseUrl(H::url('admin', 'board', ['touch' => $idAccount, 'a' => AC::ACTION_STATUS_ENABLE]), true))
            ->addLinkBullet(__("Suspendre"),  H::baseUrl(H::url('admin', 'board', ['touch' => $idAccount, 'a' => AC::ACTION_STATUS_SUSPEND]), true))
            ->addLinkBullet(__("Désactiver"), H::baseUrl(H::url('admin', 'board', ['touch' => $idAccount, 'a' => AC::ACTION_STATUS_DISABLE]), true))
            ->addParagraph(__("Confirmation à effectuer."))
            ->sendDeferred();
    }
}

//                ->addLinkBullet(__("Supprimer"),   H::baseUrl(H::url('admin', 'board', ['touch' => $idAccount, 'a' => AC::]), true))
