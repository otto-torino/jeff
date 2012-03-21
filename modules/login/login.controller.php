<?php
/**
 * @file login.controller.php
 * @brief Contains the controller of the login module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup login_module Login
 * @ingroup modules
 *
 * Module which displays the login/logout forms and links
 */

require_once('login.php');

/**
 * @ingroup login_module
 * @brief Login module controller
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class loginController extends controller {
	
	/**
	 * @brief Constructs a login controller instance 
	 * 
	 * @return login controller instance
	 */
	function __construct() {

		parent::__construct();

		$this->_cpath = dirname(__FILE__);

	}

	/**
	 * @brief Administration login form 
	 * 
	 * @return admin login form
	 */
	public function adminlogin() {
	
		$obj = new login('in');
		$this->_view->setTpl('login_admin');
		$this->_view->assign('form_action', $obj->actionform);

		return $this->_view->render();
	
	}
	
	/**
	 * @brief Administration logout link 
	 * 
	 * @return admin logout link
	 */
	public function adminlogout() {
		
		$obj = new login('out');
		$this->_view->setTpl('logout_admin');
		$this->_view->assign('link', $obj->link);

		return $this->_view->render();
  
	}
	
	/**
	 * @brief Public login form 
	 * 
	 * @return public login form
	 */
	public function login() {
	
		$obj = new login('in');
		$this->_view->setTpl('login', array('css'=>'login'));
		$this->_view->assign('form_action', $obj->actionform);

		return $this->_view->render();
	
	}
	
	/**
	 * @brief Public logout link 
	 * 
	 * @return public logout link
	 */
	public function logout() {
		
		$obj = new login('out');
		$this->_view->setTpl('logout');
		$this->_view->assign('link', $obj->link);

		return $this->_view->render();
  
	}

	/**
	 * @brief Fallback when calling methods which don't exists 
	 * 
	 * @return null
	 */
	public function index() {
		return null;
	}

}

?>
