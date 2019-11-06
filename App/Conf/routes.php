<?php namespace App;

use Library\Application;
use Library\Router;
use Library\ErrorHandler;
use Library\Exceptions\RouteException;
use Library\Request;
// define our routes

// Basic Site routes
Router::get('/', 'SiteController@home_get');
Router::get('/chat', 'SiteController@chat_get');
Router::post('/chat', 'SiteController@chat_post');
Router::get('/sync', 'SiteController@sync_get');
Router::post('/sync', 'SiteController@sync_post');
Router::get('/login', 'SiteController@login_get');
Router::post('/login', 'SiteController@login_post');
Router::get('/logout', 'SiteController@logout_get');
Router::get('/viewerlist', 'SiteController@viewerlist_get');
Router::post('/viewerlist', 'SiteController@viewerlist_post');

// Manager routes
Router::get('/manager', 'ManagerController@manager_get');
Router::get('/manager/videos', 'ManagerController@videos_get');
Router::post('/manager/videos', 'ManagerController@videos_post');
Router::get('/manager/playlist', 'ManagerController@playlist_get');
Router::post('/manager/playlist', 'ManagerController@playlist_post');
Router::get('/manager/player', 'ManagerController@player_get');
Router::post('/manager/player', 'ManagerController@player_post');
Router::get('/manager/chat', 'ManagerController@chat_get');
Router::post('/manager/chat', 'ManagerController@chat_post');
// Router::get('/manager/tickets', 'ManagerController@tickets_get'); For now there is no need to have a route to a GET varient of tickets
Router::post('/manager/tickets', 'ManagerController@tickets_post');

// Generic bad route exception
ErrorHandler::Hook('RouteException', function ($exception) {
    if (Request::isAjax()) {
        header('Content-type: application/json');
        header('Cache-Control: no-cache, must-revalidate');
        echo json_encode(
            array(
                'success' => false,
                'response' => array(),
                'errors' => array('url' => 'This url does not exist')
            ),
            JSON_FORCE_OBJECT
        );
        exit();
    } else {
        $debug = Application::setting('debug');
        if ($debug) {
            \Library\Printout::write($exception);
            exit();
        }

        header('Location: /');
        exit();
    }
});

// if we are in debug mode this additional routes are available for testing
if (Application::setting('debug')) {
    // TODO ADD debug routes
}

?>
