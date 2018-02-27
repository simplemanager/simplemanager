<?php
namespace Sma;

use Osf\Container\OsfContainer as OsfContainer;
use Osf\Controller\Router;
use Sma\Cache;
use Sma\Acl;

/**
 * SMA common container
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage container
 */
abstract class Container extends OsfContainer
{
    /**
     * @return \Sma\View\Helper
     */
    public static function getViewHelperLayout($appName = null)
    {
        return self::buildObject('\Sma\View\Helper', ['layout']);
    }
    
    /**
     * @return \Sma\View\Helper
     */
    public static function getViewHelper($appName = null, bool $layout = false)
    {
        return self::buildObject('\Sma\View\Helper', ['view']);
    }
    
    /**
     * @return \Common\View\Helper\AdminLayout
     */
    public static function getAdminLayout(): \Common\View\Helper\AdminLayout
    {
        return self::getViewHelperLayout()->adminLayout;
    }
    
    /**
     * @return \Sma\Layout\Admin
     */
    public static function getJsonRequest($namespace = null): \Sma\Layout\Admin
    {
        return self::buildObject('\Sma\Layout\Admin', [], $namespace);
    }
    
    /**
     * @return \Sma\Layout\FlashMessenger
     */
    public static function getFlashMessenger(): \Sma\Layout\FlashMessenger
    {
        return self::buildObject('\Sma\Layout\FlashMessenger', []);
    }
    
    /**
     * @return \Sma\Acl
     */
    public static function getAcl($cache = true): \Sma\Acl
    {
        static $acl = null;
        
        if ($acl !== null) {
            return $acl;
        }
        
        $aclFile = APPLICATION_PATH . '/App/' . Router::getDefaultControllerName(true) . '/Generated/acl.php';
        
        // Cached build
        if ($cache) {
            $aclStr = OsfContainer::getCache()->get(Acl::REDIS_CACHE_KEY);
            if ($aclStr) {
                $acl = unserialize($aclStr);
                return $acl;
            }
        }
        
        /* @var $acl \Sma\Acl */
        $acl = self::buildObject('\Sma\Acl', [$aclFile]);
        if ($cache) {
            $acl->buildAcl();
            OsfContainer::getCache()->set(Acl::REDIS_CACHE_KEY, serialize($acl));
        }
        return $acl;
    }
    
    /**
     * @return \Sma\Search
     */
    public static function getSearch(): \Sma\Search
    {
        return self::buildObject('\Sma\Search');
    }
    
    /**
     * @return \Sma\View\Helper\Crud
     */
    public static function getCrud(): \Sma\View\Helper\Crud
    {
        return self::buildObject('\Sma\View\Helper\Crud');
    }
    
    /**
     * @return \Sma\Safety\Firewall
     */
    public static function getFirewall(): \Sma\Safety\Firewall
    {
        return self::buildObject('\Sma\Safety\Firewall');
    }
    
    /**
     * @param string $namespace
     * @return \Sma\Cache
     */
    public static function getCacheSma(string $namespace = Cache::DEFAULT_NAMESPACE): \Sma\Cache
    {
        return self::buildObject('\Sma\Cache', [$namespace], $namespace);
    }
}
