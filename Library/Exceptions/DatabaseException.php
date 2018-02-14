<?php namespace Library\Exceptions;

use Exception;
class DataBaseException extends Exception {

	private $type = 'database';
	public function __construct($message, $type='database'){
		$this->$type = $type;
		$this->message = $message;
	}

	public function __toString(){
		return $this->message;
	}
}

?>