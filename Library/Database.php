<?php namespace Library;

use mysqli;

class Database {
	
	// connections to the database
	private static $connections = array();
	private static $info = false;
	
	public static function connection($connection = 'default'){

		$database = isset(self::$info[$connection]) ? self::$info[$connection] : false;
		$established = isset(self::$connections[$connection]) && self::$connections[$connection]->ping();
		if(!$established){
			$sql  = new mysqli($database['host'],$database['user'], $database['pass'],$database['database']);
			if($sql->errno){
				trigger_error($sql->error,E_USER_ERROR);
				exit;
			}
			self::$connections[$connection] = $sql;
		}
		return self::$connections[$connection];
	}

	public static function escape($val) {
		return self::$connections['default']->real_escape_string($val);
	}

	public static function setInfo($info){
		if(self::$sql !== false){
			self::$sql->close();
			self::$sql = false;
		}

		self::$info = $info;
	}

	public static function LoadInfo(){
		require_once(Application::file('database'));
		self::$info = $info;	
		
	}
}

?>