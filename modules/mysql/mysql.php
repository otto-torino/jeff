<?php

class mysql implements DbManager {

	private $_db_host, $_db_user, $_db_pass, $_db_dbname, $_db_charset;

	private $_connection;

	/*
	 *  @param $params: array(
	 *  	"connect"=>true,
	 *  	"host"=>"localhost",
	 *  	"user"=>"root",
	 *  	"password"=>"",
	 *  	"db_name"=>"db_name",
	 *  	"charset"=>"utf8"	
	 *  )
	 */
	function __construct($params) {
		
		$this->_db_host  = $params["host"];
		$this->_db_user  = $params["user"];
		$this->_db_pass  = $params["password"];
		$this->_db_dbname  = $params["db_name"];
		$this->_db_charset  = $params["charset"];

		if($params["connect"]===true) $this->openConnection();

	}

	public function escapeString($string) {

		return mysql_real_escape_string($string);

	}

	public function openConnection() {
		
		if($this->_connection = mysql_connect($this->_db_host, $this->_db_user, $this->_db_pass)) {
			
			if($this->_db_charset=='utf8') $this->setUtf8();
			@mysql_select_db($this->_db_dbname, $this->_connection) 
				OR Error::syserrorMessage('MySql', 'openConnection', __("DbSelectionError"), __LINE__);
		
		} 
		else 
			Error::syserrorMessage('MySql', 'openConnection', __("DbConnectionError"), __LINE__);
	}
	
	private function setUtf8() {

		$db_charset = mysql_query( "SHOW VARIABLES LIKE 'character_set_database'" );
		$charset_row = mysql_fetch_assoc( $db_charset );
		mysql_query( "SET NAMES '" . $charset_row['Value'] . "'" );
		unset( $db_charset, $charset_row );

	}

	public function autoSelect($fields, $tables, $where, $order=null, $limit=null) {
	
		$results = array();

		$qfields = is_array($fields) ? implode(",", $fields):$fields;
		$qtables = is_array($tables) ? implode(",", $tables):$tables;
		$qwhere = $where ? "WHERE ".$where : "";
		$qorder = $order ? "ORDER BY $order":"";
		$qlimit = count($limit) ? "LIMIT ".$limit[0].",".$limit[1]:"";

		$query = "SELECT $qfields FROM $qtables $qwhere $qorder $qlimit";
		$res = mysql_query($query);
		if($res) {
			while($row = mysql_fetch_assoc($res)) {
				$results[] = $row;
			}
			mysql_free_result($res);
		}

		return $results;

	}

	public function getNumRecords($table, $where=null, $field='id') {

		$tot = 0;

		$qwhere = $where ? "WHERE ".$where : "";
		$query = "SELECT COUNT($field) AS tot FROM $table $qwhere";
		$res = mysql_query($query);
		if($res) {
			while($row = mysql_fetch_assoc($res)) {
				$tot = $row['tot'];
			}
			mysql_free_result($res);
		}

		return $tot;

	}

	public function getFieldsName($table) {

		$fields = array();
		$query = "SHOW COLUMNS FROM ".$table;
		
		$res = mysql_query($query);
		while($row = mysql_fetch_assoc($res)) {
			$results[] = $row;
		}
		mysql_free_result($res);

		foreach($results as $r) {$fields[] = $r['Field'];}

		return $fields;

	}

	public function getTableStructure($table) {

		$structure = array("primary_key"=>null);
		$fields = array();

		$query = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$this->_db_dbname."' AND TABLE_NAME = '$table'";
		$res = mysql_query($query);
		
		while($row = mysql_fetch_array($res)) {
			preg_match("#(\w+)\((\d+),?(\d+)?\)#", $row['COLUMN_TYPE'], $matches);
			$fields[$row['COLUMN_NAME']] = array(
				"order"=>$row['ORDINAL_POSITION'],
				"default"=>$row['COLUMN_DEFAULT'],
				"null"=>$row['IS_NULLABLE'],
				"type"=>$row['DATA_TYPE'],
				"max_length"=>$row['CHARACTER_MAXIMUM_LENGTH'],
				"n_int"=>isset($matches[2]) ? $matches[2] : 0,
				"n_precision"=>isset($matches[3]) ? $matches[3] : 0,
				"key"=>$row['COLUMN_KEY'],
				"extra"=>$row['EXTRA']
			);
			if($row['COLUMN_KEY']=='PRI') $structure['primary_key'] = $row['COLUMN_NAME'];
		}
		$structure['fields'] = $fields;

		return $structure;
	}

	public function getTables($like=null) {

		$tables = array();
		$query = "SHOW TABLES".($like ? " LIKE '$like'":'');
		
		$res = mysql_query($query);
		while($row = mysql_fetch_array($res)) {
			$results[] = $row;
		}
		mysql_free_result($res);

		foreach($results as $r) {$tables[] = $r[0];}

		return $tables;

	}

	public function insert($table, $data) {
	
		$fields = array();
		$values = array();

		foreach($data as $f=>$v) {
			$fields[] = $f;
			$values[] = "'$v'";
		}

		$query = "INSERT INTO ".$table." (".implode(",", $fields).") VALUES (".implode(",", $values).")"; 
		$result = mysql_query($query);

		return $result ? $this->lastInsertedId() : false;
	}

	public function update($table, $data, $where) {
	
		$sets = array();
		foreach($data as $f=>$v) $sets[] = is_null($v) ? "$f=NULL" : "$f='$v'";
		$query = "UPDATE ".$table." SET ".implode(",", $sets)." ".($where ? "WHERE $where":"");

		$result = mysql_query($query);

		return $result;
	}

	private function lastInsertedId() {

		if(mysql_affected_rows()) return mysql_insert_id();
		else return false;

	}

	public function delete($table, $where) {

		$query = "DELETE FROM $table ".($where ? "WHERE $where":"");

		$result = mysql_query($query);

		return $result;
	}
}


?>
