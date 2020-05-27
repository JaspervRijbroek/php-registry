<?php

declare(strict_types=1);

namespace Library;

class Cache
{
    private static $cache = [];

    /**
     * This method will cache a value, if the value already exists, it will be overwritten.
     *
     * @param string $key The key name under which to cache
     * @param mixed $data The value to cache.
     * @param string $ttl The lifetime of the cache written in human readable state like +5 hours.
     * @return bool The result of the cache action.
     */
    public static function add(string $key, $data, $ttl = '+1 day'): bool
    {
        $timestamp = strtotime($ttl);
        $key = self::hashKey($key); // I know sha1 isn't secure however, this filename does not need to be secure.
        $dirname = self::getKeyCacheDir($key);
        $data = serialize([
            'data' => $data,
            'expires_at' => $timestamp
        ]);

        if(!file_exists($dirname)) {
            @mkdir($dirname, 0777, true);
        }

        file_put_contents($dirname . '/' . $key, $data);

        return true;
    }

    /**
     * This method checks if a cache item already exists.
     *
     * @param string $key The key to check its existence for.
     * @return bool Result if the cache item exists.
     */
    public static function has(string $key)
    {
        return !!self::get($key);
    }

    /**
     * @param string $key The key to get the value from.
     * @return bool|mixed False if the cache item doesn't exist/ is invalid, or the value when it does
     */
    public static function get(string $key)
    {
        $key = self::hashKey($key);
        $dirname = self::getKeyCacheDir($key);
        $filename = $dirname . '/' . $key;

        if(!isset(self::$cache[$key])) {
            if(!file_exists($filename)) {
                return self::$cache[$key] = false;
            }

            // Get the file and check if the timestamp is bigger than now.
            $contents = file_get_contents($filename);
            $contents = unserialize($contents);

            if($contents['expires_at'] < time()) {
                return self::$cache[$key] = false;
            }

            self::$cache[$key] = $contents['data'];
        }

        return self::$cache[$key];
    }

    /**
     * Dedicated method to hash the key.
     *
     * @param string $key The key to hash
     * @return string The hashed key
     */
    private static function hashKey(string $key): string
    {
        return sha1($key);
    }

    /**
     * This method will return the method for the current cache entry.
     *
     * @param string $key The key to get the cache directory from.
     * @return string The directory to cache too.
     */
    private static function getKeyCacheDir(string $key): string
    {
        return implode('/', [Config::get('cache.path'), substr($key, 0, 2)]);
    }
}