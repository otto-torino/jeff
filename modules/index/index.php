<?php

class index extends model {

	public $title,$text,$image;

	function __construct($registry, $id) {

		$this->_registry = $registry;
	
		$this->init($id);
	}

	private function init($id) {

		$this->title = null;
		$this->text = null;
		$this->image = null;

	}
}
 
?>
