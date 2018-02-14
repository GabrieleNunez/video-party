<?php namespace App\Classes;

use Library\Application;

// represents a captcha code that is supplied
class RecaptchaResponse {

	private $response_code = '';
	private $verified = false;
	private $verification_endpoint = 'https://www.google.com/recaptcha/api/siteverify';

	// construct the captcha mechanism
	public function __construct($response_code) {
		$this->response_code = $response_code;
		$this->verified = false;
	}

	// determines if we have verified the captcha code
	// Note: This does not make the POST call verification
	// If you do not call verify() first then this WILL always return false
	public function is_verified() {
		return $this->verified;
	}

	public function verify() {

		$recaptcha_settings = Application::setting('recaptcha');
		$post_fields = array(
			'secret' => $recaptcha_settings['key_secret'],
			'response' => $this->response_code
		);

		// generate a url encoded string 
		$post_fields_string = '';
		foreach($post_fields as $field => $val)
			$post_fields_string .= urlencode($field).'='.urlencode($val).'&';
		$post_fields_string = rtrim($post_fields_string,'&');


		// initiate the curl request
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->verification_endpoint);
		curl_setopt($curl, CURLOPT_POST, count($post_fields));
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields_string);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		// make the web request
		$curl_result = curl_exec($curl);
		curl_close($curl);

		// TODO implement error handling

		// decode the curl result into a json response
		$json_response = json_decode($curl_result, true);
		$this->verified = $json_response['success'] ? true : false;
		
		return $this->verified;

	}

}

?>