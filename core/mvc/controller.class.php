<?php

require_once('model.class.php');
require_once('view.class.php');

class controller {

	protected $_registry, $_router, $_view, $_cpath;

	function __construct($registry) {
	
		$this->_registry = $registry;
		$this->_router = $registry->router;
		$this->_view = new view($registry);
		
	}
}

?>
