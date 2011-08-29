<?php
/**
 *
 *  Class export
 *
 *  Properties:
 *
 *  (object) _registry: the registry object
 *  (string) _s default ",": the field separator, default to comma
 *  (string) _table: the name of the table to export
 *  (string) _pkey: the primary key field
 *  (string) _sfields: special fields
 *  (string) _fkeys: foreign keys
 *  (mixed)  _fields: the fields to export:
 *                   *: all fields
 *                   * -(field1,field2): all fields except from field1 and field2
 *                   field1,field2: the fields field1 and field2
 *                   array("field1", "field2"): the fields field1 and field2
 *  (bool)   _head: whether or not to print fields' headings
 *  (mixed)  _rids: the records ids to export:
 *  		     *: all records
 *  		     1,3,5: the records with id=1, id=3 and id=5
 *  		     array(1,3,5): the records with id=1, id=3 and id=5 
 *  (string) _order: the field to order the query results by
 *  (array)  _data: competitive to _table: the array containing the data to export:
 *                   array(0=>array("head1", "head2", "head3"), 
 *                         1=>array("value1 record 1", "value 2 record 1", "value 3 record 1"), 
 *                         2=>array("value1 record 2", "value 2 record 2", "value 3 record 2")
 *                   )
 *
 *  Methods are provided to set all these properties:
 *
 *  -setTable
 *  -setSfields
 *  -setFkeys
 *  -setSeparator
 *  -setFields
 *  -setHead
 *  -setRids
 *  -setOrder
 *  -setData
 *
 *  Output method:
 *
 *  exportData($filename, $extension, output) : (file)
 *    (string) filename: the name of the file written (the absolute path if the output is file)
 *    (string) extension: the file extension 
 *    (string) output: file|stream 
 *
 *
**/
class export {

	private $_registry;
	private $_s, $_fe, $_table, $_pkey, $_sfields, $_fkeys, $_head, $_fields, $_rids, $_order, $_data;

	function __construct($registry, $opts=array()) {

		$this->_registry = $registry;

		$this->_s = gOpt($opts, 'separator', ',');
		$this->_fe = gOpt($opts, 'field_enclosure', '"');
		$this->_table = gOpt($opts, 'table', '');
		$this->_pkey = gOpt($opts, 'pkey', 'id');
		$this->_sfields = gOpt($opts, 'sfields', array());
		$this->_fkeys = gOpt($opts, 'fkeys', array());
		$this->_head = gOpt($opts, 'head', true);
		$this->_fields = gOpt($opts, 'fields', '*');
		$this->_rids = gOpt($opts, 'rids', '*');
		$this->_order = gOpt($opts, 'order', '');
		$this->_data = gOpt($opts, 'data', array());

	}

	public function setTable($table) {
		$this->_table = $table;	
	}
	
	public function setSfields($sfields) {
		$this->_sfields = $sfields;	
	}
	
	public function setFkeys($fkeys) {
		$this->_fkeys = $fkeys;	
	}

	public function setSeparator($s) {
		$this->_s = $s;
	}
	
	public function setFieldEnclosure($fe) {
		$this->_fe = $fe;
	}

	public function setFields($fields) {
		$this->_fields = $fields;
	}

	public function setHead($head) {
		$this->_head = $head;
	}

	public function setRids($rids) {
		$this->_rids = $rids;
	}

	public function setOrder($order) {
		$this->_order = $order;
	}

	public function setData($data) {
		$this->_data = $data;
	}

	public function exportData($filename, $extension, $output='stream') {

		if($extension=='csv') return $this->exportCsv($filename, $output);
		// maybe other extensions in the future 
	} 

	private function exportCsv($filename, $output) {
		
		$data = $this->getData();

		$csv = '';
		foreach($data as $row) {
			$cell = array();
			foreach($row as $v) $cell[] = $this->encloseField($v);
			$csv .= implode($this->_s, $cell)."\r\n";
		}

		ob_clean();

		if($output=='stream') { 
			header("Content-Type: plain/text");
			header("Content-Disposition: Attachment; filename=$filename");

			header("Pragma: no-cache");
			echo $csv;
			exit;
		}
		elseif($output=='file') {
			$fo = fopen($filename, "w");
			fwrite($fo, $csv);
			fclose($fo);
		}

	}

	private function getData() {

		if($this->_data) return $this->_data;
		if(!$this->_table) return array();

		$tot_fk = count($this->_fkeys);
		$tot_sf = count($this->_sfields);
		$data = array();
		$table_structure = $this->_registry->db->getTableStructure($this->_table);

		$head_fields = $this->getHeadFields($table_structure);
		if(count($head_fields) && $this->_head) $data[] = $head_fields;

		if($this->_rids=='*') $where = '';
		elseif(is_array($this->_rids) && count($this->_rids)) 
			$where = $this->_pkey."='".implode("' OR id='", $this->_rids)."'";
		elseif(is_string($this->_rids) && strlen($this->_rids)>0)	
			$where = $this->_pkey."='".implode("' OR id='", explode(",",$this->_rids))."'";

		$order = $this->_order ? $this->_order : null;

		$at = new adminTable($this->_registry, $this->_table);
		$at->setForeignKeys($this->_fkeys);
		$at->setSpecialFields($this->_sfields);
		$results = $this->_registry->db->autoSelect($head_fields, $this->_table, $where, $order);
		foreach($results as $r) { 
			if($tot_fk) $r = $at->parseForeignKeys($r);
			if($tot_sf) $r = $at->parseSpecialFields($r, array("show_pwd"=>true));
			$data[] = $r;
		}

		return $data;

	}

	private function getHeadFields($table_structure) {
		
		if($this->_head && is_string($this->_fields) && preg_match("#\*#", $this->_fields)) {
			preg_match("#\* -\((.*)\)#", $this->_fields, $matches);
			$excluded_fields = isset($matches[1]) ? explode(",",$matches[1]):array();
			$head_fields = array();
			foreach($table_structure['fields'] as $field=>$info) {
				if(!in_array($field, $excluded_fields)) $head_fields[] = $field;
			}
		}
		elseif(is_string($this->_fields)) $head_fields = explode(",",$this->_fields);
		elseif(is_array($this->_fields)) $head_fields = $this->_fields;

		return $head_fields;

	}

	private function encloseField($field) {

		return $this->_fe.$field.$this->_fe;

	}

}

?>
