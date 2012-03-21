<?php
/**
 * @file /var/www/jeff.git/modules/login/login.php
 * @brief Contains the model of the login module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup login_module
 * @brief Login model class
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class login {

	/**
	 * @brief the @ref registry singleton instance 
	 */
	private $_registry;
	
	/**
	 * @brief login form action 
	 */
	public $actionform;

	/**
	 * @brief logout link 
	 */
	public $link;

	/**
	 * @brief Constructs a login instance 
	 * 
	 * @param string $id log action type ('in' or 'out')
	 * @return login instance
	 */
	function __construct($id) {
	
		$this->_registry = registry::instance();

		if($id=='in') $this->actionform = $this->_registry->router->linkHref('login', null);
		elseif($id=='out') $this->link = $this->_registry->router->linkHref('logout', null);
	
	}


}

?>
