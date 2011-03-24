<?php

class user extends model {


	function __construct($registry, $id) {
	
		$this->_registry = $registry;
		$this->_tbl_data = TBL_USERS;
		parent::__construct($this->initP($id));

	}

	private function initP($id) {

		return $this->initDbProp($id);

	}
	
	public static function getAll($registry, $opts=null) {
	
		$objs = array();
		$rows = $registry->db->autoSelect("id", TBL_USERS, '', 'lastname');
		foreach($rows as $row) $objs[] = new user($registry, $row['id']);

		return $objs;
	
	}

	public static function getFromAuth($registry, $user, $pwd) {
	
		$qr = $registry->db->autoSelect(array("id"), array(TBL_USERS), "username='$user' AND password='".md5($pwd)."'", null);

		return count($qr) ? new user($registry, $qr[0]['id']):null;

	
	}

}

?>
