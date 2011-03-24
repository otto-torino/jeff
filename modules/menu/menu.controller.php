<?php

require_once('menu.php');

class menuController extends controller {

	function __construct($registry) {

		parent::__construct($registry);

		$this->_cpath = dirname(__FILE__);

	}

	public function adminMenu() {
		
		access::check($this->_registry, 'admin_view');

		$menu = new menu($this->_registry, 'admin');
		$this->_view->setTpl('menu_admin', array('css'=>'menu'));
		$this->_registry->addJs(relativePath($this->_cpath).'/js/menu.js');
		
		$this->_view->assign('voices', $menu->voices);

		return $this->_view->render();

	}
	
	public function mainMenu() {
		
		$menu = new menu($this->_registry, 'main');
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
