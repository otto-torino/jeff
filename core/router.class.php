<?php
/**
 * @file router.class.php
 * @brief Contains the router class.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup core
 * @brief Framework router class
 *
 * This class is used to load/call class methods, it includes the proper class file, instantiates the module's 
 * controller and call its method. Also it exports the url module and method to use for example in the 
 * template factory.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class router {

	/**
	 * @brief the @ref registry singleton instance 
	 */
	private $_registry;
	
	/**
 	 * @brief base path for link generation 
 	 */
	private $_base_path;
	
	/**
 	 * @brief module called by url 
 	 */
	private $_module;
	
	/**
 	 * @brief method called by url 
 	 */
    private	$_method;

	/**
	 * @brief Constructs a router instance 
	 * 
	 * @param string $base_path the base path 
	 * @return void
	 */
	function __construct($base_path) {

		$this->_registry = registry::instance();
		$this->_base_path = $base_path;

	}

	/**
	 * @brief Returns the module called by url 
	 * 
	 * @return the url module name
	 */
	public function module() {

		return $this->_module;

	}
	
	/**
	 * @brief Returns the method called by url 
	 * 
	 * @return the url method name
	 */
	public function method() {

		return $this->_method;

	}

	/**
	 * @brief Loads a module controller and returns its called method 
	 * 
	 * @param array $route 
	 *   the class and method to call. Possible values are:
	 *   - associative array in the form array('module'=>'module_name', 'method'=>'method_name')
	 *   - null: the class name and method name are taken directly from url
	 *   if the method is not found or not callable the 'index' method is called
	 * @access public
	 * @return the controller method output or a system error
	 */
	public function loader($route) {

		/*** check the route ***/
		$this->getModule($route);

		$controller = $this->loadController($this->_module);

		if($route == null) $this->_registry->urlModule = $this->_module;

		/*** check if the method is callable ***/
		$method = is_callable(array($controller, $this->_method)) ? $this->_method : 'index';

		$params = gOpt($route, 'params');
		/*** run the action ***/

		return $controller->$method($params);
 	}

	/**
	 * @brief Sets the module and method properties 
	 * 
	 * @param array $route 
	 *   the class and method to set. Possible values are:
	 *   - associative array in the form array('module'=>'module_name', 'method'=>'method_name')
	 *   - null: the class name and method name are taken directly from url 
	 * @return void
	 */
	public function getModule($route) {

		if(is_array($route) && isset($route['module']) && isset($route['method'])) {
			$this->_module = $route['module'];
			$this->_method = $route['method'];
		}
		else { /*** get the route from the url ***/
			$this->_module = isset($_GET['module']) ? $_GET['module'] : 'index';
			$this->_method = isset($_GET['method']) ? $_GET['method'] : 'index';
		}
	}

	/**
	 * @brief Link generation 
	 *
	 * This method is used to generate links starting from a module name, one of its methods 
	 * and additional parameters
	 * 
	 * @param string $module module name (the name of the model class) 
	 * @param string $method method name
	 * @param array $params associative array of parameters in the form array('param_name'=>'param_value') 
	 * @param mixed $opts 
	 *   associative array of oprions:
	 *   - **permalink**: whether to use permalinks or not 
	 *     (i.e. '/class/method/param/value' 'against /index.php?module=class&method=method&param=value'
	 * @return void
	 */
	public function linkHref($module, $method, $params=array(), $opts=null) {

		$permalink = gOpt($opts, 'permalink', true);

		$href = '';
		if($module) $href .= $permalink ? "/".$module : "&module=$module";
		if($method) $href .= $permalink ? "/".$method : "&method=$method";
		if(count($params)) {
			if(isset($params['id']) && $params['id']) $href .= $permalink ? "/".$params['id'] : "&id=".$params['id'];
			foreach($params as $k=>$v) {
				if($k!='id') $href .= $permalink ? "/".$k."/".$v : "&$k=$v";
			}
		}
		$href .= $permalink ? "/" : "";

		return $this->_base_path.($permalink ? $href : "/?".substr($href, 1));
	}
	
	/**
	 * @brief Ajax link generation 
	 *
	 * This method is used to generate links which points directly to a controller class method, without considering
	 * the whole document (themes, templates and so on). Generally used to perform ajax requests.
	 * 
	 * @param string $module module name (the name of the model class) 
	 * @param string $method method name
	 * @param array $params associative array of parameters in the form array('param_name'=>'param_value') 
	 * @return void
	 */
	public function linkAjax($module, $method, $params=array()) {

		$href = $this->_base_path."/pointer.php?module=$module&method=$method";
		if(count($params)) {
			foreach($params as $k=>$v) {
				$href .= "&".$k."=".$v;
			}
		}

		return $href;
	}

	/**
	 * @brief Download files
	 *
	 * This method is used to download files
	 * 
	 * @param string $path the file path
	 * @return void
	 */
	public function download($path) {
		
		if($fp = fopen($path, "r")) {
			$fsize = filesize($path);
			$path_parts = pathinfo($path);
			$extension = strtolower($path_parts["extension"]);

			header("Pragma: public");
			header('Expires: 0');
			header('Content-Description: File Transfer');
			header("Content-type: application/download");
			header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\"");
			header("Content-length: ".$fsize);
			header("Cache-control: private");

			ob_clean();
			flush();

			@readfile($path);
			fclose($fp);
		}
		else {
			error::raise404();
		}
	}

	/**
	 * #brief Loads and returns the controller of the given module 
	 * 
	 * @param string $module module name
	 * @return module's controller
	 */
	public function loadController($module) {

		/*** if the file is not there diaf ***/
		if(!is_readable(ABS_MDL.DS.$module.DS.$module.'.controller.php'))
		{
			Error::syserrorMessage('router', 'laodController', sprintf(__("CantChargeModuleControllerError"), $module, ABS_MDL.DS.$module.DS.$module.'.controller.php'), __LINE__);
		}

		/*** include the controller ***/
		require_once(ABS_MDL.DS.$module.DS.$module.'.controller.php');

		/*** a new controller class instance ***/
		$class = $module."Controller";
		$controller = new $class();

		return $controller;

	}

}

?>
