<?php namespace Library\Exceptions;

use Library\Exceptions\BaseException;

class ApplicationException extends BaseException{
	public function __construct($message){
		parent::__construct('Application exception '.$message, 'unknown');
	}
}

?>