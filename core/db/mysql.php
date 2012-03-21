<?php
/**
 * @file mysql.php
 * @brief Contains the MySQL client definition.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup database
 * @brief MySQL client class. 
 * 
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class mysql implements DbManager {

	/**
	 * @brief database host 
	 */
	private $_db_host;

	/**
	 * @brief database user 
	 */
	private $_db_user;

	/**
	 * @brief database password 
	 */
	private $_db_pass;

	/**
	 * @brief database name
	 */
	private $_db_dbname;

	/**
	 * @brief database charset 
	 */
	private $_db_charset;

	/**
	 * @brief MySQL link identifier on success connection, false on failure 
	 */
	private $_connection;

	/**
	 * @brief Constructs a class instance 
	 * 
	 * @param array $params the connection parameters (host, user, password, db_name, charset, connect) as key=>value  
	 * @return void
	 */
	function __construct($params) {
		
		$this->_db_host  = $params["host"];
		$this->_db_user  = $params["user"];
		$this->_db_pass  = $params["password"];
		$this->_db_dbname  = $params["db_name"];
		$this->_db_charset  = $params["charset"];

		if($params["connect"]===true) {
			$this->openConnection();
		}

	}

	/**
	 * @brief Escapes the input string for safe database insertion 
	 * 
	 * @param string $string 
	 * @return the input string escaped
	 */
	public function escapeString($string) {

		return mysql_real_escape_string($string);

	}

	/**
	 * @brief Opens the connection with the DB HOST, exits on error. 
	 * 
	 * @return void
	 */
	public function openConnection() {
		
		if($this->_connection = mysql_connect($this->_db_host, $this->_db_user, $this->_db_pass)) {
			
			@mysql_select_db($this->_db_dbname, $this->_connection) 
				OR exit("Db selection error");
			
			if($this->_db_charset=='utf8') $this->setUtf8();
		
		} 
		else 
			exit("Db connection error");
	}
	
	/**
	 * @brief Sets the database in order to work with utf8 chars 
	 * 
	 * @return void
	 */
	private function setUtf8() {

		$db_charset = $this->executeQuery( "SHOW VARIABLES LIKE 'character_set_database'" );
		$charset_row = mysql_fetch_assoc( $db_charset );
		$this->executeQuery( "SET NAMES '" . $charset_row['Value'] . "'" );
		unset( $db_charset, $charset_row );

	}

	/**
	 * @brief Executes a query on the active database 
	 * 
	 * @param string $query the query to execute 
	 * @return the query result as a resource, or bool value.
	 */
	public function executeQuery($query) {
	
		$res = mysql_query($query);

		return $res;

	}

	/**
	 * @brief Returns the query result with accessible data. 
	 * 
	 * @param string $query the query to execute
	 * @return the query result
	 */
	public function queryResult($query) {

		$results = array();

		$res = $this->executeQuery($query);

		if($res) {
			while($row = mysql_fetch_assoc($res)) {
				$results[] = $row;
			}
			mysql_free_result($res);
		}

		return $results;

	}

	/**
	 * @brief Returns the error from the last MySQL function in a custom format
	 * 
	 * @return associative array containing error information 
	 */
	public function getError() {
		
		$error = mysql_error();

		// duplicate
		if(preg_match("#^Duplicate entry '(.*?)' .*? key (\d+)$#", $error, $matches)) {
			return array("error"=>1001, "key"=>$matches[2], "value"=>$matches[1]);
		}

	}

	/**
	 * @brief Executes a select statement on the active database, and returns the result. 
	 * 
	 * @param mixed $fields the fields to be selected. Possible values: array of fields or string.
	 * @param mixed $tables the table/s from which retrieve records. Possible values: array of tables, string.
	 * @param string $where the where clause
	 * @param string $order the order by clause
	 * @param array $limit the limit clause
	 * @return the associative array with the select statement result
	 */
	public function autoSelect($fields, $tables, $where, $order=null, $limit=null) {
	
		$qfields = is_array($fields) ? implode(",", $fields):$fields;
		$qtables = is_array($tables) ? implode(",", $tables):$tables;
		$qwhere = $where ? "WHERE ".$where : "";
		$qorder = $order ? "ORDER BY $order":"";
		$qlimit = count($limit) ? "LIMIT ".$limit[0].",".$limit[1]:"";

		$query = "SELECT $qfields FROM $qtables $qwhere $qorder $qlimit";

		return $this->queryResult($query);

	}

	/**
	 * @brief Returns the number of records in the given table selectyed by the given where clause 
	 * 
	 * @param string $table the database table
	 * @param mixed $where the where clause
	 * @param string $field the field used for counting
	 * @return number of records
	 */
	public function getNumRecords($table, $where=null, $field='id') {

		$tot = 0;

		$qwhere = $where ? "WHERE ".$where : "";
		$query = "SELECT COUNT($field) AS tot FROM $table $qwhere";
		$res = $this->executeQuery($query);
		if($res) {
			while($row = mysql_fetch_assoc($res)) {
				$tot = $row['tot'];
			}
			mysql_free_result($res);
		}

		return (int) $tot;

	}

	/**
	 * @brief Returns the name of the fields of the given table
	 * 
	 * @param string $table the database table
	 * @return array containing the name of the fields
	 */
	public function getFieldsName($table) {

		$fields = array();
		$query = "SHOW COLUMNS FROM ".$table;
		
		$res = $this->executeQuery($query);
		while($row = mysql_fetch_assoc($res)) {
			$results[] = $row;
		}
		mysql_free_result($res);

		foreach($results as $r) {$fields[] = $r['Field'];}

		return $fields;

	}

	/**
	 * @brief Returns information about the structure of a table.
	 *
	 * @param string $table the database table
	 * @return 
	 *   information about the table structure. \n
	 *   The returned array is in the form \n
	 *   array("field_name" => array("property" => "value"), "primary_key" => "primary_key_name", "keys" => array("keyname1", "keyname2")) \n
	 *   Returned properties foreach field:
	 *   - **order**: the ordinal position
	 *   - **deafult**: the default value
	 *   - **null**: whether the field is nullable or not
	 *   - **type**: the field type (varchar, int, text, ...)
	 *   - **max_length**: the field max length
	 *   - **n_int**: the number of int digits
	 *   - **n_precision**: the number of decimal digits
	 *   - **key**: the field key if set
	 *   - **extra**: extra information
	 */
	public function getTableStructure($table) {

		$structure = array("primary_key"=>null, "keys"=>array());
		$fields = array();

		$query = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$this->_db_dbname."' AND TABLE_NAME = '$table'";
		$res = $this->executeQuery($query);
		
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
			if($row['COLUMN_KEY']!='') $structure['keys'][] = $row['COLUMN_NAME'];
		}
		$structure['fields'] = $fields;

		return $structure;
	}

	/**
	 * @brief Returns the tables found in the connected database 
	 * 
	 * @param string $like the like clause, defaut null. 
	 * @return array of table names
	 */
	public function getTables($like=null) {

		$tables = array();
		$query = "SHOW TABLES".($like ? " LIKE '$like'":'');
		
		$res = $this->executeQuery($query);
		while($row = mysql_fetch_array($res)) {
			$results[] = $row;
		}
		mysql_free_result($res);

		foreach($results as $r) {$tables[] = $r[0];}

		return $tables;

	}

	/**
	 * @brief Inserts the given data in the given table
	 * 
	 * @param string $table the database table
	 * @param array $data the data to insert in the form array('field_name'=>'value')
	 * @return the last inserted id on success, false on failure
	 */
	public function insert($table, $data) {
	
		$fields = array();
		$values = array();

		foreach($data as $f=>$v) {
			$fields[] = $f;
			$values[] = "'$v'";
		}

		$query = "INSERT INTO ".$table." (`".implode("`,`", $fields)."`) VALUES (".implode(",", $values).")"; 
		$result = $this->executeQuery($query);

		return $result ? $this->lastInsertedId() : false;
	}

	/**
	 * @brief Updates the given table with the given data using the given where clause 
	 * 
	 * @param string $table 
	 * @param array $data the data to update in the form array('field_name'=>'value')
	 * @param string $where the where clause
	 * @return boolean value: true on success, false on failure
	 */
	public function update($table, $data, $where) {
	
		if(!$data) return true;

		$sets = array();
		foreach($data as $f=>$v) $sets[] = is_null($v) ? "`$f`=NULL" : "`$f`='$v'";
		$query = "UPDATE ".$table." SET ".implode(",", $sets)." ".($where ? "WHERE $where":"");

		$result = $this->executeQuery($query);

		return $result;
	}

	/**
	 * @brief Returns the last inserted id 
	 * 
	 * @return the last inserted id or false
	 */
	private function lastInsertedId() {

		if(mysql_affected_rows()) return mysql_insert_id();
		else return false;

	}

	/**
	 * @brief Deletes records from the given table using the given where clause. 
	 * 
	 * @param string $table the database table
	 * @param mixed $where the where clause
	 * @return boolean value: true on success, false on failure
	 */
	public function delete($table, $where) {

		$query = "DELETE FROM $table ".($where ? "WHERE $where":"");

		$result = $this->executeQuery($query);

		return $result;
	}
}


?>
