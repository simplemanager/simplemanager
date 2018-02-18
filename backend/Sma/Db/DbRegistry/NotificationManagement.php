<?php
namespace Sma\Db\DbRegistry;

use Osf\Helper\Mysql;
use Sma\Bean\NotificationBean;
use Sma\Db\NotificationTable;
use Sma\Db\NotificationRow;
use Sma\Session\Identity;
use Sma\Log;
use App\Common\Container;
use DB;

/**
 * Gestion centralisée des notifications
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage db
 */
trait NotificationManagement
{
    /**
     * Ajoute une notification à un utilisateur (courant par défaut)
     * @param NotificationBean $notification
     * @param int|null $idAccount
     * @param bool $fixIdAccount
     * @param bool $updateIfExists
     * @return bool
     */
    public static function notificationPush(NotificationBean $notification, ?int $idAccount = null, bool $fixIdAccount = true, bool $updateIfExists = true): bool
    {
        // Récupération des données
        $idAccount = $fixIdAccount ? NotificationTable::fixIdAccount($idAccount) : $idAccount;
        if (!$idAccount) {
            Log::error('Id account not fixed or not found', 'NOTIF', [$idAccount]);
            return false;
        }
        $params = [
            'id_account' => $idAccount,
            'icon'       => $notification->getIcon(),
            'color'      => $notification->getColor(),
            'content'    => $notification->getContent()
        ];
        $dateEnd = $notification->getDateEnd() ? Mysql::dateToMysql($notification->getDateEnd()) : null;
        
        // Update si la notification existe déjà
        $updated = false;
        if ($updateIfExists) {
            $row = DB::getNotificationTable()->select($params)->current();
            if ($row instanceof NotificationRow) {
                $row->setDateEnd($dateEnd)->setLink($notification->getLink())->save();
                $updated = $row->getId();
            }
        }
        
        // Insertion sinon
        if (!$updated) {
            $params['link'] = $notification->getLink();
            $params['date_end'] = $dateEnd;
            $affectedRows = DB::getNotificationTable()->insert($params);
            if (!$affectedRows) {
                Log::error('Unable to insert notification', 'NOTIF', ['notification' => $notification, 'affectedRows' => $affectedRows]);
                return false;
            }
        }
        
        // Push de la notification dans flash messenger
        $notification->setId($updated ?: DB::getNotificationTable()->getLastInsertValue());
        $notification->setDate(new \DateTime());
        if (!$updated && $idAccount === Identity::getIdAccount()) {
            Container::getFlashMessenger()->addNotification($notification);
        }
        return true;
    }
    
    /**
     * Envoi de multiples notifications
     * @param NotificationBean $notification
     * @param bool|null $broadcast
     * @param array $ids
     * @return bool
     */
    public static function notificationPushMultiple(NotificationBean $notification, ?bool $broadcast = false, ?array $ids = null): bool
    {
        // Tous les comptes actifs
        if ($broadcast === true) {
            $accounts = DB::getAccountTable()->buildSelect(['status' => 'enabled'])->columns(['id'])->execute();
            foreach ($accounts as $row) {
                self::notificationPush($notification, (int) $row['id']);
            }
            return true;
        }
        
        // Ou une selection d'ids
        if ($ids) {
            foreach ($ids as $id) {
                self::notificationPush($notification, (int) $id);
            }
            return true;
        }
        
        // Ou rien
        return false;
    }
    
    /**
     * Charge les notifications (lors de l'identification d'un utilisateur)
     * @return void
     */
    public static function notificationLoad(): void
    {
        self::notificationUnload();
        self::addToFlashMessenger(DB::getNotificationTable()->select(['id_account' => Identity::getIdAccount()]));
    }
    
    /**
     * Charge les nouvelles notifications (tick)
     * @return void
     */
    public static function notificationUpdate(): void
    {
        $currentIds = implode(',', Container::getFlashMessenger()->getNotificationIds());
        $sql  = 'SELECT * FROM ' . DB::getNotificationTable()->getTableName() . ' '
                . 'WHERE id_account = ? '
                . 'AND (date_end > NOW() OR date_end IS NULL) ';
        $sql .= $currentIds ? 'AND id NOT IN (' . $currentIds . ')' : '';
        $notifications = DB::getNotificationTable()->prepare($sql)->execute([Identity::getIdAccount()]);
        self::addToFlashMessenger($notifications);
    }
    
    /**
     * @param array|Iterator $notifications
     * @return void
     */
    protected static function addToFlashMessenger($notifications): void
    {
        foreach ($notifications as $row) {
            $row = is_array($row) ? $row : $row->toArray();
            Container::getFlashMessenger()->addNotification((new NotificationBean())->populate($row));
        }
    }
    
    /**
     * Supprime une notification (sur action de l'utilisateur ou autre)
     * @param int $id
     * @param int|null $idAccount
     * @return bool
     */
    public static function notificationRemoveOne(int $id, ?int $idAccount = null): bool
    {
        $result = DB::getNotificationTable()->delete(['id' => $id, 'id_account' => NotificationTable::fixIdAccount($idAccount)]);
        Container::getFlashMessenger()->removeNotification($id);
        return (bool) $result;
    }
    
    /**
     * Supprime l'ensemble des notifications d'un utilisateur
     * @param int|null $idAccount
     * @return bool
     */
    public static function notificationRemoveAll(?int $idAccount = null): bool
    {
        $idAccount = NotificationTable::fixIdAccount($idAccount);
        $result = DB::getNotificationTable()->delete(['id_account' => $idAccount]);
        if ($idAccount === Identity::getIdAccount()) {
            Container::getFlashMessenger()->cleanNotifications();
        }
        return (bool) $result;
    }
    
    /**
     * Suppression des notifications périmées
     * @param int|null $idAccount
     * @return void
     */
    public static function notificationClean(?int $idAccount = null): void
    {
        $sql = 'DELETE FROM notification WHERE 1 ';
        $params = [];
        if ($idAccount) {
            $sql .= 'AND id_account = ? ';
            $params[] = $idAccount;
        }
        $sql .= 'AND date_end < NOW() - INTERVAL 1 DAY';
        DB::getNotificationTable()->prepare($sql)->execute($params);
    }
    
    /**
     * Retire les notifications de l'environnement (sans les retire de la base) (logout)
     * @return void
     */
    public static function notificationUnload(): void
    {
        Container::getFlashMessenger()->cleanNotifications();
    }
}
