<?php

class privilege extends model {

	private $_user;

	function __construct($id) {
	
		$this->_tbl_data = TBL_SYS_PRIVILEGES;

		parent::__construct($id);

	}

	public static function get($registry, $opts=null) {
	
		$objs = array();
		$rows = $registry->db->autoSelect("id", TBL_SYS_PRIVILEGES, '', 'id');
		foreach($rows as $row) $objs[] = $row['id'];

		return $objs;
	
	}

}

?>
