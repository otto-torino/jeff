<?php
/**
 * @file privilege.php
 * @brief Contains the model of the privilege module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup privilege_module
 * @brief privilege model class
 *
 * Model fields:
 * - **id** int(11): primary key
 * - **category** varchar(128): privilege category
 * - **class** varchar(64): privilege class
 * - **class_id** int(8): privilege class identifier
 * - **label** varchar(128): privilege label
 * - **description** text: privilege description
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class privilege extends model {

	/**
	 * @brief Constructs a privilege model instance
	 * 
	 * @param mixed $id the object id (primary key value of the record)
	 * @return privilege instance
	 */
	function __construct($id) {
	
		$this->_tbl_data = TBL_SYS_PRIVILEGES;

		parent::__construct($id);

	}
	
	/**
	 * @brief Get privilege objects 
	 * 
	 * @param array $opts associative array of options (none for now)
	 * @return array privilege objects
	 */
	public static function get($opts=null) {
	
		$registry = registry::instance();

		$objs = array();
		$rows = $registry->db->autoSelect("id", TBL_SYS_PRIVILEGES, '', 'id');
		foreach($rows as $row) $objs[] = $row['id'];

		return $objs;
	
	}

}

?>
