<?php

class group extends model {

	function __construct($id) {
	
		$this->_tbl_data = TBL_SYS_GROUPS;

		parent::__construct($id);

	}

	public static function get($registry, $opts=null) {
	
		$objs = array();
		$rows = $registry->db->autoSelect("id", TBL_SYS_GROUPS, '', 'id');
		foreach($rows as $row) $objs[] = $row['id'];

		return $objs;
	
	}

}

?>
