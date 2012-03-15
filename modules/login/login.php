<?php

class login {

	private $_registry;
	public $actionform, $link;

	function __construct($id) {
	
		$this->_registry = registry::instance();

		if($id=='in') $this->actionform = $this->_registry->router->linkHref('login', null);
		elseif($id=='out') $this->link = $this->_registry->router->linkHref('logout', null);
	
	}


}

?>
