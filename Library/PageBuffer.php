<?php namespace Library;

// Simple page buffer class
class PageBuffer
{
    private static $page_route = '';

    // create the page buffer
    public static function start()
    {
        self::$page_route = $_SERVER['REQUEST_URI'];
        if (self::cache_expired(self::$page_route)) {
            ob_start();
        } else {
            // not expired force render out the current page and kill the script.
            echo self::load(self::$page_route);
            exit();
        }
    }

    public static function cache_expired($route, $expire_time_seconds = 10)
    {
        $filepath = realpath(self::get_full_filepath($route));
        $elapsed_time = is_file($filepath) ? time() - filemtime($filepath) : $expire_time_seconds + 1;
        return $elapsed_time < $expire_time_seconds ? false : true;
    }

    // load a specific page buffer
    public static function load($route)
    {
        $route_filepath = realpath(self::get_full_filepath($route));
        return file_get_contents($route_filepath);
    }

    // end the current output buffer
    public static function end()
    {
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    public static function get_filename($route)
    {
        return md5($route);
    }

    private static function get_full_filepath($route)
    {
        $route_filepath = Application::setting('viewcache') . '/' . self::get_filename($route) . '.page_buffer';
        return $route_filepath;
    }

    // end and save at the same time
    public static function end_save()
    {
        $contents = self::end();
        self::save($contents, self::$page_route);
        return $contents;
    }

    // save page buffer
    public static function save($contents, $route)
    {
        $filepath = self::get_full_filepath($route);
        $handle = fopen($filepath, 'wb+');
        flock($handle, LOCK_EX);
        fwrite($handle, $contents);
        fflush($handle);
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}

?>
