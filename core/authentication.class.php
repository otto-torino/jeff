<?php
/**
 * @file authentication.class.php
 * @brief Contains the authentication class.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup security core
 * @brief Class used to check user login/logout actions  
 * 
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl MIT License \see http://www.opensource.org/licenses/mit-license.php
 */
class authentication {

	/**
	 * @brief Cheks for user login/logout actions.
	 *
	 * If the user is logging in checks its credentials. Exits with error if the authentication fails,
	 * sets the userid session variable and redirects the user if it has success. <br />
	 * If the user is logging out destroies the session and redirects to the home page.<br />
	 * If the user is already logged in is associated with the property 'user' of the register singleton instance, 
	 * otherwise that property is associated with a user of type 'free user' with id equal to 0 
	 * 
	 * @return void 
	 */
	public static function check() {

		$registry = registry::instance();

		if(isset($_GET['login'])) {

		    	$redirect = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $registry->router->linkHref(null, null);

			if(($username = cleanInput('post', 'user', 'string')) && ($password = cleanInput('post', 'password', 'string'))) {
				$user = user::getFromAuth($username, $password);	
				if(self::checkUser($user)) {
					$_SESSION['userid'] = $user->id;
					header('Location: '.$redirect);
					exit;
				}
			}	
			
			Error::errorMessage(array("error"=>__("authError")), $redirect);
		}
		elseif(isset($_GET['logout'])) {
			unset($_SESSION);
			session_destroy();
			header('Location: '.$registry->router->linkHref(null, null));
			exit();
		}
		else {
			$registry->user = null;
			$registry->admin = false;

			if(isset($_SESSION['userid'])) {
				$registry->user = new user($_SESSION['userid']);
				if(access::check('main', $registry->admin_privilege)) {
					$registry->admin = true;
				}
			}
			else {
				$registry->user = new StdClass();
				$registry->user->groups = 5;
				$registry->user->id = 0;
				$registry->user->active = 1;
			}
		}


	}

	/**
	 * @brief Checks if user may access the system (main or admin area) 
	 * 
	 * @param user $user 
	 * @static
	 * @access public
	 * @return true if the user has access false otherwise
	 */
	public static function checkUser($user) {

		$registry = registry::instance();

		if(!$user || !$user->active) return false;

		$registry->user = $user;
		if( ($user && $registry->site=='main') || 
		    ($registry->site=='admin' && access::check('main', $registry->admin_view_privilege))) 
		    return true;

		return false;
	}

}

?>
