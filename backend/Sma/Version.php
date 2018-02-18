<?php
namespace Sma;

/**
 * SimpleManager version
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage version
 */
class Version
{
    private const SMA_VERSION = '1.0';
    private const SMA_SUBVERSION = 500; // Generated
    
    public static function getSmaVersion()
    {
        return self::SMA_VERSION . '.' . self::SMA_SUBVERSION;
    }
}
