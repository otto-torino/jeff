<?php
/**
 * @file index.controller.php
 * @brief Contains the controller of the index module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup index_module Index
 * @ingroup modules
 *
 * Module which outputs the default index pages of Jeff framework
 */

/**
 * @ingroup index_module
 * @brief Index module controller
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class indexController extends controller {
	
	/**
	 * @brief Constructs a index controller instance 
	 * 
	 * @return index controller instance
	 */
	function __construct() {

		parent::__construct();

		$this->_cpath = dirname(__FILE__);

	}

	/**
	 * @brief Jeff default index views (admin and public)
	 *
	 * @access public
	 * @return index view output
	 */
	public function index() {
	
		if($this->_registry->site=='admin') return $this->adminIndex();
		
		return $this->publicIndex();

	}

	/**
	 * @brief public index view 
	 * 
	 * @return public index view output
	 */
	public function publicIndex() {

		access::check('public_view', null, array("exitOnFailure"=>true));

		$this->_view->setTpl('index_public', array('css', 'index'));

		return $this->_view->render();
	}
	
	/**
	 * @brief user index view 
	 * 	 
	 * @return user index view output
	 */
	public function userIndex() {
	
		access::check('private_view');

		$this->_view->setTpl('index_user', array('css'=>'index'));

		return $this->_view->render();	
	}
	
	/**
	 * @brief admin index view 
	 * 	 
	 * @return admin index view output
	 */
	public function adminIndex() {
	
		access::check('admin_view');

		$this->_view->setTpl('index_admin', array('css', 'index'));

		return $this->_view->render();
	
	}

}

?>
