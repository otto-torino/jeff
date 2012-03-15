<?php
/**
 * \file interface.db.php
 * \brief Contains the database client interface.
 *
 * Defines a common interface for all db client classes
 *
 * @version 0.98
 * @copyright 2011 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors abidibo abidibo@gmail.com
 */

/**
 * \ingroup database core
 * \brief Database client interface. 
 *
 * Defines a common interface for all db client classes.
 *
 * @abstract
 * @version 0.98
 * @copyright 2011 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors abidibo abidibo@gmail.com 
 */
interface DbManager {

	function __construct($params);

	/**
	 * Opens the connection with the DB HOST, exits on error. 
	 * 
	 * @return void
	 */
	public function openConnection();

	/**
	 * Escapes the input string for safe database insertion 
	 * 
	 * @param string $string 
	 * @return string the input string escaped
	 */
	public function escapeString($string);
	
	/**
	 * Executes a query on the active database 
	 * 
	 * @param string $query the query to execute 
	 * @return the query result as a resource or a bool value.
	 */
	public function executeQuery($query);

	/**
	 * Returns the query result with accessible data. 
	 * 
	 * @param string $query the query to execute
	 * @return the query result
	 */
	public function queryResult($query);
	
	/**
	 * Returns the error from the last executed function in a custom format
	 * 
	 * @return associative array containing error information 
	 */
	public function getError();
	
	/**
	 * Executes a select statement on the active database, and returns the result. 
	 * 
	 * @param mixed $fields the fields to be selected. Possible values: array of fields or string.
	 * @param mixed $tables the table/s from which retrieve records. Possible values: array of tables, string.
	 * @param string $where the where clause
	 * @param string $order the order by clause
	 * @param array $limit the limit clause
	 * @return the associative array with the select statement result
	 */
	public function autoSelect($fields, $tables, $where, $order=null, $limit=null);
	
	/**
	 * Returns the number of records in the given table selectyed by the given where clause 
	 * 
	 * @param string $table the database table
	 * @param mixed $where the where clause
	 * @param string $field the field used for counting
	 * @return number of records
	 */
	public function getNumRecords($table, $where=null, $field='id');

	/**
	 * Returns the name of the fields of the given table
	 * 
	 * @param string $table the database table
	 * @return array containing the name of the fields
	 */
	public function getFieldsName($table);
	
	/**
	 * Returns information about the structure of a table.
	 *
	 * @param string $table the database table
	 * @return 
	 *   information about the table structure.<br />
	 *   The returned array is in the form<br />
	 *   array("field_name" => array("property" => "value"), "primary_key" => "primary_key_name", "keys" => array("keyname1", "keyname2")) <br />
	 *   Returned properties foreach field:
	 *   - order: the ordinal position
	 *   - deafult: the default value
	 *   - deafult value
	 *   - null: whether the field is nullable or not
	 *   - type: the field type (varchar, int, text, ...)
	 *   - max_length: the field max length
	 *   - n_int: the number of int digits
	 *   - n_precision: the number of decimal digits
	 *   - key: the field key if set
	 *   - extra: extra information
	 */
	public function getTableStructure($table);
	
	/**
	 * Returns the names of the tables found in the connected database 
	 * 
	 * @param string $like the like clause, defaut null. 
	 * @return array of table names
	 */
	public function getTables($like=null);
	
	/**
	 * Inserts the given data in the given table
	 * 
	 * @param string $table the database table
	 * @param array $data the data to insert in the form array('field_name'=>'value')
	 * @return the last inserted id on success, false on failure
	 */
	public function insert($table, $data);
	
	/**
	 * Updates the given table with the given data using the given where clause 
	 * 
	 * @param string $table 
	 * @param array $data the data to update in the form array('field_name'=>'value')
	 * @param string $where the where clause
	 * @return boolean value: true on success, false on failure
	 */
	public function update($table, $data, $where);
	
	/**
	 * Deletes records from the given table using the given where clause. 
	 * 
	 * @param string $table the database table
	 * @param mixed $where the where clause
	 * @return boolean value: true on success, false on failure
	 */
	public function delete($table, $where);


}

?>
