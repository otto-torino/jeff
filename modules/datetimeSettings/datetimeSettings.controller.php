<?php
/**
 * @file datetimeSettings.controller.php
 * @brief Contains the controller of the datetimeSettings module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup modules Modules
 *
 * Modules are the entities which produce html outputs. May be formed by one or more classes, following the @ref MVC pattern. \n 
 * Every module has at least a controller which exposes the public methods callable by url and usable in the global templates, see @ref templates.
 *
 * Standard models have a @ref controller class and one or more @ref model classes which manages the module's data stored in the database.
 *
 * Usually all models used the primitive @ref view class but may also extend it to add more functionality.
 *
 */

/**
 * @defgroup datetimesettings_module Datetime settings
 * @ingroup modules datetime configurations
 *
 * Module for the management of the system date and time settings
 */

/**
 * @ingroup datetimesettings_module
 * @brief Datetime Settings module controller
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class datetimeSettingsController extends controller {
	
	/**
	 * module's administration privilege class 
	 */
	private $_class_privilege;

	/**
	 * module's administration privilege id 
	 */
	private $_admin_privilege;

	/**
	 * @brief Constructs a datetimeSettings controller instance 
	 * 
	 * @return datetimeSettings controller instance
	 */
	function __construct() {

		parent::__construct();

		$this->_cpath = dirname(__FILE__);
		$this->_mdl_name = "datetimeSettings";

		// privileges
		$this->_class_privilege = $this->_mdl_name;
		$this->_admin_privilege = 1;

	}

	/**
	 * @brief Datetime settings backoffice 
	 * 
	 * @access public
	 * @return the datetime settings table back office
	 */
	public function manage() {

		access::check($this->_class_privilege, $this->_admin_privilege, array("exitOnFailure"=>true));

		$at = new adminTable(TBL_SYS_DATETIME_SETTINGS, array('insertion'=>false, "deletion"=>false));

		$table = $at->manage();

		$this->_view->setTpl('manage_table');
		$this->_view->assign('title', __("ManageTable")." ".TBL_SYS_DATETIME_SETTINGS);
		$this->_view->assign('table', $table);

		return $this->_view->render();
	}

}

?>
