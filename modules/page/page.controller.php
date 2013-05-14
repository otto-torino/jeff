<?php
/**
 * @file page.controller.php
 * @brief Contains the controller of the page module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup page_module Pages
 * @ingroup modules
 *
 * Module for the management of html pages
 */

/**
 * @ingroup page_module
 * @brief Page module controller
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class pageController extends controller {
	
	/**
	 * @brief allowed pages id 
	 */
	private $_white_list = array("http404", "http403", "credits");

	/**
	 * @brief Constructs a page controller instance 
	 * 
	 * @return page controller instance
	 */
	function __construct() {

		parent::__construct();

		$this->_cpath = dirname(__FILE__);

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
	 * @brief Page view 
	 *
	 * If the requested page is not listed in the white list the user is redirected to the home page
	 * 
	 * @param string $id page identifier (template name)
	 * @return the requested page
	 */
	public function view($id=null) {

		$id = $id ? $id : cleanInput('get', 'id', 'string');

		if(!in_array($id, $this->_white_list)) header("Location: ".ROOT);

		$this->_view->setTpl($id);
		$this->_view->assign('registry', $this->_registry);

		return $this->_view->render();
	
	}


}

?>
