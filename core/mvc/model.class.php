<?php

class model {

	protected $_p, $_chgP = array();
	protected $_tbl_data;
	protected $_registry;
	protected $_id_name = 'id';

	function __construct($data) {
		
		$this->_p = $data;

	}

	public function setIdName($id_name) {

		$this->_id_name = $id_name;

	}

	public function setRegistry($registry) {

		$this->_registry = $registry;

	}

	public function setTable($table) {
		
		$this->_tbl_data = $table;

	}

	protected function initDbProp($id) {

		$qr = $this->_registry->db->autoSelect(array("*"), array($this->_tbl_data), "id='$id'", null);
		if(count($qr)) return $qr[0];
		else return $this->initNullProp();

	}

	protected function initNullProp() {

		$res = array();
		$fields = $this->_registry->db->getFieldsName($this->_tbl_data);
		foreach($fields as $f) $res[$f] = null;

		return $res;

	}

	public function __get($pName) {

		if(!array_key_exists($pName, $this->_p)) return null;
		if(method_exists($this, 'get'.$pName)) return $this->{'get'.$pName}();
		else return $this->_p[$pName];
	}
	
	public function __set($pName, $value) {

		if(!array_key_exists($pName, $this->_p)) return null;
		if(method_exists($this, 'set'.$pName)) return $this->{'set'.$pName}($value);
		else {
			if($this->_p[$pName]!=$value && !in_array($pName, $this->_chgP)) $this->_chgP[] = $pName;
			$this->_p[$pName] = $value;
		}
	}

	public function saveData($insert=false) {
	
		$data = array();
		foreach($this->_chgP as $f) $data[$f] = $this->_p[$f];

		if(!$this->_p[$this->_id_name] || $insert) $this->_p[$this->_id_name] = $this->_registry->db->insert($this->_tbl_data, $data);
		else $this->_registry->db->update($this->_tbl_data, $data, $this->_id_name."='".$this->_p[$this->_id_name]."'");
	}

	public function deleteData() {
	
		return $this->_registry->db->delete($this->_tbl_data, $this->_id_name."=".$this->_p[$this->_id_name]);
	}

}

?>
