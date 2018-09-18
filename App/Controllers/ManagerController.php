<?php namespace App\Controllers;

// include library dependencies
use Library\View;
use Library\Application;
use Library\Request;
use Library\Database;
use Library\Session;


// include application defined classes
use App\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Viewer;
use App\Models\PlayerSync;
use App\Models\ChatMessage;


// This class is responsible for handing off the transcodes and setting up the video party from a user friendly perspective
class ManagerController extends Controller { 

	// construct the controller and set controller wide variables right here
	public function __construct() {

		View::engine('php');


	}


	// show a manager view for someone who is marked as manager
	public function manager_get() {


		// if the user does not have a master ticket code then we don't allow them into this area
		// TODO: this should be tightened up significantly
		$ticket_code = Session::read('ticket_code');
		if(!$ticket_code !== false) {
			$ticket = Ticket::select(array('id'))->where('code', $ticket_code,'=')->where('master',1,'=')->limit(1)->get(true);
			if(!$ticket) {
				$this->redirect('/');
				exit;
			}
		}

		// grab all tickets from database
		$tickets = Ticket::select(array('*'))->where('deleted_at',0,'=')->get(true);
		$this->variable('tickets', $tickets);

		$this->variable('title','');
		$this->variable('maintab','manager');
		$this->content_view('/manager/manager.php');
		$this->render_template();
			
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