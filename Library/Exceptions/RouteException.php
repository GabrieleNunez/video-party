<?php namespace Library\Exceptions;

use Library\Exceptions\BaseException;

class RouteException extends BaseException{
	
	public function __construct($message){
		parent::__construct('Route Exception: '.$message, 'request');
	}
}

?>