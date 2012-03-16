<?php
/**
 * @file datetimeSettings.php
 * @brief Contains the model of the datetimeSettings module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.98
 * @date 2011-2012
 * @copyright Otto srl MIT License \see http://www.opensource.org/licenses/mit-license.php
 */

/**
 * @ingroup datetimesettings_module
 * @brief Datetime Settings model class
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.98
 * @date 2011-2012
 * @copyright Otto srl MIT License \see http://www.opensource.org/licenses/mit-license.php 
 */
class datetimeSettings extends model {

	/**
	 * @brief Constructs a datetimeSettings instance
	 * 
	 * @return datetimeSettings instance
	 */
	function __construct() {
	
		$id = 1;
		$this->_registry = registry::instance();
		$this->_tbl_data = TBL_SYS_DATETIME_SETTINGS;

		parent::__construct($id);

		date_default_timezone_set($this->timezone);

	}

}

?>
