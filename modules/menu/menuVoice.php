<?php
/**
 * @file menuVoice.php
 * @brief Contains the model of the menu module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup menu_module
 * @brief Menu voice model class
 *
 * Model fields:
 * - **id** int(8): primary key
 * - **parent** int(4): parent id, null or 0 if the voice has not a parent
 * - **label** varchar(128): voice label
 * - **url** varchar(128): link url
 * - **target** varchar(8): link target, '_self' or '_blank'
 * - **position** int(3): sort order
 * - **groups** varchar(128): comma separated list of user groups which can see the voice (everyone can see it if this field is null or empty)
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class menuVoice extends model {
	
	/**
	 * @brief Constructs a menu voice instance
	 * 
	 * @param mixed $id the object id (primary key value of the record)
	 * @return menu voice instance
	 */
	function __construct($id) {
	
		$this->_tbl_data = TBL_MENU;
		parent::__construct($id);

	}
	
	/**
	 * @brief Gets menu voices objects 
	 * 
	 * @param array $opts: associative array of options
	 * -**where**: the where clause
	 * -**order**: the order clause
	 * @return array menu voices objects
	 */
	public static function get($opts=null) {
	    
		$registry = registry::instance();

	    	$objs = array();

		$where = gOpt($opts, "where", ""); 
		$order = gOpt($opts, "order", null); 

		$rows = $registry->db->autoSelect("id", TBL_MENU, $where, $order, null);
		foreach($rows as $row) $objs[] = new menuVoice($row['id']);

		return $objs;

	}

}

?>
