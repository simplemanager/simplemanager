<?php
namespace Sma;

use Sma\Log\DbAdapter;
use Osf\Log\LogProxy;
use Osf\Log\AdapterInterface;

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
    public static function getAdapter(): AdapterInterface
    {
        if (self::$adapter === null) {
            self::setAdapter(new DbAdapter());
        }
        return parent::getAdapter();
    }
}
