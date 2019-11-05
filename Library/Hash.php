<?php namespace Library;

// Based off  Laravel 5.0 Bcrypt hasher
class Hash
{
    protected static $rounds = 10; // How many iterations we should utilize, the more rounds the more expensive

    // Hash the value with given options
    public static function make($value, array $options = array())
    {
        $cost = isset($options['rounds']) ? $options['rounds'] : self::$rounds;
        $hash = password_hash($value, PASSWORD_BCRYPT, array('cost' => $cost));
        if ($hash === false) {
            trigger_error('Bcrypt hashing not supported', E_USER_ERROR);
        } // Throw new RuntimeException("Bcrypt hashing not supported.");
        return $hash;
    }

    // Check the password
    public static function check($value, $hashedValue, array $options = array())
    {
        return password_verify($value, $hashedValue);
    }

    // See if the password it needs to be rehashed
    public static function needsRehash($hashedValue, array $options = array())
    {
        $cost = isset($options['rounds']) ? $options['rounds'] : self::$rounds;
        return password_needs_rehash($hashedValue, PASSWORD_BCRYPT, array('cost' => $cost));
    }

    // Set the cost factor
    public static function setRounds($rounds)
    {
        self::$rounds = (int) $rounds;
        return self::$rounds;
    }

    // generates a quick 40 character hash
    public static function generate()
    {
        return sha1(uniqid() . rand(0, 255));
    }
}

?>
