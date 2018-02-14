<?php namespace Library\ViewEngines;

use Library\Application;
use Library\ViewEngines\ViewEngine;
use Library\Gateways\MustacheGateway;

// uses Mustache to render templates
class MustacheViewEngine extends ViewEngine {

	private static $mustache = null;
	private $variables = array();
	private $template = null;

	public function __construct($viewname) {

		if(self::$mustache === null) {
			$gateway = new MustacheGateway();
			$gateway->open();

			self::$mustache = new \Mustache_Engine(array(
				'template_class_prefix' => '__MustacheTemplates__',
				'cache' => realpath(Application::setting('viewcache')),
				'cache_lambda_templates' => true,
				'loader' => new \Mustache_Loader_FilesystemLoader(realpath(Application::folder('views'))),
				'partials_loader' => new \Mustache_Loader_FilesystemLoader(realpath(Application::folder('views').'/partials')),
				'helpers' => array('i18n' => function($text){
					// translate here
				}),
				'escape' => function($value) {
					return htmlspecialchars($value, ENT_COMPAT,'UTF-8');
				},
				'charset' => 'ISO-8859-1',
				'logger' => new \Mustache_Logger_StreamLogger('php://stderr'),
				'strict_callables' => true,
				'pragmas' => array(\Mustache_Engine::PRAGMA_FILTERS),
			));
		}
		
		$this->template = self::$mustache->loadTemplate($viewname);
	}

	// construct the view statically
	public static function make($viewname) {
		return new MustacheViewEngine($viewname);
	}

	// include variables
	public function with($key, $value = null) {
		if (is_array($key)) // If it is an array, lets merge our results with our variables table
			$this->variables = array_merge($this->variables,$key);
		else // Not array lets set the key value pair outselves
			$this->variables[$key] = $value; 
		return $this;
	}

	// clear stack variables
	public function clear() {
		$this->variables = array();
	}

	// render the view
	public function render() {
		return $this->template->render($this->variables);
	}

	public function __tostring() {
		return $this->render();
	}
}
?>