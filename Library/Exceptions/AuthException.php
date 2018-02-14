<?php namespace Library\Exceptions;

use Library\Exceptions\BaseException;

class AuthException extends BaseException{
	
	public function __construct($message){
		parent::__construct('Authentication: '.$message, 'auth');
	}
}

?>