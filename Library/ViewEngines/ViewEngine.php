<?php namespace Library\ViewEngines;

// Abstract View Engine class
abstract class ViewEngine {

	abstract public function render(); // render the view stack
	abstract public function with($key, $value = null); // pass variables to the view stack
	abstract public function clear(); // clear variables off the view stack 

}
?>