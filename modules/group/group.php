<?php
/**
 * @file group.php
 * @brief Contains the model of the group module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.98
 * @date 2011-2012
 * @copyright Otto srl MIT License \see http://www.opensource.org/licenses/mit-license.php
 */

/**
 * @ingroup group_module
 * @brief User group model class
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.98
 * @date 2011-2012
 * @copyright Otto srl MIT License \see http://www.opensource.org/licenses/mit-license.php 
 */
class group extends model {
	
	/**
	 * @brief Constructs a group instance
	 * 
	 * @param mixed $id the object id (primary key value of the record)
	 * @return group instance
	 */
	function __construct($id) {
	
		$this->_tbl_data = TBL_SYS_GROUPS;

		parent::__construct($id);

	}

	/**
	 * @brief Get all groups objects 
	 * 
	 * @param array $opts: associative array of options (no one by now)
	 * @return array group objects
	 */
	public static function get($opts=null) {

		$registry = registry::instance();

		$objs = array();
		$rows = $registry->db->autoSelect("id", TBL_SYS_GROUPS, '', 'id');
		foreach($rows as $row) $objs[] = $row['id'];

		return $objs;
	
	}

}

?>
