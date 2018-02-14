<?php namespace Library;
	
class Validator {


	private $errors = array();

	// Validate the incoming input data with the associated rules
	public function __construct(&$input, $rules) {

		$this->errors = array();
		while(($input_key = key($input)) !== null && !$this->errors) {
			
		}
	}

	public function validate_number($input, $parameters = array()) {
		return is_numeric($input);
	}

	public function validate_set($input, $parameters = array()) {
		return in_array($input, explode(',',$parameters[0]));
	}
}

?>