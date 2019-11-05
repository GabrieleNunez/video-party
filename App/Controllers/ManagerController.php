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
class ManagerController extends Controller
{
    // construct the controller and set controller wide variables right here
    public function __construct()
    {
        View::engine('php');
    }

    // show a manager view for someone who is marked as manager
    public function manager_get()
    {
        // if the user does not have a master ticket code then we don't allow them into this area
        // TODO: this should be tightened up significantly
        $ticket_code = Session::read('ticket_code');
        if (!$ticket_code !== false) {
            $ticket = Ticket::select(array('id'))
                ->where('code', $ticket_code, '=')
                ->where('master', 1, '=')
                ->limit(1)
                ->get(true);
            if (!$ticket) {
                $this->redirect('/');
                exit();
            }
        }

        // grab all tickets from database
        $tickets = Ticket::select(array('*'))
            ->where('deleted_at', 0, '=')
            ->get(true);
        $this->variable('tickets', $tickets);

        $this->variable('title', '');
        $this->variable('maintab', 'manager');
        $this->content_view('/manager/manager.php');
        $this->render_template();
    }

    // a visual way to see all video information will go here
    public function videos_get()
    {
    }

    // adding and converting video logic will go here
    public function videos_post()
    {
    }

    // visually showing the playlist will go here
    public function playlist_get()
    {
    }

    // change made to playlist will be processed here
    public function playlist_post()
    {
    }

    // whenever we do a get request for tickets return all o
    public function tickets_post()
    {
        // make sure we have a valid request
        $request = new Request('POST', array(
            'ticket_id',
            'ticket_new',
            'ticket_user',
            'ticket_code',
            'ticket_updated',
            'ticket_deleted'
        ));
        $missing = $request->get_missing();
        if ($missing) {
            $this->bad_request();
            exit();
        }

        // TODO: this should be tightened up significantly
        $ticket_code = Session::read('ticket_code');
        if (!$ticket_code !== false) {
            $ticket = Ticket::select(array('id'))
                ->where('code', $ticket_code, '=')
                ->where('master', 1, '=')
                ->limit(1)
                ->get(true);
            if (!$ticket) {
                $this->redirect('/');
                exit();
            }
        }

        // retrieve our flags for the ticket
        $ticket_update = $request->get('ticket_updated');
        $ticket_delete = $request->get('ticket_deleted');
        $ticket_new = $request->get('ticket_new');

        // grab and validate/clean all of our fields
        $ticket_id = $request->get('ticket_id');
        if (!strlen($ticket_id)) {
            $this->error('ticket_id', 'Please specify a valid ticket id');
        }

        // ticket username
        $ticket_username = $request->get('ticket_user');
        if (!strlen($ticket_username)) {
            $this->error('ticket_user', 'Please specify a username length');
        } else {
            $ticket_username = strip_tags($ticket_username);
            $ticket_username = preg_replace('/[^a-z0-9+]+/i', '', $ticket_username); // we are going to strip out any of this data
        }

        // ticket code
        $ticket_code = $request->get('ticket_code');
        if (!strlen($ticket_code)) {
            $this->error('ticket_code', 'Please specify a ticket code');
        } else {
            $ticket_code = strip_tags($ticket_code);
            $ticket_code = preg_replace('/[^a-z0-9+]+/i', '', $ticket_code);
        }

        if (!$this->has_errors()) {
            $assigned_ticket = array();
            if ($ticket_new) {
                $assigned_ticket = new Ticket();
                $assigned_ticket->assign(array(
                    'id' => null,
                    'master' => 0,
                    'username' => $ticket_username,
                    'code' => $ticket_code,
                    'created_at' => $_SERVER['REQUEST_TIME'],
                    'deleted_at' => 0
                ));
                $assigned_ticket->save();
                $assigned_ticket = $assigned_ticket->to_array();
            } elseif ($ticket_update) {
                $assigned_ticket = Ticket::select(array('*'))
                    ->where('id', $ticket_id, '=')
                    ->limit(1)
                    ->get();
                $assigned_ticket->username = $ticket_username;
                $assigned_ticket->code = $ticket_code;
                $assigned_ticket->save();
                $assigned_ticket = $assigned_ticket->to_array();
            } elseif ($ticket_delete) {
                $assigned_ticket = Ticket::select(array('*'))
                    ->where('id', $ticket_id, '=')
                    ->limit(1)
                    ->get();
                $assigned_ticket->delete('id', $assigned_ticket->id);
                $assigned_ticket = $assigned_ticket->to_array();
            }

            $ticket_html = '';
            if ($assigned_ticket && !$ticket_delete) {
                $ticket_html = View::make('/manager/ticket.php')
                    ->with(array('ticket' => $assigned_ticket))
                    ->render();
            }

            $this->variable('ticket_html', $ticket_html);
            $this->variable('ticket', $assigned_ticket);
        }

        // send back down the flags we sent up
        $this->variable('flags', array(
            'ticket_update' => $ticket_update ? true : false,
            'ticket_delete' => $ticket_delete ? true : false,
            'ticket_new' => $ticket_new ? true : false
        ));

        $this->render_json();
    }
}

?>
