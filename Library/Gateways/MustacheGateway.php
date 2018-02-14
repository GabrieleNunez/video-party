<?php namespace Library\Gateways;

use Library\Application;
use Library\Gateways\Gateway;

// Brings the 3rd party Mustache Library into our space
class MustacheGateway extends Gateway {

	// link to our vendor library
	protected function unlock() {

		$mustache_folder = realpath(Application::vendor('Mustache'));
		$autoloaderfile = $mustache_folder.DIRECTORY_SEPARATOR.'Autoloader.php';
		require_once($autoloaderfile);
		
		// load the mustache autoloader
		\Mustache_Autoloader::register();

	}
}

?>