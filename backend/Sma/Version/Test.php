<?php
namespace Sma\Version;

use Osf\Test\Runner as OsfTest;
use Sma\Container;
use Sma\Version;

/**
 * Version extractor test
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage test
 */
class Test extends OsfTest
{
    public static function run()
    {
        self::reset();
        
        // Composer
        $composer = Version::getComposer();
        self::assert(is_array($composer));
        self::assert(array_key_exists('version', $composer));
        self::assert(preg_match('/^[0-9]+\.[0-9]+\.[0-9]+(\-(dev|alpha|beta|rc[0-9]+))?$/', $composer['version']));
        
        // Version
        self::assert(is_int(Container::getCache()->clean(Version::CACHE_KEY)));
        $version = Version::getSmaVersion();
        self::assert(preg_match('/^[0-9]+\.[0-9]+\.[0-9]+(\-(alpha|beta|rc[0-9]+))?$/', $version));
        self::assertEqual($version, str_replace('-dev', '-alpha', $composer['version']));
        
        return self::getResult();
    }
}
