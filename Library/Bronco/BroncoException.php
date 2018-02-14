<?php namespace Library\Bronco;

use Library\Exceptions\BaseException;

class BroncoException extends BaseException{
	public function __construct($message){
		parent::__construct('Bronco Exception: '.$message);
	}
}
?>