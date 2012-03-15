<?php

require_once('index.php');

class indexController extends controller {

	function __construct() {

		parent::__construct();

		$this->_cpath = dirname(__FILE__);

	}

	public function index() {
	
		if($this->_registry->site=='admin') return $this->adminIndex();
		
		return $this->publicIndex();

	}

	public function publicIndex() {

		access::check('public_view', null, array("exitOnFailure"=>true));

		$this->_view->setTpl('index_public', array('css', 'index'));

		return $this->_view->render();
	}

	public function userIndex() {
	
		access::check('private_view');

		$this->_view->setTpl('index_user', array('css'=>'index'));
		$this->_view->assign('summary', $summary);

		return $this->_view->render();	
	}
	
	public function adminIndex() {
	
		access::check('admin_view');

		$this->_view->setTpl('index_admin', array('css', 'index'));

		return $this->_view->render();
	
	}

}

?>
