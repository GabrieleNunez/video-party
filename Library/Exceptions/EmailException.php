<?php namespace Library\Exceptions;

use Library\Exceptions\BaseException;

class EmailException extends BaseException
{
    public function __construct($message)
    {
        parent::__construct('Email: ' . $message, 'email');
    }
}

?>
