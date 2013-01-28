<?php
/**
 * @file layout.controller.php
 * @brief Contains the controller of the layout module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup layout_module Layout
 * @ingroup modules themes
 *
 * Module for the selection of the active theme
 */

require_once('layout.php');

/**
 * @ingroup layout_module
 * @brief Layout module controller
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class layoutController extends controller {
	
	/**
	 * module's administration privilege class 
	 */
	private $_class_privilege;

	/**
	 * module's administration privilege id 
	 */
	private $_admin_privilege;

	/**
	 * @brief Constructs a layout controller instance 
	 * 
	 * @return layout controller instance
	 */
	function __construct() {

		parent::__construct();

		$this->_cpath = dirname(__FILE__);
		$this->_mdl_name = "layout";

		// privileges
		$this->_class_privilege = $this->_mdl_name;
		$this->_admin_privilege = 1;
	}
	
	/**
	 * @brief Layout module backoffice 
	 * 
	 * @access public
	 * @return the layout module backoffice
	 */
	public function manage() {
	
		access::check($this->_class_privilege, $this->_admin_privilege, array("exitOnFailure"=>true));

		$items = array();
		foreach(layout::getThemes($this->_registry) as $theme) {
			require_once(ABS_THEMES.DS.$theme->name.DS.$theme->name.'.php');
			$themeClassName = $theme->name."Theme";
			$themeObj = new $themeClassName($this->_registry);
			$items[] = array(
				'link_activate'=>$this->_router->linkHref($this->_mdl_name, 'activateTheme', array("id"=>$theme->id)), 
				'image'=>htmlVar($themeObj->getImage()), 
				'name'=>htmlVar($themeObj->getName()), 
				'description'=>htmlVar($themeObj->getDescription()), 
				'active'=>$theme->active ? true : false
			);
		}

		$this->_view->setTpl('layout_list');
		$this->_view->assign('title', __("ManageLayout"));
		$this->_view->assign('text', __("ManageLayoutExp"));
		$this->_view->assign('items', $items);

		return $this->_view->render();

	}

	/**
	 * @brief Theme activation
	 *
	 * The theme to activate is taken from the $_GET parameter 'id' 
	 * 
	 * @return void
	 */
	public function activateTheme() {

		access::check($this->_class_privilege, $this->_admin_privilege, array("exitOnFailure"=>true));

		$id = cleanInput('get', 'id', 'int');
		layout::activateTheme($id);

		header("Location: ".$this->_router->linkHref($this->_mdl_name, 'manage'));

	}

}

?>
