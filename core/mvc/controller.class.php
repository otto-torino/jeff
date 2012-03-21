<?php
/**
 * @file controller.class.php
 * @brief Contains the controller primitive class.
 *
 * Defines the mvc controller class
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

require_once('model.class.php');
require_once('view.class.php');

/**
 * @ingroup mvc core
 * @brief Controller class of the MVC pattern, is the class used to control a model object and call the desired views. 
 * 
 * This is the general controller class extended by all specific module controllers. It acts like an interface used to control and view the model data. \n
 * Clearly every module has its own public interfaces, so every controller is different. \n
 * That's why the controller class only implements the constructor method which instantiates some protected properties.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php) 
 */
class controller {

	/**
	 * @brief the registry singleton instance 
	 */
	protected $_registry;
	
	/**
	 * @brief a @ref router instance 
	 */
	protected $_router;

	/**
	 * @brief a @ref view instance 
	 */
	protected $_view;

	/**
	 * @brief the absolute path of the controller class file 
	 */
	protected $_cpath;

	/**
	 * @brief the module name 
	 */
	protected $_mdl_name;

	/**
	 * @brief Constructs a controller instance 
	 *
	 * Sets some properties: _registry, _router and _view.
	 * 
	 * @return controller instance
	 */
	function __construct() {
	
		$this->_registry = registry::instance();
		$this->_router = $this->_registry->router;
		$this->_view = new view();
		
	}

}

?>
