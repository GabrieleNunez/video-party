<?php namespace Library\Exceptions;

use Exception;
class BaseException extends Exception {
	private $type = 'base';
	public function __construct($message, $type='base'){
		$this->$type = $type;
		$this->message = $message;
	}

	public function __toString(){
		return $this->message;
	}
}

?>