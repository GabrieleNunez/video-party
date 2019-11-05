<?php namespace Library;
class Session
{
    private static $started = false;

    public static function start()
    {
        if (!self::$started) {
            session_start();
            self::$started = true;
        }
    }

    public static function write($key, $data)
    {
        self::start();
        $_SESSION[$key] = $data;
    }

    public static function read($key)
    {
        self::start();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
    }

    public static function delete($key)
    {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function flush()
    {
        self::start();

        session_destroy();
        self::$started = false;
    }
}
?>
