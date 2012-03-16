<?php
/**
 * @file datetimeSettings.controller.php
 * @brief Contains the controller of the datetimeSettings module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.98
 * @date 2011-2012
 * @copyright Otto srl MIT License \see http://www.opensource.org/licenses/mit-license.php
 */

/**
 * @defgroup modules Modules
 *
 * <p>Modules are the entities which produce html outputs. May be formed by one or more classes, following the @ref MVC pattern.<br />
 * Every module has at least a controller which exposes the public methods callable by url and usable in the global templates, see @ref templates.</p>
 * <p>Standard models have a @ref controller class and one or more @ref model classes which manages the module's data stored in the database.</p>
 * <p>Usually all models used the primitive @ref view class but may also extend it to add more functionality.</p>
 *
 */

/**
 * @defgroup datetimesettings_module Datetime settings
 * @ingroup modules datetime
 *
 * Module for the management of the system date and time settings
 */

/**
 * @ingroup datetimesettings_module
 * @brief Datetime Settings module controller
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.98
 * @date 2011-2012
 * @copyright Otto srl MIT License \see http://www.opensource.org/licenses/mit-license.php 
 */
class datetimeSettingsController extends controller {

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

		/**
 		 * Module's administration privilege  
 		 */
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
