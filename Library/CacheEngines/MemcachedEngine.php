<?php namespace Library\CacheEngines;

use Library\Application;
use Library\UnknownException;
use Library\CacheEngines\PhpCache;

// Memcache engine that will utilize memcache for caching. If memcache fails to connect PhpCache will be used
class MemcachedEngine {
    
    private static $cache = null;
    private static $fallback = false;
    private static $started = false;
    
    // start up the memcache engine 
    public static function start() {
        if(!self::$started) {
            if(self::$cache === null) {
                self::$started = true;
                //$memcached_settings = Application::setting('memcached');
               // self::$cache = memcache_connect($memcached_settings['host'],$memcached_settings['port']);
                self::$cache = false; // use fallback for now until memcache is figured out
                if(!self::$cache) {
                    self::$cache = new PhpCache();
                    self::$fallback = true;
                }
            }
        }
    }
   
   
   
    // exist in the cache
    public static function exist($key) {
        self::start();
            
        if(self::$fallback) {
            return self::$cache->exist($key);
        } else {
            $is_new = self::$cache->add($key, false);
            if($is_new !== false)
                self::$cache->delete($key);
                
            return $is_new === false;
        
        }
    }
    
    // write data into the memcached server
    public static function set($key, $contents) {
        self::start();
        self::$cache->set($key, $contents);
    }
       
    // read from the memcached server
    public static function get($key) {
        self::start();
        self::$cache->get($key);
    }
}

?>