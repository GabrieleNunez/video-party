<?php namespace Library\Exceptions;

use Library\Exceptions\BaseException;

class BadRequestException extends BaseException
{
    public function __construct($message)
    {
        parent::__construct('Bad Request: ' . $message, 'request');
    }
}

?>
