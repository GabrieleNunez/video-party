<?php namespace Library\Exceptions;

use Library\Exceptions\BaseException;

class UnknownException extends BaseException
{
    public function __construct($message)
    {
        parent::__construct('Unknown exception ' . $message, 'unknown');
    }
}

?>
