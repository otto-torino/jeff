<?php
/**
 * @file user.php
 * @brief Contains the model of the user module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup user_module
 * @brief User model class
 *
 * Model fields:
 * - **id** int(11): primary key
 * - **lastname** varchar(255): user lastname
 * - **firstname** varchar(255): user firstname
 * - **username** varchar(20): user login name
 * - **password** varchar(50): user login password
 * - **groups** varchar(64): comma separated list of user groups
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class user extends model {
	
	/**
	 * @brief Constructs a user instance
	 * 
	 * @return user instance
	 */
	function __construct($id) {
	
		$this->_tbl_data = TBL_USERS;

		parent::__construct($id);

	}
	
	/**
	 * @brief Get all user objects 
	 * 
	 * @param array $opts associative array of options (none for now) 
	 * @return array user objects
	 */
	public static function get($opts = array()) {
	
		$registry = registry::instance();

		$objs = array();
		$rows = $registry->db->autoSelect("id", TBL_USERS, '', 'lastname');
		foreach($rows as $row) $objs[] = new user($row['id']);

		return $objs;
	
	}
	
	/**
	 * @brief Get user object from username and password 
	 * 
	 * @param string $user username 
	 * @param string $pwd password
	 * @return mixed user object or null if not found
	 */
	public static function getFromAuth($user, $pwd) {

		$registry = registry::instance();
		
		if(PWD_HASH=='md5') $pwd_check = md5($pwd);	
		elseif(PWD_HASH=='sha1') $pwd_check = sha1($pwd);	
		else $pwd_check = $pwd;

		$qr = $registry->db->autoSelect(array("id"), array(TBL_USERS), "username='$user' AND password='".$pwd_check."'", null);

		return count($qr) ? new user($qr[0]['id']):null;

	
	}

}

?>
