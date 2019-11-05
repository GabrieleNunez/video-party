<?php namespace Library;
class Printout
{
    public static function write($data)
    {
        echo '<xmp>';
        print_r($data);
        echo '</xmp>';
    }
}
?>
