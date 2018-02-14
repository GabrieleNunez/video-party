<?php namespace Library;

use Library\Session;
use Library\Exceptions\AuthException;

class Auth {
	
	public static function login($user) {
		// logging in? Flush the session just in case 
		Session::flush();
		Session::write('user', $user);
		Session::write('user_id', $user->id);	
	}
	
	public static function logout() {
		Session::flush();
	}
	
	public static function user() {
		return Session::read('user');
	}
	
	public static function check() {
		return Session::read('user_id') ? true : false;
	}
}
?>