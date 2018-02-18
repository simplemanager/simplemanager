<?php
namespace Sma\Layout;

use Osf\View\Helper\Bootstrap\AbstractViewHelper as AVH;
use Osf\View\Helper\Bootstrap\Tools\Checkers;
use Sma\Bean\NotificationBean;
use Sma\Session\Identity;
use Sma\Container;
use L;

/**
 * Messages à afficher dès que possible
 * 
 * Ce conteneur stocke dans une file en session des messages à afficher, 
 * puis les affiche dès que l'occasion de présente (requête JSON via le layout plugin)
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage layout
 */
class FlashMessenger
{
    const SESSION_NAMESPACE = 'flash';
    
    const NOTIF_KEY  = 'notif'; // Notifications courantes
    const ALERT_KEY  = 'alert'; // Alertes à afficher
    const TOPAR_KEY  = 'topar'; // Redirection vers ces paramètres de requête au login
    const NOTIF_HASH = 'nhash'; // Hash de notification
    
    protected $activated = true;
    protected $saved = true; // Tant qu'on a pas touché aux actions il n'y a rien à sauver
    
    protected $actions = [];
    
    public function __construct()
    {
        $this->actions = $this->getSession()->getAll();
        if (!$this->actions) {
            $this->cleanAll();
        }
    }
    
    public function save()
    {
        foreach ($this->actions as $key => $actions) {
            $this->getSession()->set($key, $actions);
        }
        $this->saved = true;
        return $this;
    }
    
    public function __destruct()
    {
        if (!$this->saved) {
            $this->save();
        }
    }
    
    /**
     * Ajoute un message dans le registre pour affichage
     * @param string $message
     * @param string $title
     * @param bool $status
     * @param bool $closable
     * @return $this
     */
    public function msg(?string $message, ?string $title = null, $status = AVH::STATUS_INFO, $closable = true)
    {
        // Hash de message, remplace les doublons
        $hash = md5($message . '|' . $title);
        
        // file_put_contents('/tmp/debug', 'add ' . $message, FILE_APPEND);
        
        // Vérifications
        Checkers::checkStatus($status);
        
        // Enregistrement
        $this->actions[self::ALERT_KEY][$hash] = [(string) $message, (string) $title, $status, (bool) $closable];
        
        // Modification effectuée = doit être sauvée
        $this->saved = false;
        
        return $this;
    }
    
    /**
     * Enregistre un message pour affichage dès que possible
     * @param string $message
     * @param string $title
     * @param bool $closable
     * @return $this
     */
    public function msgDanger(?string $message, ?string $title = null, $closable = true)
    {
        return $this->msg($message, $title, AVH::STATUS_DANGER, $closable);
    }
    
    /**
     * Enregistre un message pour affichage dès que possible
     * @param string $message
     * @param string $title
     * @param bool $closable
     * @return $this
     */
    public function msgInfo(?string $message, ?string $title = null, $closable = true)
    {
        return $this->msg($message, $title, AVH::STATUS_INFO, $closable);
    }
    
    /**
     * Enregistre un message pour affichage dès que possible
     * @param string $message
     * @param string $title
     * @param bool $closable
     * @return $this
     */
    public function msgWarning(?string $message, ?string $title = null, $closable = true)
    {
        return $this->msg($message, $title, AVH::STATUS_WARNING, $closable);
    }
    
    /**
     * Enregistre un message pour affichage dès que possible
     * @param string $message
     * @param string $title
     * @param bool $closable
     * @return $this
     */
    public function msgSuccess(?string $message, ?string $title = null, $closable = true)
    {
        return $this->msg($message, $title, AVH::STATUS_SUCCESS, $closable);
    }
    
    /**
     * N'envoie pas le message à la fin de cette requête
     * Cette méthode doit être appelée pour toute redirection, etc. qui 
     * empêcherait l'affichage des messages
     * @return $this
     */
    public function skipThisRequest()
    {
        $this->activated = false;
        return $this;
    }
    
    /**
     * Supprime les notifications à afficher
     * Si on ajoute pas de notification après ça, le bouton de notification n'apparaîtra plus.
     * @return $this
     */
    public function cleanNotifications()
    {
        // Plus d'action
        if ($this->actions[self::NOTIF_KEY] !== []) {
            $this->actions[self::NOTIF_KEY] = [];
        
            // Modification effectuée = doit être sauvée
            $this->saved = false;
        }
        
        return $this;
    }
    
    /**
     * Ajoute une notification dans la liste des notifications à afficher pour l'utilisateur courant
     * @param NotificationBean $notification
     * @return $this
     */
    public function addNotification(NotificationBean $notification)
    {
        // Ajout d'une notification en haut de la liste
        $newNotifs = array_merge([$notification->toArray()], $this->actions[self::NOTIF_KEY]);
        $this->actions[self::NOTIF_KEY] = $newNotifs;
        
        // Modification effectuée = doit être sauvée
        $this->saved = false;
            
        return $this;
    }
    
    /**
     * Suppression d'un notification et mise à jour du hash (pour éviter d'envoyer une alerte quand on supprime)
     * @param int $id
     * @param bool $updateHash
     * @return $this
     */
    public function removeNotification(int $id, bool $updateHash = true)
    {
        // Suppression dans le tableau des notifications
        foreach ($this->actions[self::NOTIF_KEY] as $key => $notification) {
            if ($notification[4] === $id) {
                unset($this->actions[self::NOTIF_KEY][$key]);
            }
        }
        
        // Mise à jour du hash
        if ($updateHash) {
            $newHash = $this->getNotificationHash();
            $this->actions[self::NOTIF_HASH] = ['hash' => $newHash, 'count' => count($this->actions[self::NOTIF_KEY])];
            $this->saved = false;
        }
        
        return $this;
    }
    
    /**
     * Hash correspondant à la liste (pour éviter d'envoyer deux fois le même jeu de notifications)
     * @return string
     */
    protected function getNotificationHash(): string
    {
        return implode('|', $this->getNotificationIds());
    }
    
    /**
     * @return array
     */
    public function getNotificationIds(): array
    {
        $ids = [];
        foreach ($this->actions[self::NOTIF_KEY] as $notification) {
            $ids[] = $notification[4];
        }
        return $ids;
    }
    
    /**
     * Redirection vers cette url au prochain login
     * @param array $params
     * @return $this
     */
    public function setRedirectToParams(array $params)
    {
        // Ajout d'une redirection
        $this->actions[self::TOPAR_KEY] = $params;
        
        // Modification effectuée = doit être sauvée
        $this->saved = false;
        
        return $this;
    }
    
    /**
     * Récupère l'url vers laquelle on doit rediriger l'utilisateur au login
     * @param bool $resetParams
     * @return string
     */
    public function getRedirectToParams(bool $resetParams = true)
    {
        $url = $this->actions[self::TOPAR_KEY];
        if ($resetParams) {
            $this->actions[self::TOPAR_KEY] = [];
        }
        return $url;
    }
    
    /**
     * Expédie les messages dans la requête JSON en cours et vide le registre
     * @return $this
     */
    public function sendMessages()
    {
        if (!$this->activated) {
            return $this;
        }
        
        // file_put_contents('/tmp/debug', print_r(self::getSession()->getAll(), true), FILE_APPEND);
                
        // Mise à jour des notifications...
        $notificationHash = $this->getNotificationHash();
        $notificationHashSent = isset($this->actions[self::NOTIF_HASH]['hash']) ? $this->actions[self::NOTIF_HASH]['hash'] : '';
        $notifCountSent = isset($this->actions[self::NOTIF_HASH]['count']) ? $this->actions[self::NOTIF_HASH]['count'] : 0;
        $notifCount = count($this->actions[self::NOTIF_KEY]);

        // Suppression des notifications par défaut (réinitialisation)
        Container::getJsonRequest()->cleanButtons();

        // Mise à jour des notifications si elles ont évolué
        if ($notificationHash) {
            Container::getJsonRequest()->updateButtonNotifications(__("Notifications"), L::ICON_CALLOUT, $notifCount, 'warning');
            foreach ($this->actions[self::NOTIF_KEY] as $notif) {
                Container::getJsonRequest()->addButtonNotificationsLink($notif[4], $notif[1], $notif[5] . ' : ' . $notif[0], $notif[2], $notif[3]);
            }
            $newNotifCount = $notifCount - $notifCountSent;
            if (!Identity::isLevelExpert() && $notificationHash !== $notificationHashSent && $newNotifCount) {
                $explication = Identity::isLevelBeginner() ? __("Cliquez sur l'icône en haut à droite.") : null;
                $msg = $newNotifCount > 1 ? 
                    sprintf(__("Vous avez %d notifications."), $newNotifCount) : 
                    __("Vous avez une notification.");
                $this->msgInfo($explication, $msg);
            }
        }
        $this->actions[self::NOTIF_HASH] = ['hash' => $notificationHash, 'count' => $notifCount];
        
        // Envoi des alertes
        foreach ($this->actions[self::ALERT_KEY] as $msg) {
            Container::getJsonRequest()->addAlert($msg[1], $msg[0], $msg[2], $msg[3]);
        }
        $this->actions[self::ALERT_KEY] = [];
        
        // Modification effectuée = doit être sauvée
        $this->saved = false;
        
        return $this;
    }
    
    /**
     * Supprime les alertes
     * @return $this
     */
    public function cleanAlerts()
    {
        // Plus d'action
        if ($this->actions[self::ALERT_KEY] !== []) {
            $this->actions[self::ALERT_KEY] = [];
        
            // Modification effectuée = doit être sauvée
            $this->saved = false;
        }
        
        return $this;
    }
    
    public function cleanAll()
    {
        $this->actions = [
            self::ALERT_KEY => [],
            self::NOTIF_KEY => [],
            self::TOPAR_KEY => [],
            self::NOTIF_HASH => ''
        ];
        
        // Modification effectuée = doit être sauvée
        $this->saved = false;
    }
    
    /**
     * @return \Osf\Session\AppSession
     */
    protected function getSession()
    {
        return Container::getSession(self::SESSION_NAMESPACE);
    }
    
    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }
}
