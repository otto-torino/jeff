<?php

class page extends model {

	function __construct($id) {
	
		parent::__construct($this->initP());

		if($id=='home_public_summary') $this->homePageSummary();
	
	}

	private function initP() {

		return array("title"=>null, "text"=>null, "image"=>null);

	}

}
