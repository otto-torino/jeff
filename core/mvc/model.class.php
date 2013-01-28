<?php
/**
 * @file model.class.php
 * @brief Contains the model primitive class.
 *
 * Defines the mvc model class
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup mvc MVC pattern
 * Set of primitive classes which implements the MVC pattern.
 *
 * Jeff is designed following the MVC pattern directive. \n
 * All user requests are managed by @ref controller classes which may or not control \ref model objects and return specific model @ref view.
 *
 */

/**
 * @ingroup mvc core
 * @brief Model class of the MVC pattern, is the class used to represent a database record object. 
 *
 * This is the general model class extended by all specific module models. It acts like an interface to the database table which stores the module data and so has:
 * - the getter and setter methods to set and retrieve properties (field values)
 * - the save and delete action methods to perform database actions (insert, update and delete statements) 
 * - the init methods to instantiate the object and set all its properties reading the field values from db.
 *
 * A Model instance represents a record of a database table.<br />
 * Each model stores all the record field values as elements of a property array. Each model property value can be retrieved calling it as a normal property.\n
 * i.e. $model->field is the same as  $model->_p['field'] and contains the value of 'field' for the represented record. 
 * 
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php) 
 */
class model {

	/**
	 * @brief model properties 
	 * 
	 * an associative array containing the record fields name and their value
	 */
	protected $_p = array();

 	/**
 	 * @brief changed model properties 
 	 * 
 	 * an array an array containing the list of changed model properties
 	 */
 	protected $_chgP = array();


	/**
	 * @brief the database table of the model
	 */
	protected $_tbl_data = '';

	/**
	 * @brief the registry singleton instance 
	 */
	protected $_registry;

	/**
	 * @brief the name of the primary key field 
	 */
	protected $_id_name = 'id';

	/**
	 * @brief Constructs a model instance 
	 * 
	 * @param mixed $id the identifier of the record (the primary key value) 
	 * @param mixed $table the model database table. Default null (may be set after object construction)
	 * @return model instance
	 */
	function __construct($id, $table=null) {
		
		$this->_registry = registry::instance();

		if(!is_null($table)) $this->_tbl_data = $table;

		$this->_p = $this->initDbProp($id);

	}

	/**
	 * @brief Setter method for $_id_name property 
	 * 
	 * @param mixed $id_name the name of the primary key 
	 * @return void
	 */
	public function setIdName($id_name) {

		$this->_id_name = $id_name;

	}

	/**
	 * @brief Setter method for $_tbl_data property 
	 * 
	 * @param string $table the model database table
	 * @return void
	 */
	public function setTable($table) {
		
		$this->_tbl_data = $table;

	}

	/**
	 * @brief Model initialization 
	 * 
	 * Initializes the model object retrieving data from the database. If the record still doesn't exist, returns an associative array with all null values. 
	 * 
	 * @param mixed $id the identifier of the record (the primary key value)
	 * @return 
	 *   an associative array with all model properties in the form array("property_name" => "value")
	 */
	protected function initDbProp($id) {

		$qr = $this->_registry->db->autoSelect(array("*"), array($this->_tbl_data), "id='$id'", null);
		if(count($qr)) {
		    $structure = $this->_registry->db->getTableStructure($this->_tbl_data);
		    $res = array();
		    foreach($qr[0] as $fname=>$fvalue) {
		    	if($structure['fields'][$fname]['type']=='int') setType($fvalue, 'int');
			elseif($structure['fields'][$fname]['type']=='float' || $structure['fields'][$fname]['type']=='double' || $structure['fields'][$fname]['type']=='decimal') setType($fvalue, 'float');
			else setType($fvalue, 'string');
			$res[$fname] = $fvalue;
		    }
		    return $res;
		}
		else return $this->initNullProp();

	}

	/**
	 * @brief Initialization of an empty model object 
	 * 
	 * @return
	 *   an associative array with all model properties set to null in the form array("property_name" => null)
	 */
	protected function initNullProp() {

		$res = array();
		$fields = $this->_registry->db->getFieldsName($this->_tbl_data);
		foreach($fields as $f) $res[$f] = null;

		return $res;

	}

	/**
	 * @brief ToString method
	 *
	 * Method called when doing a casting to string of the object, by default it returns the object id. 
	 * 
	 * @return the object id
	 */
	public function __toString() {

		return (string) $this->id;

	}

	/**
	 * @brief Getter method 
	 * 
	 * Returns the value of the given property. Calls the specific property getter method if exists, otherwise returns directly its value. 
	 * 
	 * @param string $pName the property name
	 * @return
	 *   null if the requested property is not a model property, the specific getter method for the property if exists or the property value
	 */
	public function __get($pName) {

		if(!array_key_exists($pName, $this->_p)) return null;
		if(method_exists($this, 'get'.$pName)) return $this->{'get'.$pName}();
		else return $this->_p[$pName];
	}
	
	/**
	 * @brief Setter method 
	 * 
	 * Sets the value of the given property. Calls a specific property setter method if exists.
	 * 
	 * @param string $pName the property name 
	 * @param mixed $value the property value
	 * @return mixed
	 *   false if the propertiy is not a model property, the specific setter method for the property if exists or true.
	 */
	public function __set($pName, $value) {

		if(!array_key_exists($pName, $this->_p)) return false;
		if(method_exists($this, 'set'.$pName)) return $this->{'set'.$pName}($value);
		else {
			if($this->_p[$pName]!=$value && !in_array($pName, $this->_chgP)) $this->_chgP[] = $pName;
			$this->_p[$pName] = $value;
			return true;
		}
	}

	/**
	 * @brief Saves the model object
	 *
	 * Stores the object properties in the database. 
	 * 
	 * @param bool $insert force new record insertion
	 * @return
	 *   the operation result, true on success, false on failure
	 */
	public function saveData($insert=false) {
	
		$data = array();
		foreach($this->_chgP as $f) $data[$f] = $this->_p[$f];

		if(!$this->_p[$this->_id_name] || $insert) {
			$res = $this->_registry->db->insert($this->_tbl_data, $data);
			if($res) $this->_p[$this->_id_name] = $res;
		}
		else 
			$res = $this->_registry->db->update($this->_tbl_data, $data, $this->_id_name."='".$this->_p[$this->_id_name]."'");

		return $res;
	}

	/**
	 * @brief Deletes the object properties from database 
	 * 
	 * @return
	 *   the operation result, true on success, false on failure
	 */
	public function deleteData() {
	
		return $this->_registry->db->delete($this->_tbl_data, $this->_id_name."=".$this->_p[$this->_id_name]);
	}

}

?>
