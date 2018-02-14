<?php namespace Library\Gateways;

abstract class Gateway {

	private $opened = false;

	// runs the internal link code to link our code to a 3rd party codebase
	abstract protected function unlock();

	public function open() {

		if(!$this->opened) { // we are not linked go ahead and utilize 
			$this->unlock();
			$this->opened = true;
		}

	}
}
?>