<?php
/**
 * @file menu.controller.php
 * @brief Contains the controller of the menu module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup menu_module Menu
 * @ingroup modules
 *
 * Module for the management of the main and administrative menu.
 *
 * The menu voices of the main menu are stored in a database table and managed through backoffice. The visibility of each voice may be restricted only to some user groups.
 *
 * The menu voices of the administrative menu are written in the menu model class.
 */

require_once('menu.php');

/**
 * @ingroup menu_module
 * @brief Menu module controller
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class menuController extends controller {
	
	/**
	 * module's administration privilege class 
	 */
	private $_class_privilege;

	/**
	 * module's administration privilege id 
	 */
	private $_admin_privilege;

	/**
	 * @brief Constructs a menu controller instance 
	 * 
	 * @return menu controller instance
	 */
	function __construct() {

		parent::__construct();

		$this->_cpath = dirname(__FILE__);

		$this->_mdl_name = "menu";

		// privileges
		$this->_class_privilege = $this->_mdl_name;
		$this->_admin_privilege = 1;
	}

	/**
	 * @brief Administrative area's menu 
	 * 
	 * @return menu of the administrative area
	 */
	public function adminMenu() {
		
		access::check('admin_view');

		$menu = new menu('admin');
		$this->_view->setTpl('menu_admin', array('css'=>'menu_admin'));
		$this->_registry->addJs(relativePath($this->_cpath).'/js/menu.js');
		
		$this->_view->assign('voices', $menu->voices);

		return $this->_view->render();

	}
	
	/**
	 * @brief Main site menu 
	 * 
	 * @return main site menu
	 */
	public function mainMenu() {
		
		$menu = new menu('main');

		$this->_view->setTpl('menu_public', array('css'=>'menu'));
		$this->_registry->addJs(relativePath($this->_cpath).'/js/menu.js');

		$this->_view->assign('voices', $menu->voices);
		$this->_view->assign('selected_url', $_SERVER['REQUEST_URI']);

		return $this->_view->render();

	}

	/**
	 * @brief Callback when calling a non existing method 
	 * 
	 * @return null
	 */
	public function index() {
		return null;
	}

	/**
	 * @brief Menu module backoffice 
	 * 
	 * @access public
	 * @return the menu module backoffice
	 */
	public function manage() {
		
		require_once('menuAdminTable.php');
		
		access::check($this->_class_privilege, $this->_admin_privilege, array("exitOnFailure"=>true));
		

		$f_keys = array(
			"parent"=>array(
				"table"=>TBL_MENU,
				"field"=>"label",
				"where"=>null,
				"order"=>"label"
			)
		);

		$target_data = array("_self"=>__("sameWindow"), "_blank"=>__("newWindow"));
		$s_fields = array(
			"groups"=>array(
				"type"=>"multicheck",
				"value_type"=>"int",
				"table"=>TBL_SYS_GROUPS,
				"field"=>"label",
				"where"=>"",
				"order"=>'id'
			),
			"target"=>array(
				'type' => 'enum',
				'key_type' => 'string',
				'data' => $target_data
			)
		);

		$at = new menuAdminTable(TBL_MENU, array("insertion"=>true));
		$at->setForeignKeys($f_keys);
		$at->setSpecialFields($s_fields);

		$table = $at->manage();

		$this->_view->setTpl('manage_table');
		$this->_view->assign('title', __("ManageTable")." ".__("Menu"));
		$this->_view->assign('table', $table);

		return $this->_view->render();

	}
}

?>
