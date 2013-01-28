<?php
/**
 * @file access.class.php
 * @brief Contains the access class.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup security Authentication and privileges
 *
 * ### Authentication
 * The login/logout actions are checked before rendering the document. This work is carried out by the @ref authentication class.
 *
 * ### Privileges 
 * Methods and functionality may be protected setting some access privileges. Access to such functionality is then granted if the user belongs to a group having such privileges.
 * Who checks if the user may access the protected content is the @ref access class.
 *
 * Some general privileges exists by default (public view, private view, admin view, admin manage).
 * The specific privileges depends on the module itself and are stored in the database table 'sys_privileges'.
 */

/**
 * @ingroup security core
 * @brief Class used to check the user privileges
 *
 * Provides methods to check if the user may access the desired content.
 *
 * Jeff privileges are defined by a 'class' and an 'identifier'. So each class may use as many privileges as needed, each one with a different identifier.
 * All the available privileges are stores in the database table sys_privileges.
 * Since in many cases it's enough to deal with generic privileges Jeff has some default and reserved privileges classes and identifiers, 
 * which may be checked using the following class parameters and null as $pids.
 * - **public_view**: generic privilege which allows to view public contents
 * - **private_view**: generic privilege which allows to view private contents (the user must be logged in)
 * - **admin_view**: generic privilege which allows to view the administrative area
 * - **admin**: complete administration of the web application 
 * 
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class access {

	/**
	 * @brief Checks if the user has the right privileges to acess the requested content 
	 *
	 * @param string $class the privilege class (a standard identifier or a real class) 
	 * @param mixed $pids the privilege identifier for the given class
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **exitOnFailure**: whether to redirect to the noaccess page if the user doesn't have the right privileges. 
	 * @return true if the user has the right privileges, false or redirect to the no access page otherwise.
	 */
	public static function check($class=null, $pids=null, $opts=null) {

		$registry = registry::instance();
		
		$user = $registry->user;

		if($class == 'public_view') {
			$class = 'main';
			$pids = $registry->public_view_privilege;
		}
		elseif($class == 'private_view') {
			$class = 'main';
			$pids = $registry->private_view_privilege;
		}
		elseif($class == 'admin_view') {
			$class = 'main';
			$pids = $registry->admin_view_privilege;
		}
		elseif($class == 'admin') {
			$class = 'main';
			$pids = $registry->admin_privilege;
		}

		$user_privileges = array();
		foreach(explode(",", $user->groups) as $ugid) {
			$ug = new group($ugid);
			if($ug->privileges) {
				foreach(explode(",", $ug->privileges) as $gpid) {
					$p = new privilege($gpid);
					$user_privileges[$p->class][] = $p->class_id;
				}
			}
		}
		if(!is_array($pids)) $pids = array($pids);

		$access = false;
		foreach($pids as $pid) {
			if((isset($user_privileges[$class]) && in_array($pid, $user_privileges[$class])) || 
				(isset($user_privileges['main']) && in_array($registry->admin_privilege, $user_privileges['main']))) {
				$access = true;
				break;
			}
		}

		if((!$access || !$user->active) && gOpt($opts, 'exitOnFailure')) {
		    header("Location: ".$registry->router->linkHref('noaccess', null));
		    exit();
		}
		return $access;

	}

	/**
	 * @brief Checks if the user belongs to at least one of the given groups. 
	 * 
	 * @param array $group_ids array containing all the identifiers of the groups to check.
	 * @return true if the user belongs to at least one group, false otherwise
	 */
	public static function hasGroup($group_ids) {
	
		$registry = registry::instance();

		$user = $registry->user;

		if(!is_array($group_ids)) {
			$group_ids = array($group_ids);
		}

		foreach($group_ids as $group_id) {
			if(preg_match("#\b".preg_quote($group_id)."\b#", $user->groups)) return true;
		}

		return false;

	}


}

?>
