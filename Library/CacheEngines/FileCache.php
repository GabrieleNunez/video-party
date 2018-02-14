<?php  namespace Library\CacheEngines;
class FileCache {
    
    private $directory = '';
    private $known_files = array();
    
    // construct the file cache information
    public function __construct($directory) {
        $this->directory = $directory;
    } 
    
    private function update_known() {
        $handle = opendir($this->directory);
        while(($file = readdir($handle)) !== false) {
            if($file != '.' || $file != '..') {
                $file_path =  $this->directory.DIRECTORY_SEPARATOR.$file;
                $this->known_files[$file_path] = true;
            }
        }
        closedir($handle);
    }
    
    // file exist in our cache location
    public static function exist($file_path) {
        return true;
    }
    
}
?>