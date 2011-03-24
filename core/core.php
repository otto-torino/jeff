<?php

class core {

	private $_registry, $_base_path, $_site;

	function __construct() {
	
		require_once(ABS_CORE.DS.'tables.php');
		require_once(ABS_CORE.DS.'include.php');

		$this->_base_path = BASE_PATH;
		
		// initializing registry variable
		$this->_registry = new registry();
		$this->_registry->db = db::getInstance();
		$this->_registry->admin_privilege = 1;
		$this->_registry->admin_view_privilege = 2;
		$this->_registry->public_view_privilege = 3;
		$this->_registry->private_view_privilege = 4;

	}

	public function renderApp($site=null) {
		
		session_name(SESSIONNAME);
		session_start();
		ob_start();

		// some other registry properties
		$this->_registry->theme = $this->getTheme();
		$this->_registry->lng = language::setLanguage($this->_registry);
		$this->_registry->site_settings = new siteSettings($this->_registry);
		$this->_registry->dtime = new dtime($this->_registry);
		$this->_registry->router = new router($this->_registry, $this->_base_path);
		$this->_registry->site = $site=='admin' ? 'admin':'main';
		$this->_registry->isHome = preg_match("#^module=index&method=index(&.*)?$#", $_SERVER['QUERY_STRING']) ? true : false;
		/*
		 * check login/logout
		 */
		authentication::check($this->_registry);

		/*
		 * create document
		 */
		$doc = new document($this->_registry);
		$buffer = $doc->render();

		ob_end_flush();

	}

	public function methodPointer() {

		session_name(SESSIONNAME);
		session_start();

		ob_start();

		$this->_registry = new registry();
		$this->_registry->db = db::getInstance();
		$this->_registry->router = new router($this->_registry, $this->_base_path);
		
		/*
		 * check login/logout
		 */
		authentication::check($this->_registry);

		echo $registry->router->loader(null);
		ob_end_flush();

		exit(); 
	}

	public function getTheme() {

		$rows = $this->_registry->db->autoSelect(array("name"), TBL_THEMES, "active='1'", '');

		return new theme($this->_registry, $rows[0]['name']);

	}


}

?>
