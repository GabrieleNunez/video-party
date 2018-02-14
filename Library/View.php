<?php namespace Library;

// Shortcut to whatever engine
class View  {
	private static $engine = 'bronco';

	// set the engine to the specific
	public static function engine($engine = 'bronco') {
		self::$engine = $engine;
	}

	public static function make($viewname, $engine = false) {

		// determine if we need to provide our own engine type or use the specified one
		$engine = $engine === false ? self::$engine : $engine;

		switch(strtolower($engine)){
			case 'php':
				return \Library\ViewEngines\PhpViewEngine::make($viewname);
				break;
			case 'mustache':
				return \Library\ViewEngines\MustacheViewEngine::make($viewname);
				break;
			case 'bronco':
				return \Library\ViewEngines\BroncoViewEngine::make($viewname);
				break;
			default:
				return \Library\ViewEngines\BroncoViewEngine::make($viewname);
				break;
		}
	} 
}

?>