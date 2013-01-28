<?php
/**
 * @file privilege.controller.php
 * @brief Contains the controller of the privilege module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup privilege_module Privileges
 * @ingroup modules security
 *
 * <p>The module only allow to display all the available privileges with a brief description</p>
 */

/**
 * @ingroup privilege_module
 * @brief Privilege module controller
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class privilegeController extends controller {
	
	/**
	 * module's privilege class 
	 */
	private $_class_privilege;

	/**
	 * module's list view privilege id 
	 */
	private $_view_privilege;

	/**
	 * @brief Constructs a privilege controller instance 
	 * 
	 * @return privilege controller instance
	 */
	function __construct() {

		parent::__construct();

		$this->_cpath = dirname(__FILE__);
		$this->_mdl_name = "privilege";

		// privileges
		$this->_class_privilege = $this->_mdl_name;
		$this->_view_privilege = 1;
	}
	
	/**
	 * @brief Privileges list 
	 * 
	 * @return list of available privileges
	 */
	public function manage() {
	
		access::check($this->_class_privilege, $this->_view_privilege, array("exitOnFailure"=>true));

		$at = new adminTable(TBL_SYS_PRIVILEGES, array('insertion'=>false, 'edit_deny'=>'all'));

		$text = __("ManagePrivilegesExp");

		$table = $at->manage();

		$this->_view->setTpl('manage_table');
		$this->_view->assign('text', $text);
		$this->_view->assign('title', __("TableContent")." ".TBL_SYS_PRIVILEGES);
		$this->_view->assign('table', $table);

		return $this->_view->render();
	}

}

?>
