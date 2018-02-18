<?php
namespace Sma;

use Osf\Cache\OsfCache;
use Osf\Controller\Cli;
use Sma\Container;
use Sma\Session\Identity;

/**
 * Cache de haut niveau
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage cache
 */
class Cache extends OsfCache
{
    // Categories
    public const C_PRODUCT  = 'P';
    public const C_CONTACT  = 'C';
    public const C_DOCUMENT = 'D';
    
    // Type d'élément à calculer
    public const T_LIST_ITEM = 'LI';
    
    protected const SEPARATOR = ':';
    
    public const NAMESPACE = 'SMAI' . self::SEPARATOR;
    
    /**
     * @param string $category
     * @param string $type
     * @param int $id
     * @param string $content
     * @return $this
     */
    public function setItem(string $category, string $type, int $id, string $content, ?int $idAccount = null)
    {
        if ($this->noCache()) {
            return $this;
        }
        $this->getRedis()->hSet(self::getItemKey($category, $id, $idAccount), $type, $content);
        return $this;
    }
    
    /**
     * @param string $category
     * @param string $type
     * @param int $id
     * @param int|null $idAccount
     * @return string
     */
    public function getItem(string $category, string $type, int $id, ?int $idAccount = null): string
    {
        return $this->getRedis()->hGet(self::getItemKey($category, $id, $idAccount), $type);
    }
    
    /**
     * @param string $category
     * @param int $id
     * @param int|null $idAccount
     * @return $this
     * @task clean pour tous les devices
     */
    public function cleanItem(string $category, int $id, ?int $idAccount = null)
    {
        $this->getRedis()->del(self::getItemKey($category, $id, $idAccount));
        return $this;
    }
    
    /**
     * Suppression de toutes les clés d'un utilisateur donné
     * @param int|null $idAccount
     * @return $this
     */
    public function cleanUserCache(?int $idAccount = null)
    {
        $idAccount = $idAccount ?? Identity::getIdAccount();
        foreach ($this->getRedis()->keys(self::NAMESPACE . self::getUserKey($idAccount) . '*') as $key) {
            $this->getRedis()->del($key);
        }
        return $this;
    }
    
    /**
     * @param string $category
     * @param string $id
     * @return string
     */
    public static function getItemKey(string $category, string $id, ?int $idAccount = null): string
    {
        static $device = null;
        static $user = null;
        
        if (!$user) {
            $user = self::getUserKey($idAccount ?? Identity::getIdAccount());
        }
        if (!$device) {
            $device = Cli::isCli() ? 'I' : (Container::getDevice()->isMobile() ? 'M' : (Container::getDevice()->isTablet() ? 'T' : 'C'));
        }
        return self::NAMESPACE . $user . self::SEPARATOR . $device . self::SEPARATOR . $category . self::SEPARATOR . $id;
    }
    
    /**
     * @param int|null $idAccount
     * @return string
     */
    protected static function getUserKey(?int $idAccount = null): string
    {
        return 'U' . ($idAccount ?: 'A');
    }
}