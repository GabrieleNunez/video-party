<?php namespace Library;
class Scrub
{
    // sanitize output
    public static function htmlClean($value)
    {
        return is_array($value) ? array_map('Library\Scrub::htmlClean', $value) : htmlspecialchars($value, ENT_QUOTES);
    }
}
?>
