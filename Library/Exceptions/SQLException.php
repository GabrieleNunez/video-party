<?php namespace Library\Exceptions; 


use Library\Exceptions\BaseException;
class SQLException extends BaseException {

	private $type = 'sql';
	public function __construct($message, $type='sql'){
		$this->$type = $type;
		$this->message = $message;
	}

	public function __toString(){
		return $this->message;
	}
}
?>