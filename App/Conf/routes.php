<?php namespace App;

use Library\Application;
use Library\Router;
use Library\ErrorHandler;
use Library\Exceptions\RouteException;
use Library\Request;
// define our routes

// Basic Site routes
Router::get('/', 'SiteController@home_get');
Router::get('/chat','SiteController@chat_get');
Router::post('/chat','SiteController@chat_post');

// Generic bad route exception
ErrorHandler::Hook('RouteException', function($exception) {
	if(Request::isAjax()){
		header('Content-type: application/json');
		header('Cache-Control: no-cache, must-revalidate');
		echo json_encode(array('success' => false, 'response' => array(), 'errors' => array('url' => 'This url does not exist')), JSON_FORCE_OBJECT);
		exit;
	} else {
		header('Location: /');
		exit;
	}
});

// if we are in debug mode this additional routes are available for testing
if(Application::setting('debug')) {

	// TODO ADD debug routes



}

?>