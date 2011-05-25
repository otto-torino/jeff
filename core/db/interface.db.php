<?php

interface DbManager {

	function __construct($params);

	public function openConnection();
	public function escapeString($string);
	public function executeQuery($query);
	public function autoSelect($fields, $tables, $where, $order=null, $limit=null);
	public function getNumRecords($table, $where=null, $field='id');
	public function getFieldsName($table);
	public function getTableStructure($table);
	public function getTables($like=null);
	public function insert($table, $data);
	public function update($table, $data, $where);
	public function delete($table, $where);


}

?>
