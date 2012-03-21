<?php
/**
 * @file export.class.php
 * @brief Contains the export class.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup core
 * @brief Data exportation class
 *
 * This classed is used to export database tables or arrays of data to csv files.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class export {
	
	/**
	 * @brief the registry singleton instance 
	 */
	private $_registry;
	
	/**
	 * @brief data separator character  
	 */
	private $_s;
	
	/**
	 * @brief field enclosure character  
	 */
	private $_fe;

	/**
	 * @brief database table  
	 */
	private $_table;
	
	/**
	 * @brief table primary key field  
	 */
	private $_pkey;
	
	/**
	 * @brief special fields  
	 */
	private $_sfields;
	
	/**
	 * @brief foreign keys  
	 */
	private $_fkeys;

	/**
	 * @brief whether or not to print fields' headings  
	 */
	private $_head;

	/**
	 * @brief the fields to export
	 */
	private $_fields;

	/**
	 * @brief the records ids to export
	 */
	private $_rids;
	
	/**
	 * @brief the field used to sort the results  
	 */
	private $_order;
	
	/**
	 * @brief array of data to export  
	 */			
	private $_data;

	/**
	 * @brief Construct an export instance 
	 * 
	 * @param array $opts 
	 *   Associative array of options</b>:
	 *   - **separator**: string default ','. Data separator character
	 *   - **field_enclosure**: string default '"'. Field enclosure character 
	 *   - **table**: string. Database table 
	 *   - **pkey**: string default 'id'. Table primary key field 
	 *   - **sfields**: array default array(). Special fields, same as \ref adminTable::setSpecialFields 
	 *   - **fkeys**: array default array(). Foreign keys, same as \ref adminTable::setForeignKeys 
	 *   - **head**: bool default true. Whether or not to print fields' headings 
	 *   - **fields**: mixed default '*'. Fields to export. Possible values are: 
	 *     - *****: all fields
	 *     - **-(field1, field2)**: all fields except from field1 and field2
	 *     - **field1,field2**: the fields field1 and field2
	 *     - **array('field1', 'field2')**: the fields field1 and field2
	 *   - **rids**: mixed default '*'. Records to export. Possible values are: 
	 *     - *****: all records
	 *     - **-(field1, field2)**: all fields except from field1 and field2
	 *     - **1,3,5**: the records with id=1, id=3 and id=5
	 *     - **array(1,3,5)**: the records with id=1, id=3 and id=5
	 *   - **order**: string default ''. The field used to sort the results 
	 *   - **data**: array default array(). Arra of data to export. Competitive to table option, ie: 
	 *     array(
	 *       0=>array("head1", "head2", "head3"), 
	 *       1=>array("value1 record 1", "value 2 record 1", "value 3 record 1"), 
	 *       2=>array("value1 record 2", "value 2 record 2", "value 3 record 2")
	 *    )
	 * @return export instance
	 */
	function __construct($opts=array()) {

		$this->_registry = registry::instance();

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

	/**
	 * @brief Sets the $_table property 
	 * 
	 * @param string $table databse table 
	 * @return void
	 */
	public function setTable($table) {
		$this->_table = $table;	
	}
	
	/**
	 * @brief Sets the $_sfields property 
	 * 
	 * @param array $sfields special fields 
	 * @return void
	 */
	public function setSfields($sfields) {
		$this->_sfields = $sfields;	
	}
	
	/**
	 * @brief Set the $_fkeys property 
	 * 
	 * @param array $fkeys foreign keys 
	 * @return void
	 */
	public function setFkeys($fkeys) {
		$this->_fkeys = $fkeys;	
	}

	/**
	 * @brief Sets the $_s property 
	 * 
	 * @param string $s separator character
	 * @return void
	 */
	public function setSeparator($s) {
		$this->_s = $s;
	}
	
	/**
	 * @brief Sets the $_fe property 
	 * 
	 * @param string $fe field enclosure character
	 * @return void
	 */
	public function setFieldEnclosure($fe) {
		$this->_fe = $fe;
	}

	/**
	 * @brief Sets the $_fields property 
	 * 
	 * @param mixed $fields fields value (see @ref export::__construct)
	 * @return void
	 */
	public function setFields($fields) {
		$this->_fields = $fields;
	}

	/**
	 * @brief Sets the $_head property 
	 * 
	 * @param bool $head headings visibility (see @ref export::__construct)
	 * @return void
	 */
	public function setHead($head) {
		$this->_head = $head;
	}
	
	/**
	 * @brief Sets the $_rids property 
	 * 
	 * @param mixed $rids rids value (see @ref export::__construct)
	 * @return void
	 */
	public function setRids($rids) {
		$this->_rids = $rids;
	}

	/**
	 * @brief Sets the $_order property 
	 * 
	 * @param string $order sort order (see @ref export::__construct)
	 * @return void
	 */
	public function setOrder($order) {
		$this->_order = $order;
	}

	/**
	 * @brief Sets the $_data property 
	 * 
	 * @param array $data data value (see @ref export::__construct)
	 * @return void
	 */
	public function setData($data) {
		$this->_data = $data;
	}

	/**
	 * @brief Data exportation 
	 * 
	 * @param string $filename name of the exportation file
	 * @param string $extension extension of the exportation file
	 * @param string $output output type. Possible values are:
	 *   - stream: the data are streamed directly to the browser
	 *   - file: the data are saved to file
	 * @return void
	 */
	public function exportData($filename, $extension, $output='stream') {

		if($extension=='csv') return $this->exportCsv($filename, $output);
		// maybe other extensions in the future 
	} 

	/**
	 * @brief Data exportation in csv format 
	 * 
	 * @param string $filename name of the exportation file 
	 * @param mixed $output output type. Possible values are:
	 *   - stream: the data are streamed directly to the browser
	 *   - file: the data are saved to file
	 * @return void
	 */
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

	/**
	 * @brief Returns data inside an array
	 *
	 * If data are already given in an array form returns them, if data are stored in a database table
	 * reads them and puts them in an aray structure. 
	 * 
	 * @access private
	 * @return void
	 */
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
			if($tot_sf) $r = $at->parseSpecialFields($r, array("show_pwd"=>true, "mailto"=>false));
			$data[] = $r;
		}

		return $data;

	}

	/**
	 * @brief Get column names of the given fields from a database table 
	 * 
	 * @param array $table_structure database table structure as returned by mysql::getTableStructure 
	 * @return void
	 */
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

	/**
	 * @brief Encloses a string between the enclosire characters 
	 * 
	 * @param mixed $field field to enclose 
	 * @return void
	 */
	private function encloseField($field) {

		return $this->_fe.$field.$this->_fe;

	}

}

?>
