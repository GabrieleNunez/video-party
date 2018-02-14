<?php namespace Library\CacheEngines;  
class PhpCache {
    
    private $memory_bucket = array();
   
    // exist in the cache 
    public function exist($key) {
        return isset($this->memory_bucket[$key]);
    }
    
    // write into the memory bucket
    public function set($key, $contents) {
        $this->memory_bucket[$key] = $contents;
    }
       
    // read from the memory bucket
    public function get($key) {
        return isset($this->memory_bucket[$key]) ? $this->memory_bucket[$key] : null;
    }
}
?>