<?php namespace Library;

use Library\Exceptions\UnknownException;

class ErrorHandler{

	private static $debug = true;
	private static $hooks = array();

	public static function Register($debug = true){
		if($debug) // local. Allow  force all errors on
			error_reporting(E_ALL);

		self::$debug = $debug;
		set_error_handler('Library\ErrorHandler::Handle');
	}

	public static function isDebug(){
		return self::$debug;
	}

	public static function Hook($type,$callback){
		if(is_array($type)) {
			foreach($type as $error_type)
				self::$hooks[$error_type] = $callback;
		} else {
			self::$hooks[$type] = $callback;
		}
	}

	public static function HasHook($type){
		return isset(self::$hooks[$type]);
	}

	public static function ExecuteHook($type,$exception){
		$callback = self::$hooks[$type];
		$callback($exception);
	}


	public static function Handle($errno ,$errstr, $errfile, $errline, $errcontext){
		$stack = print_r($errcontext, true); // store in variable instead of showing on screen
		$file = basename($errfile);
		$message = sprintf("%s Line %u: %s \n Stack: %s", $file, $errline, $errstr, $stack);
		if(self::$debug){
			if($errno == E_USER_NOTICE)
				echo '<xmp>'.$message.'</xmp>';
			else {
				echo '<xmp>'.$message.'</xmp>';
				exit; 
			}
		} else{
			echo 'An unknown error has occurred. Please contact system administrators';
			exit;
		}
		return true; // never execute the internal php handler
	}
}

?>