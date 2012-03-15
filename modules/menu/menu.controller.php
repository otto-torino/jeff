<?php

require_once('menu.php');

class menuController extends controller {

	function __construct() {

		parent::__construct();

		$this->_cpath = dirname(__FILE__);

	}

	public function adminMenu() {
		
		access::check('admin_view');

		$menu = new menu('admin');
		$this->_view->setTpl('menu_admin', array('css'=>'menu'));
		$this->_registry->addJs(relativePath($this->_cpath).'/js/menu.js');
		
		$this->_view->assign('voices', $menu->voices);

		return $this->_view->render();

	}
	
	public function mainMenu() {
		
		access::check('public_view', null, array("exitOnFailure"=>true));

		$menu = new menu('main');
		$this->_view->setTpl('menu_public', array('css'=>'menu'));
		$this->_registry->addJs(relativePath($this->_cpath).'/js/menu.js');
		$this->_view->assign('voices', $menu->voices);

		return $this->_view->render();

	}

	public function index() {
		return null;
	}

}

?>
