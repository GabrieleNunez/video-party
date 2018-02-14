<?php namespace Library;
class Autoloader{

	private static $folders = array();
	
	public static function Register(){
		spl_autoload_register('Library\Autoloader::Find');
	}

	private static function Find($name){

		$parts = explode('\\',$name);
		$path = implode(DIRECTORY_SEPARATOR, $parts);
		$root = dirname(__DIR__);
		$file = $root.DIRECTORY_SEPARATOR.$path.'.php';
		if(file_exists($file)) {
			include_once($file);
		}
	}
}