<?php namespace Library;

require_once('Autoloader.php');
require_once('Password.php');

use Exception;
use Library\Exceptions\UnknownException;
use Library\Exceptions\ApplicationException;

class Application {
	
	// location of the enviorment setting
	private static $envPath = '../App/Conf/env.php';
	
	// folders and files relevant to our application
	private static $folders = array();
	private static $files = array();
	private static $vendors = array();

	private static $env = null; // all the enviorment settings in one array
	private static $debug = true;

	public static function Initialize($autoload_routes = true) {
	
		// load our application setti1ngs
		self::$env = self::loadEnvSettings();
		self::$folders = self::$env['folders'];
		self::$files = self::$env['files'];
		self::$debug = self::$env['debug'];
		self::$vendors = self::$env['vendor'];
		
		// initialize our services
		Autoloader::Register();
		ErrorHandler::Register(self::$debug);
		Database::LoadInfo();
		Session::start();

		// auto create the view cache folder if neccessary
		if(isset(self::$env['viewcache'])) {
			if(!is_dir(self::$env['viewcache']))
				mkdir(self::$env['viewcache'],  0777, true);
		}

		if($autoload_routes){
			try {
				Router::LoadRoutes();
				Router::Run();
				
			} catch(Exception $exception) {

				
				$class_bits = get_class($exception);
				$parts = explode('\\',$class_bits);
				$class = end($parts);
				
				if(ErrorHandler::HasHook($class)){
					ErrorHandler::ExecuteHook($class,$exception);
				} elseif (self::$debug) {
					\Library\Printout::write($exception);
				}
				
			}
		}
	}
	
	
	// load the env settings through the file
	private static function loadEnvSettings() {
		require_once(self::$envPath);
		return $env;
	}
	
	// establish a connection to the specified database
	public static function connection($connection = 'default'){
		return Database::connection($connection);
	}
	
	// retrieve a setting
	public static function setting($name) {
		$value = isset(self::$env[$name]) ? self::$env[$name] : null;
		if($value === null)
			throw new ApplicationException('Unable to find setting: "'.$name.'"');
		
		return $value;
	}
	
	// loads the specified folder path
	public static function folder($name) {
		$path = isset(self::$folders[$name]) ? self::$folders[$name] : null;
		if($path === null)
			throw new ApplicationException('Unable to find folder "'.$name.'"');
		return $path;
	}

	// load the specified file path
	public static function file($name) {
		$path = isset(self::$files[$name]) ? self::$files[$name] : null;
		if($path === null)
			throw new ApplicationException('Unable to find file "'.$name.'"');
		return $path;
	}

	// deals with loading vendor library
	public static function vendor($name) {
		$path = isset(self::$vendors[$name]) ? self::$vendors[$name] : null;
		if($path === null)
			throw new ApplicationException('Unable to find vendor "'.$name.'"');
		return $path;
	}

}