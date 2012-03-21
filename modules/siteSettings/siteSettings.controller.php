<?php
/**
 * @file siteSettings.controller.php
 * @brief Contains the controller of the siteSettings module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup sitesettings_module Application settings
 * @ingroup modules configurations
 *
 * Module for the management of the application settings
 */

/**
 * @ingroup sitesettings_module
 * @brief Site settings module controller
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class siteSettingsController extends controller {

	/**
	 * module's administration privilege class 
	 */
	private $_class_privilege;

	/**
	 * module's administration privilege id 
	 */
	private $_admin_privilege;

	/**
	 * @brief Constructs a siteSettings controller instance 
	 * 
	 * @return siteSettings controller instance
	 */
	function __construct() {

		parent::__construct();

		$this->_cpath = dirname(__FILE__);
		$this->_mdl_name = "siteSettings";

		// privileges
		$this->_class_privilege = $this->_mdl_name;
		$this->_admin_privilege = 1;

	}
	
	/**
	 * @brief Site settings backoffice 
	 * 
	 * @return the site settings back office
	 */
	public function manage() {
	
		access::check($this->_class_privilege, $this->_admin_privilege, array("exitOnFailure"=>true));

		$at = new adminTable(TBL_SYS_SETTINGS, array('insertion'=>false, 'deletion'=>false));

		$table = $at->manage();

		$this->_view->setTpl('manage_table');
		$this->_view->assign('title', __("ManageTable")." ".TBL_SYS_SETTINGS);
		$this->_view->assign('table', $table);

		return $this->_view->render();
	}

}

?>
