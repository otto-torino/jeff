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
class Mysql extends Database  {

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
        try {
            $sth = $this->_db->prepare($query);
        }
        catch(PDOException $e) {
            exit(Error::syserrorMessage(get_class($this), 'getFieldsName', $this->errorInfo($query), __LINE__));
        }
        $sth->execute();
        $res = $sth->fetchAll();
        
        foreach($res as $row) {
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









}


?>
