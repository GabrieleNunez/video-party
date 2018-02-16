<?php namespace App\Controllers;

// Anything we are pulling from our library include here
use Library\View;
use Library\Application;
use Library\Request;
use Library\Database;
use Library\Session;

// include library
use App\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\PlayerSync;
use App\Models\Ticket;

class SiteController extends Controller {


	// set constant controller wide variables here 
	public function __construct() {

		// set the view engine to be used for this request
		View::engine('php');

	}

	public function logout_get($in) {
		Session::flush();
		$this->redirect('/login');
	}

	// login
	public function login_get($in) {

		// load the ticket code from the session.
		$ticket_code = Session::read('ticket_code');
		if($ticket_code !== false) { // we found one, lets validate and redirect if its legit

			// search for the ticket
			$ticket = Ticket::select(array('id'))->where('code', $ticket_code)->limit(1)->get(true);
			if($ticket) {
				$this->redirect('/');
				exit;
			}


		}

		$this->variable('title','Level Crush - Member Lounge Login');
		$this->variable('maintab', 'login');
		$this->content_view('/site/login.php');
		$this->render_template();
	}

	// attempt to login and set session variables!
	public function login_post($in) {


		// we are expecting a response
		$request = new Request('POST', array('code'));
		$missing = $request->get_missing();
		if($missing) {
			$this->bad_request();
			exit;
		}

		// lightly validate the ticket code
		$ticket_code = $request->get('code');
		if(strlen($ticket_code) === 0)
			$this->error('code','Please specify a ticket code');

		// no errors lets hit the database
		if(!$this->has_errors()) {
			$ticket = Ticket::select(array('id'))->where('code', $ticket_code,'=')->limit(1)->get(true);
			if(!$ticket) {
				$this->error('code','This is not a valid ticket, sorry.');
			} else {
				Session::write('ticket_code',$ticket_code);
				$this->variable('validated',true);
			}
		}



		$this->render_json();

	}	

	// pulls up a home page
	public function home_get($in) {

		// set the following variables
		$this->variable('title', 'Level Crush - Member');

		// set the main tab to home
		$this->variable('maintab','');
		$this->variable('maintab','home');

		// make sure we are loaded with a valid ticket
		$ticket = array();
		$ticket_code = Session::read('ticket_code');
		if($ticket_code === false) {
			
			$this->redirect('/login');
			exit;

		} else {

			$ticket = Ticket::select(array('id','code','username','master'))->where('code',$ticket_code,'=')->limit(1)->get(true);
			if(!$ticket) {
				$this->redirect('/login');
				exit;
			}
		}

		// set ticket code
		$this->variable('ticket_master', $ticket['master'] ? true : false);
		$this->variable('ticket_username', $ticket['username']);
		$this->variable('ticket_code', $ticket_code);


		// player sync
		$player_sync = PlayerSync::select(array('*'))->limit(1)->get(true);	
		$this->variable('player_state', $player_sync['state']);
		$this->variable('player_time', $player_sync['current']);
		$this->variable('player_stream_file', $player_sync['stream_file']);


		// render template
		$this->content_view('/site/home.php');
		$this->render_template();


	}


	// 
	public function chat_get($in) {

		$last_message_id = 0;
		
		$request = new Request('GET');
		$last_message_id = $request->get('last_message_id');
		if(!$last_message_id) 
			$last_message_id = 0;
		
		// load messages
		$messages = array();
		$message = new ChatMessage();

		$messages = ChatMessage::raw('
									SELECT * FROM
									(
										SELECT 
											tickets.username, 
											chat_messages.id AS message_id, 
											chat_messages.message,
											chat_messages.created_at
										 FROM chat_messages
										 INNER JOIN tickets ON chat_messages.ticket = tickets.id
										 WHERE tickets.deleted_at = 0 
										 AND chat_messages.deleted_at = 0
										 AND chat_messages.id > "'.Database::escape($last_message_id).'"
										 ORDER BY chat_messages.id DESC
										 LIMIT 30
									) AS messages
									ORDER BY message_id ASC
									');


		$this->variable('messages', $messages);
		$this->render_json();
	}

	//
	public function chat_post($in) {


		$request = new Request('POST', array('ticket','message'));
		$missing = $request->get_missing();
		if($missing) {
			$this->bad_request(true);
			exit;
		}

		$ticket_id = $request->get('ticket');
		$message = $request->get('message');

		// lightly parse message
		if($message === null || strlen($message) === 0)
			$this->error('message','The submitted message must contain at least one character');
		elseif(strlen($message) > 255)
			$this->error('message','This message must not exceed 255 characters');


		if($ticket_id === null || strlen($ticket_id) === 0)
			$this->error('ticket', 'Please submit a valid ticket id');



		// find the ticket information
		$ticket = array();
		if(!$this->has_errors()) {
			$ticket = Ticket::select(array('id'))->where('code', $ticket_id)->limit(1)->get(true);
			if(!$ticket)
				$this->error('ticket','Please submit a valid ticket id');

		}

		if(!$this->has_errors()) {

			$chat_message = new ChatMessage();
			$chat_message->assign(array(
				'id' => null,
				'ticket' => $ticket['id'],
				'message' => $message,
				'created_at' => $_SERVER['REQUEST_TIME'],
				'deleted_at' => 0
			));
			$chat_message->save();
			$chat_message = $chat_message->to_array();

			$this->variable('saved_message', $chat_message);
		}



		$this->render_json();

	}

	// sync get
	public function sync_get($in) {

		
		// player sync
		$player_sync = PlayerSync::select(array('*'))->limit(1)->get(true);	
		$this->variable('state', $player_sync['state']);
		$this->variable('time', $player_sync['current']);
		$this->variable('stream_file',$player_sync['stream_file']);
		$this->render_json();

	}

	// update player syncronization status0
	public function sync_post($in) {

		$request = new Request('POST', array('current', 'state'));
		$missing = $request->get_missing();
		if($missing) {
			$this->bad_request(true);
			exit;
		}

		// find the ticket code matching the session
		$ticket_code = Session::read('ticket_code');
		$ticket = Ticket::select(array('*'))->where('code', $ticket_code,'=')->where('master','1')->limit(1)->get(true);
		if(!$ticket) {
			$this->bad_request();
			exit;
		}

		// update player information
		$player_sync = PlayerSync::select(array('*'))->limit(1)->get();
		$player_sync->current = $request->get('current');
		$player_sync->state = $request->get('state');
		$player_sync->save();

		$player_sync = $player_sync->to_array();
		$this->variable('state', $player_sync['state']);
		$this->variable('time', $player_sync['current']);
		$this->variable('stream_file',$player_sync['stream_file']);


		$this->render_json();
	}

}


?>