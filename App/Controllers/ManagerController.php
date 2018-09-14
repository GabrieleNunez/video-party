<?php namespace App\Controllers;

// include library dependencies
use Library\View;
use Library\Application;
use Library\Request;
use Library\Database;
use Library\Session;


// include application defined classes
use App\Controllers\Controller;


// This class is responsible for handing off the transcodes and setting up the video party from a user friendly perspective
class ManagerController extends Controller { 


	// construct the controller and set controller wide variables right here
	public function __construct() {

		View::engine('php');

	}


	// show a manager view for someone who is marked as manager
	public function manager_get() {

	}


	// a visual way to see all video information will go here
	public function videos_get() {

	}

	// adding and converting video logic will go here
	public function videos_post() {

	}

	// visually showing the playlist will go here
	public function playlist_get() {

	}

	// change made to playlist will be processed here
	public function playlist_post() {

	}

}



?>