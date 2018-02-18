<?php

use Sma\Container;
use Osf\Container\AbstractStaticContainer;

/**
 * Cache quick access
 *
 * This class is generated, do not edit it
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class C extends AbstractStaticContainer
{

    /**
     * @param string $category
     * @param string $type
     * @param int $id
     * @param string $content
     * @return \Sma\Cache
     */
    public static function setItem(string $category, string $type, int $id, string $content, int $idAccount = null)
    {
        return Container::getCacheSma('cache')->setItem($category, $type, $id, $content, $idAccount);
    }

    /**
     * @param string $category
     * @param string $type
     * @param int $id
     * @param int|null $idAccount
     * @return string
     */
    public static function getItem(string $category, string $type, int $id, int $idAccount = null)
    {
        return Container::getCacheSma('cache')->getItem($category, $type, $id, $idAccount);
    }

    /**
     * @param string $category
     * @param int $id
     * @param int|null $idAccount
     * @return \Sma\Cache
     * @task clean pour tous les devices
     */
    public static function cleanItem(string $category, int $id, int $idAccount = null)
    {
        return Container::getCacheSma('cache')->cleanItem($category, $id, $idAccount);
    }

    /**
     * Suppression de toutes les clés d'un utilisateur donné
     * @param int|null $idAccount
     * @return \Sma\Cache
     */
    public static function cleanUserCache(int $idAccount = null)
    {
        return Container::getCacheSma('cache')->cleanUserCache($idAccount);
    }

    /**
     * PSR6: Persists data in the cache, uniquely referenced by a key with an optional
     * expiration TTL time.
     * @param string $key
     * @param mixed $value
     * @param null|int|DateInterval $ttl
     * @return bool
     */
    public static function set($key, $value, $ttl = null)
    {
        return Container::getCacheSma('cache')->set($key, $value, $ttl);
    }

    /**
     * PSR6: Fetches a value from the cache.
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return Container::getCacheSma('cache')->get($key, $default);
    }

    /**
     * PSR6: Delete an item from the cache by its unique key.
     * @param string $key
     * @return bool
     */
    public static function delete($key)
    {
        return Container::getCacheSma('cache')->delete($key);
    }

    /**
     * PSR6: Wipes clean the entire cache's keys.
     * @return bool
     */
    public static function clear()
    {
        return Container::getCacheSma('cache')->clear();
    }

    /**
     * PSR6: Obtains multiple cache items by their unique keys.
     * @param iterable $keys
     * @param mixed $default
     * @return iterable
     */
    public static function getMultiple($keys, $default = null)
    {
        return Container::getCacheSma('cache')->getMultiple($keys, $default);
    }

    /**
     * PSR6: Persists a set of key => value pairs in the cache, with an optional TTL.
     * @param iterable $values
     * @param null|int|DateInterval $ttl
     * @return bool
     */
    public static function setMultiple($values, $ttl = null)
    {
        return Container::getCacheSma('cache')->setMultiple($values, $ttl);
    }

    /**
     * PSR6: Deletes multiple cache items in a single operation.
     * @param iterable $keys
     * @return bool
     */
    public static function deleteMultiple($keys)
    {
        return Container::getCacheSma('cache')->deleteMultiple($keys);
    }

    /**
     * PSR6: Determines whether an item is present in the cache.
     * @param string $key The cache item key.
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function has($key)
    {
        return Container::getCacheSma('cache')->has($key);
    }

    /**
     * Start cache via buffer
     * @param string $key
     * @return \Sma\Cache
     */
    public static function start(string $key)
    {
        return Container::getCacheSma('cache')->start($key);
    }

    /**
     * Stop buffer and put it in cache
     * @param float $timeout
     * @return \Sma\Cache
     */
    public static function stop(float $timeout = 0)
    {
        return Container::getCacheSma('cache')->stop($timeout);
    }

    /**
     * Clean all values of the current namespace
     * @return int Number of deleted fields
     */
    public static function cleanAll()
    {
        return Container::getCacheSma('cache')->cleanAll();
    }

    /**
     * Call Redis::del()
     * @param string $key
     * @return int Number of deleted fields
     */
    public static function clean(string $key)
    {
        return Container::getCacheSma('cache')->clean($key);
    }

    /**
     * @return \Redis
     */
    public static function getRedis()
    {
        return Container::getCacheSma('cache')->getRedis();
    }

    /**
     * Build and return a zend cache storage using OSF cache configuration
     * @staticvar array $storages
     * @param string $namespace
     * @return \Zend\Cache\Storage\Adapter\Redis
     */
    public static function getZendStorage(string $namespace = 'default')
    {
        return Container::getCacheSma('cache')->getZendStorage($namespace);
    }

}