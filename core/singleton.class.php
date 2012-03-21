<?php
/**
 * @file singleton.class.php
 * @brief Contains the singleton class.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup core
 * @brief Singleton primitive class 
 * 
 * Class which implemets the singleton pattern and assures the existence of only one object instance
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
abstract class singleton {
	
	/*
	 * @brief array containing the singleton instances
	 */
	protected static $_instances = array();
	
	/**
	 * @brief Singleton constructor
	 *
	 * This method is protected in order to deny direct object instantiation outside this class and the ones
	 * which extends it 
	 * 
	 * @return void
	 */
	protected function __construct() {

	}

	/**
	 * @brief Method used to retrieve singleton instances 
	 *
	 * if the singleton instance already exists returns it, otherwise creates an instance and returns it
	 * 
	 * @return mixed the singleton instance
	 */
	public static function instance() {
	
		$class = get_called_class();
		if(array_key_exists($class, self::$_instances) === false) {
			self::$_instances[$class] = new static();
		}
		
		return self::$_instances[$class];

	}

	/**
	 * @brief Clone method 
	 * 
	 * Singleton cloning is denied
	 *
	 * @return error
	 */
	public function __clone() {
		Error::syserrorMessage('singleton', '__clone', __("CannotCloneSingleton"), __LINE__);
	}
	
	/**
	 * @brief Sleep method 
	 * 
	 * Singleton serialization is denied
	 *
	 * @return error
	 */
	public function __sleep() {
		Error::syserrorMessage('singleton', '__sleep', __("CannotSerializeSingleton").get_called_class(), __LINE__);
	}
	
	/**
	 * @brief Wakeup method 
	 * 
	 * Singleton serialization is denied
	 *
	 * @return error
	 */
	public function __wakeup() {
		Error::syserrorMessage('singleton', '__wakeup', __("CannotSerializeSingleton"), __LINE__);
	}

}

?>
