<?php

require_once('page.php');

class pageController extends controller {
	
	private $_white_list = array("credits");

	function __construct() {

		parent::__construct();

		$this->_cpath = dirname(__FILE__);

	}

	public function index() {
		return null;
	}

	public function view($id=null) {

		$id = $id ? $id : cleanInput('get', 'id', 'string');

		if(!in_array($id, $this->_white_list)) header("Location: ".ROOT);

		$this->_view->setTpl($id);
		$this->_view->assign('registry', $this->_registry);

		return $this->_view->render();
	
	}


}

?>
