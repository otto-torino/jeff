<?php

class group extends model {


	function __construct($registry, $id) {
	
		$this->_registry = $registry;
		$this->_tbl_data = TBL_SYS_GROUPS;
		parent::__construct($this->initP($id));

	}

	private function initP($id) {

		return $this->initDbProp($id);

	}

	public static function get($registry, $opts=null) {
	
		$objs = array();
		$rows = $registry->db->autoSelect("id", TBL_SYS_GROUPS, '', 'id');
		foreach($rows as $row) $objs[] = $row['id'];

		return $objs;
	
	}

	public function save() {
	
	}
	

}

?>
