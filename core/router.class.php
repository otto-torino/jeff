<?php

class router {

	private $_registry, $_base_path;
	private $_module, $_method;

	function __construct($registry, $base_path) {

		$this->_registry = $registry;
		$this->_base_path = $base_path;

	}

	public function module() {

		return $this->_module;

	}

	public function method() {

		return $this->_method;

	}

	public function loader($route) {

		/*** check the route ***/
		$this->getModule($route);

		/*** if the file is not there diaf ***/
		if(!is_readable(ABS_MDL.DS.$this->_module.DS.$this->_module.'.controller.php'))
		{
			Error::syserrorMessage('router', 'laoder', sprintf(__("CantChargeModuleControllerError"), $this->_module, ABS_MDL.DS.$this->_module.DS.$this->_module.'.controller.php'), __LINE__);
		}

		/*** include the controller ***/
		include_once(ABS_MDL.DS.$this->_module.DS.$this->_module.'.controller.php');

		if($route == null) $this->_registry->urlModule = $this->_module;

		/*** a new controller class instance ***/
		$class = $this->_module."Controller";
		$controller = new $class();

		/*** check if the method is callable ***/
		$method = is_callable(array($controller, $this->_method)) ? $this->_method : 'index';

		$params = gOpt($route, 'params');
		/*** run the action ***/

		return $controller->$method($params);
 	}

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
	
	public function linkAjax($module, $method, $params=array()) {

		$href = $this->_base_path."/pointer.php?module=$module&method=$method";
		if(count($params)) {
			foreach($params as $k=>$v) {
				$href .= "&".$k."=".$v;
			}
		}

		return $href;
	}

}

?>
