<?php

require_once('login.php');

class loginController extends controller {

	function __construct($registry) {

		parent::__construct($registry);

		$this->_cpath = dirname(__FILE__);

	}

	public function adminlogin() {
	
		$obj = new login($this->_registry, 'in');
		$this->_view->setTpl('login_admin');
		$this->_view->assign('form_action', $obj->actionform);

		return $this->_view->render();
	
	}

	public function adminlogout() {
		
		$obj = new login($this->_registry, 'out');
		$this->_view->setTpl('logout_admin');
		$this->_view->assign('link', $obj->link);

		return $this->_view->render();
  
	}
	
	public function login() {
	
		$obj = new login($this->_registry, 'in');
		$this->_view->setTpl('login', array('css'=>'login'));
		$this->_view->assign('form_action', $obj->actionform);

		return $this->_view->render();
	
	}

	public function logout() {
		
		$obj = new login($this->_registry, 'out');
		$this->_view->setTpl('logout');
		$this->_view->assign('link', $obj->link);

		return $this->_view->render();
  
	}

	public function index() {
		return null;
	}
}

?>
