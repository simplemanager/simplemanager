<?php
namespace Sma;

use Sma\Log\DbAdapter;
use Osf\Log\LogProxy;
use Osf\Log\AdapterInterface;
use App\Common\Container as C;
use Osf\Exception\ArchException;

/**
 * Simple logger
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage log
 */
class Log extends LogProxy
{
    const WEIGHT = [
        self::LEVEL_ERROR   => 0,
        self::LEVEL_WARNING => 1,
        self::LEVEL_INFO    => 2
    ];
    
    /**
     * @return AdapterInterface
     */
    public static function getAdapter(): AdapterInterface
    {
        if (self::$adapter === null) {
            self::setAdapter(new DbAdapter());
        }
        return parent::getAdapter();
    }
    
    /**
     * Is log level is enabled in configuration environment ? (default is warning)
     * @staticvar type $currentLevel
     * @param string $level
     * @return bool
     * @throws ArchException
     */
    public static function isLevelEnabled(string $level, ?string $category = null): bool
    {
        static $currentLevel = null;
        
        // Exception for (important) comment category
        if ($category === 'COMMENT') {
            return true;
        }
        
        // Get the current log level from configuration
        if (!$currentLevel) {
            $currentLevel = C::getConfig()->getConfig('log', 'level') ?? self::LEVEL_WARNING;
            if (!in_array($currentLevel, [self::LEVEL_INFO, self::LEVEL_WARNING, self::LEVEL_ERROR])) {
                throw new ArchException('Unkown log level [' . $currentLevel . ']');
            }
        }
        
        // Calculate if enabled
        return self::WEIGHT[$level] <= self::WEIGHT[$currentLevel];
    }
}
