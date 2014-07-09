<?php
/**
 * @file db.class.php
 * @brief Contains the main database class implementation. All specific DBMS classes will inherit from this class, overriding specific methods in order to implement the right DBMS syntax.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2014
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup database
 * @brief Database client class. 
 * 
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2014
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class Database {

    /**
     * @brief dbms
     */
    protected $_dbms;

    /**
     * @brief database host 
     */
    protected $_db_host;

    /**
     * @brief database user 
     */
    protected $_db_user;

    /**
     * @brief database password 
     */
    protected $_db_pass;

    /**
     * @brief database name
     */
    protected $_db_dbname;

    /**
     * @brief database charset 
     */
    protected $_db_charset;

    /**
     * @brief PDO instance
     */
    protected $_db;

    /**
     * @brief returns a singleton db instance 
     * 
     * @return the singleton instance
     */
    function __construct($params) {

        $this->_dbms  = $params["dbms"];
        $this->_db_host  = $params["host"];
        $this->_db_user  = $params["user"];
        $this->_db_pass  = $params["password"];
        $this->_db_dbname  = $params["db_name"];
        $this->_db_charset  = $params["charset"];

        $connection_params = sprintf('%s:host=%s;dbname=%s', $this->_dbms, $this->_db_host, $this->_db_dbname);
        try {
            $this->_db = new PDO($connection_params , $this->_db_user, $this->_db_pass);
            $this->_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if($this->_db_charset=='utf8') {
                $this->_db->query("SET NAMES utf8");
            }
        }
        catch(PDOException $e) {
            Error::syserrorMessage(get_class($this), '__construct', $e->getMessage(), __LINE__);
        }
    }

    /**
     * @brief Retrives error information about the last operation performed
     * 
     * @param string $sql the executed query
     * @return error information
     */
    protected function errorInfo($sql) {
        $error = $this->_db->errorInfo();
        return sprintf('<b>Db error</b>: %s, <b>query</b>: %s', $error[2], $sql);
    }

    /**
     * @brief Executes a query without preparation
     * 
     * @param string $sql the query to execute
     * @return the query result
     */
    public function query($sql) {
        return $this->_db->query($sql);
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
    public function select($fields, $tables, $where = null, $order = null, $limit = null) {

        $selection = $this->selection($fields);
        $from = $this->from_table($tables);
        $where = $this->where($where);
        $order = $this->order_by($order);
        $limit = $this->limit($limit);

        if(is_array($where)) {
            $where_sql = $where['sql'];
            $where_data = $where['data'];
        }
        else {
            $where_sql = $where;
            $where_data = null;
        }

        $query = "SELECT ".$selection.' '.$from.($where_sql ? ' '.$where_sql : '').($order ? ' '.$order : '').($limit ? ' '.$limit: '');

        try {
            $sth = $this->_db->prepare($query);
        }
        catch(PDOException $e) {
            exit(Error::syserrorMessage(get_class($this), 'select', $this->errorInfo($query), __LINE__));
        }
        $sth->execute($where_data);

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @brief Sets the limit statement of a query.
     * 
     * @param mixed $limit the limit clause (can be an array or a string)
     * @return the limit statement
     */
    public function limit($limit) {
        if(is_array($limit) and count($limit) == 2) {
            return sprintf('LIMIT %s', implode(',', $limit));
        }
        elseif(is_string($limit) and $limit) {
            return sprintf('LIMIT %s', $limit);
        }
        else {
            return '';
        }
    }

    /**
     * @brief Sets the order by statement of a query.
     * 
     * @param string $order the order clause
     * @return the order statement
     */
    public function order_by($order) {
        if($order) {
            return sprintf('ORDER BY %s', $order);
        }
        else {
            return '';
        }
    }

    /**
     * @brief Sets the from statement of a query.
     * 
     * @param mixed $tables the db tables (can be an array or a string)
     * @return the from statement
     */
    public function from_table($tables) {
        if(is_array($tables) and count($tables)) {
            return sprintf('FROM %s', implode(',', $tables));
        }
        elseif(is_string($tables) and $tables) {
            return sprintf('FROM %s', $tables);
        }
        else {
            return '';
        }
    }

    /**
     * @brief Sets the selection statement of a query.
     * 
     * @param mixed $fields the fields to be selected (can be an array or string)
     * @return the selection statement
     */
    public function selection($fields) {
        if(is_array($fields) and count($fields)) {
            return implode(',', $fields);
        }
        elseif(is_string($fields) and $fields) {
            return $fields;
        }
        else {
            return '';
        }
    }

    /**
     * @brief Sets the where statement of a query.
     * 
     * @param mixed $where the where clause (can be an array or a string)
     * @return the where statement
     */
    public function where($where) {
        if(is_array($where) and count($where)) {
            $res = array();
            $data = array();
            foreach($where as $k => $f) {
                $res[] = sprintf("%s=:%s", $k, $k);
                $data[':'.$k] = $f;
            }
            return array('sql' => sprintf('WHERE %s', implode(' AND ', $res)), 'data' => $data);
        }
        elseif(is_string($where) and $where) {
            return sprintf('WHERE %s', $where);
        }
        else {
            return '';
        }
    }

    /**
     * @brief Returns the name of the fields of the given table
     * 
     * @param string $table the database table
     * @return array containing the name of the fields
     */
    public function getFieldsName($table) {

        $fields = array();
        $query = sprintf("SHOW COLUMNS FROM %s", $table);
        try {
            $sth = $this->_db->prepare($query);
        }
        catch(PDOException $e) {
            exit(Error::syserrorMessage(get_class($this), 'getFieldsName', $this->errorInfo($query), __LINE__));
        }

        $sth->execute();
        $results = $sth->fetchAll(PDO::FETCH_ASSOC);

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

        exit(error::syserrorMessage(get_class($this), 'getTableStructure', 'The DBMS class must implement such method!', __LINE__));

    }

    /**
     * @brief Returns the number of records in the given table selected by the given where clause 
     * 
     * @param string $table the database table
     * @param mixed $where the where clause (array or string)
     * @param string $field the field used for counting
     * @return number of records
     */
    public function count($table, $where=null, $field='id') {

        $res = $this->select(sprintf("COUNT(%s) AS tot", $field), $table, $where);

        return ($res and $res[0]['tot']) ? (int) $res[0]['tot']: 0;

    }

    /**
     * @brief Inserts the given data in the given table
     * 
     * @param string $table the database table
     * @param array $data the data to insert in the form array('field_name'=>'value')
     * @return the last inserted id on success, false on failure
     */
    public function insert($table, $data, $options = array()) {

        $fields = array();
        $values = array();
        $_data = array();

        foreach($data as $f=>$v) {
            $fields[] = $f;
            $values[] = ':'.$f;
            $_data[':'.$f] = $v;
        }

        $query = "INSERT INTO ".$table." (`".implode("`,`", $fields)."`) VALUES (".implode(",", $values).")"; 
        try {
            $sth = $this->_db->prepare($query);
        }
        catch(PDOException $e) {
            exit(Error::syserrorMessage(get_class($this), 'insert', $this->errorInfo($query), __LINE__));
        }
        $result = $sth->execute($_data);

        return $result ? $this->lastInsertedId() : false;
    }

    /**
     * @brief Updates the given table with the given data using the given where clause 
     * 
     * @param string $table 
     * @param array $data the data to update in the form array('field_name'=>'value')
     * @param mixed $where the where clause (array or string)
     * @return boolean value: true on success, false on failure
     */
    public function update($table, $data, $where) {

        if(!$data) return true;

        $where = $this->where($where);

        if(is_array($where)) {
            $where_sql = $where['sql'];
            $where_data = $where['data'];
        }
        else {
            $where_sql = $where;
            $where_data = array();
        }

        $_data = array();
        $sets = array();

        foreach($data as $f=>$v) {
            $sets[] = '`'.$f.'`=:'.$f;
            $_data[':'.$f] = $v;
        }
        $query = "UPDATE ".$table." SET ".implode(",", $sets)." ".($where_sql ? ' '.$where_sql : '');

        try {
            $sth = $this->_db->prepare($query);
        }
        catch(PDOException $e) {
            exit(Error::syserrorMessage(get_class($this), 'update', $this->errorInfo($query), __LINE__));
        }
        $result = $sth->execute(array_merge($_data, $where_data));

        return $result;
    }

    /**
     * @brief Deletes records from the given table using the given where clause. 
     * 
     * @param string $table the database table
     * @param mixed $where the where clause (array or string)
     * @return boolean value: true on success, false on failure
     */
    public function delete($table, $where) {

        $query = "DELETE FROM $table ".($where ? "WHERE $where":"");

        $where = $this->where($where);

        if(is_array($where)) {
            $where_sql = $where['sql'];
            $where_data = $where['data'];
        }
        else {
            $where_sql = $where;
            $where_data = array();
        }

        try {
            $sth = $this->_db->prepare($query);
        }
        catch(PDOException $e) {
            exit(Error::syserrorMessage(get_class($this), 'delete', $this->errorInfo($query), __LINE__));
        }
        $result = $sth->execute($where_data);

        return $result;

    }

    /**
     * @brief Returns the last inserted id 
     * 
     * @return the last inserted id or false
     */
    private function lastInsertedId() {

        return $this->_db->lastInsertId();

    }

    /*
     * @brief Turns off autocommit. Initiates a transaction.
     * @return true on success, false on failure
     */
    public function begin() {
        return $this->_db->beginTransaction();
    }

    /*
     * @brief Commits a transaction.
     * @return true on success, false on failure
     */
    public function commit() {
        return $this->_db->commit();
    }

    /*
     * @brief Rolls back a transaction.
     * @return true on success, false on failure
     */
    public function rollBack() {
        return $this->_db->rollback();
    }

}
