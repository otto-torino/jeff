<?php

class core {

	private $_registry, $_base_path, $_site;

	function __construct() {

		session_name(SESSIONNAME);
		session_start();

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
		$this->_registry->theme = $this->getTheme();
		$_SESSION['theme'] = $this->_registry->theme; // translations
		$this->_registry->lng = language::setLanguage($this->_registry);
		$this->_registry->site_settings = new siteSettings($this->_registry);
		$this->_registry->dtime = new dtime($this->_registry);
		$this->_registry->router = new router($this->_registry, $this->_base_path);
		$this->_registry->isHome = preg_match("#^module=index&method=index(&.*)?$#", $_SERVER['QUERY_STRING']) ? true : false;

		// extra plugins
		$plugins_objs = array();
		if(is_readable(ABS_ROOT.DS.'plugins.php')) {
			require_once(ABS_ROOT.DS.'plugins.php');
			foreach($plugins as $k=>$v) { 
				if(is_readable(ABS_PLUGINS.DS.$k.".php")) {
					require_once(ABS_PLUGINS.DS.$k.".php");
					$plugins_objs[$k] = new $k($this->_registry, $v);
				}
				else 
					exit(error::syserrorMessage(get_class($this), '__construct', sprintf(__("cantFindPluginSource"), $k), __LINE__));
			}
		}
		$this->_registry->plugins = $plugins_objs;

	}

	public function renderApp($site=null) {
		
		ob_start();
		
		// some other registry properties
		$this->_registry->site = $site=='admin' ? 'admin':'main';

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

		ob_start();

		/*
		 * check login/logout
		 */
		authentication::check($this->_registry);

		echo $this->_registry->router->loader(null);
		ob_end_flush();

		exit(); 
	}

	public function getTheme() {

		$rows = $this->_registry->db->autoSelect(array("name"), TBL_THEMES, "active='1'", '');
		$theme_name = $rows[0]['name'];

		if(is_readable(ABS_THEMES.DS.$theme_name.DS.$theme_name.'.php'))
			require_once(ABS_THEMES.DS.$theme_name.DS.$theme_name.'.php');
		else 
			Error::syserrorMessage('coew', 'getTheme', sprintf(__("CantLoadThemeError"), $theme_name, __LINE__));

		$theme_class = $theme_name.'Theme';

		if(class_exists($theme_class))
			return new $theme_class($this->_registry);
		else 
			Error::syserrorMessage('coew', 'getTheme', sprintf(__("CantLoadThemeError"), $theme_name, __LINE__));

	}


}

?>
