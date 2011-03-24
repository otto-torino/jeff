<?php

require_once('index.php');

class indexController extends controller {

	function __construct($registry) {

		parent::__construct($registry);

		$this->_cpath = dirname(__FILE__);

	}

	public function index() {
	
		if($this->_registry->site=='admin') return $this->adminIndex();
		//if($this->_registry->user) return $this->userIndex();
		
		return $this->publicIndex();

	}

	public function publicIndex() {
		
		access::check($this->_registry, 'public_view', null, array("exitOnFailure"=>true));

		$this->_view->setTpl('index_public', array('css', 'index'));

		return $this->_view->render();
	}

	public function userIndex() {
	
		access::check($this->_registry, 'private_view');

		$this->_view->setTpl('index_user', array('css'=>'index'));
		$this->_view->assign('summary', $summary);

		return $this->_view->render();	
	}
	
	public function adminIndex() {
	
		access::check($this->_registry, 'admin_view');

		$this->_view->setTpl('index_admin', array('css', 'index'));

		return $this->_view->render();
	
	}

}

?>
