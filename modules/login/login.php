<?php

class login extends model {

	public $actionform, $link;

	function __construct($registry, $id) {
	
		$this->_registry = $registry;

		if($id=='in') $this->actionform = $this->_registry->router->linkHref('login', null);
		elseif($id=='out') $this->link = $this->_registry->router->linkHref('logout', null);
	
	}


}
