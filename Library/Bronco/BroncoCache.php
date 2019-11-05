<?php namespace Library\Bronco;

use Library\CacheEngines\MemcachedEngine;

// Bronco class that helps load into and out of the current cache system
class BroncoCache
{
    private static $enable = false;

    // enables or disables the cache
    public static function enable($enable)
    {
        self::$enable = $enable;
    }

    // determines if the file exist in cache
    public static function exist($cache_file)
    {
        return self::$enable ? file_exists($cache_file) : false;
    }

    // write file into the cache
    public static function write($contents, $cache_file)
    {
        if (self::$enable) {
            // write file
            $handle = fopen($cache_file, 'wb+');
            flock($handle, LOCK_EX);

            fwrite($handle, $contents);
            fflush($handle);

            flock($handle, LOCK_UN);
            fclose($handle);

            // write to memcache
            //MemcachedEngine::set($cache_file, $contents);
        }
    }

    // load from the cache.
    /*
    private static function cacheLoad($cache_file) {
        if(!MemcachedEngine::exist($cache_file))
            MemcachedEngine::set($cache_file, file_get_contents($cache_file));
		
        return MemcachedEngine::get($cache_file);
    } */

    // read a file from the cache
    public static function read($cache_file)
    {
        return self::$enable ? file_get_contents($cache_file) : '';
    }
}
?>
