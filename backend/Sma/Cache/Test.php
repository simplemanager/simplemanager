<?php
namespace Sma\Cache;

use Osf\Test\Runner as OsfTest;
use Sma\Container;

/**
 * Test du cache
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
        
        $cache = Container::getCacheSma();
        self::assert($cache instanceof \Sma\Cache, get_class($cache));
        
        $data1 = str_shuffle('simplemanager');
        $data2 = str_shuffle('openstates');
        
        $cache->setItem('C', 'A', 1, $data1, 1000);
        $cache->setItem('C', 'B', 1, $data2, 1000);
        self::assertEqual($cache->getItem('C', 'A', 1), $data1);
        self::assertEqual($cache->getItem('C', 'B', 1), $data2);
        self::assertEqual($cache->getRedis()->exists($cache::getItemKey('C', 1)), true);
        
        $cache->cleanItem('C', 1);
        self::assertEqual($cache->getItem('C', 'A', 1), '');
        self::assertEqual($cache->getItem('C', 'B', 1), '');
        self::assertEqual($cache->getRedis()->exists($cache::getItemKey('C', 1)), false);
        
        return self::getResult();
    }
}
