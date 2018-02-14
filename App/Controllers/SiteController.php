<?php namespace App\Controllers;

// Anything we are pulling from our library include here
use Library\View;
use Library\Application;
use Library\Request;
use Library\Database;

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


	// pulls up a home page
	public function home_get($in) {

		// set the following variables
		$this->variable('title', 'Level Crush - Movie');

		// set the main tab to home
		$this->variable('maintab','');
		$this->variable('maintab','home');

		// for now hardcode everyone to be this code
		$ticket = Ticket::select(array('code'))->where('id',1,'=')->limit(1)->get(true);
		$ticket_code = $ticket['code'];
		$this->variable('ticket_code', $ticket_code);

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
										 LIMIT 10
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


}


?>