<?php
namespace Sma;

use Sma\Container;

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
    const CACHE_KEY = 'SMA_RELEASE';
    
    public static function getSmaVersion(): string
    {
        static $version = null;
        
        if ($version === null) {
            $version = Container::getCache()->get(self::CACHE_KEY);
            if (!$version) {
                $composer = self::getComposer();
                $version = str_replace('-dev', '-alpha', $composer['version']);
                Container::getCache()->set(self::CACHE_KEY, $version);
            }
        }
        
        return $version;
    }
    
    /**
     * Get composer.json content
     * @return array|null
     */
    public static function getComposer(): ?array
    {
        $composerFile = __DIR__ . '/../../composer.json';
        $composer = json_decode(file_get_contents($composerFile), true);
        return is_array($composer) ? $composer : null;
    }
}
