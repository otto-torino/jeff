<?php
/**
 * @file core.class.php
 * @brief Contains the core class.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup core Framework core
 * @brief Classes which forms the core of the framework
 */

/**
 * @ingroup core
 * @brief The core of the web application 
 * 
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class core {

	/**
	 * @brief the registry singleton instance 
	 */
	protected $_registry;
	
	/**
	 * @brief path used to generate links 
	 */
	private $_base_path;

	/**
	 * @brief Constructs the core instance 
	 * 
	 * Initializes many registry properties used throughout the framework, checks for session timeout (if active) and checks for installed plugins. 
	 * 
	 * @return void
	 */
	function __construct() {

		session_name(SESSIONNAME);
		session_start();
		require_once(ABS_CORE.DS.'tables.php');
		require_once(ABS_CORE.DS.'include.php');

		$this->_base_path = BASE_PATH;
		
		// initializing registry variable
		$this->_registry = registry::instance();
		$this->_registry->db = db::instance();
		$this->_registry->url = $_SERVER['REQUEST_URI'];
		$this->_registry->admin_privilege = 1;
		$this->_registry->admin_view_privilege = 2;
		$this->_registry->public_view_privilege = 3;
		$this->_registry->private_view_privilege = 4;
		$this->_registry->theme = $this->getTheme();
		$this->_registry->lng = language::setLanguage($this->_registry);
		$this->_registry->site_settings = new siteSettings($this->_registry);
		$this->_registry->dtime = new dtime($this->_registry);
		$this->_registry->router = new router($this->_base_path);
		$this->_registry->isHome = preg_match("#^module=index&method=index(&.*)?$#", $_SERVER['QUERY_STRING']) ? true : false;
		$this->_registry->css = array();
		$this->_registry->js = array();
		$this->_registry->meta = array();
		$this->_registry->head_links = array();

		//set session timeout
		if($this->_registry->site_settings->session_timeout) {
			if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $this->_registry->site_settings->session_timeout)) {
				// last request was more than timeout seconds ago
				session_regenerate_id(true);
				session_destroy();
				unset($_SESSION);
				session_start();
			}
			$_SESSION['last_activity'] = time(); // update last activity time stamp
		}

		// extra plugins
		$plugins_objs = array();
		if(is_readable(ABS_ROOT.DS.'plugins.php')) {
			require_once(ABS_ROOT.DS.'plugins.php');
			foreach($plugins as $k=>$v) { 
				if(is_readable(ABS_PLUGINS.DS.$k.DS.$k.".php")) {
					require_once(ABS_PLUGINS.DS.$k.DS.$k.".php");
					$plugins_objs[$k] = new $k($this->_registry, $v);
				}
				else 
					exit(error::syserrorMessage(get_class($this), '__construct', sprintf(__("cantFindPluginSource"), $k), __LINE__));
			}
		}
		$this->_registry->plugins = $plugins_objs;

	}

	/**
	 * @brief Renders the whole document 
	 * 
	 * @param string $site the requested site: main or admin
	 * @return void
	 */
	public function renderApp($site=null) {

		ob_start();

		// some other registry properties
		$this->_registry->site = $site=='admin' ? 'admin':'main';

		/*
		 * check login/logout
		 */
		authentication::check();

		/*
		 * create document
		 */
		$doc = new document();
		$buffer = $doc->render();

		ob_end_flush();

	}

	/**
	 * @brief Returns the output of the class method invoked through the url 
	 * 
	 * @return void
	 */
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

	/**
	 * @brief Retrieves the active theme object. 
	 * 
	 * @return the active theme or a sys error message
	 */
	public function getTheme() {

		$rows = $this->_registry->db->autoSelect(array("name"), TBL_THEMES, "active='1'", '');
		$theme_name = $rows[0]['name'];

		if(is_readable(ABS_THEMES.DS.$theme_name.DS.$theme_name.'.php'))
			require_once(ABS_THEMES.DS.$theme_name.DS.$theme_name.'.php');
		else 
			Error::syserrorMessage('coew', 'getTheme', sprintf(__("CantLoadThemeError"), $theme_name, __LINE__));

		$theme_class = $theme_name.'Theme';

		if(class_exists($theme_class))
			return new $theme_class();
		else 
			Error::syserrorMessage('coew', 'getTheme', sprintf(__("CantLoadThemeError"), $theme_name, __LINE__));

	}

}

?>
