<?php
/**
 * \file controller.class.php
 * \brief Contains the controller primitive class.
 *
 * Defines the mvc controller class
 *
 * @version 0.98
 * @copyright 2011 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors abidibo abidibo@gmail.com
 */

require_once('model.class.php');
require_once('view.class.php');

/**
 * \ingroup mvc core
 * \brief Controller class of the MVC pattern, is the class used to control a model object and call the desired views. 
 * 
 * This is the general controller class extended by all specific module controllers. It acts like an interface used to control and view the model data.<br />
 * Clearly every module has its own public interfaces, so every controller is different. <br />
 * That's why the controller class only implements the constructor method which instantiates some protected properties.
 *
 * @version 0.98
 * @copyright 2011 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author abidibo abidibo@gmail.com 
 */
class controller {

	/**
	 * \brief The registry singleton instance 
	 */
	protected $_registry;
	
	/**
	 * \brief A router instance 
	 */
	protected $_router;

	/**
	 * \brief A view instance 
	 */
	protected $_view;

	/**
	 * \brief The absolute path of the controller class file 
	 */
	protected $_cpath;

	/**
	 * Constructs a controller instance 
	 *
	 * Sets some properties: _registry, _router and _view.
	 * 
	 * @return void
	 */
	function __construct() {
	
		$this->_registry = registry::instance();
		$this->_router = $this->_registry->router;
		$this->_view = new view();
		
	}

}

?>
