<?php namespace App\Controllers;

// Anything we are pulling from our library include here
use Library\View;
use Library\Application;

// include library
use App\Controllers\Controller;


class SiteController extends Controller {


	// set constant controller wide variables here 
	public function __construct() {

		// set the view engine to be used for this request
		View::engine('php');

		// set the following variables
		$this->variable('title', 'Level Crush - Movie');
		$this->variable('maintab', '');



	}


	// pulls up a home page
	public function home_get($in) {

		// set the main tab to home
		$this->variable('maintab','');
		$this->variable('maintab','home');


		// render template
		$this->content_view('/site/home.php');
		$this->render_template();


	}


}


?>