<?php
/**
 * @file datetimeSettings.php
 * @brief Contains the model of the datetimeSettings module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup datetimesettings_module
 * @brief Datetime Settings model class
 *
 * <p>Model fields:</p>
 * - **id** int(1): primary key
 * - **timezone** varchar(64): timezone identifier, i.e 'Europe/Rome'
 * - **date_format** varchar(64): date format, i.e. '%Y-%m-%d'
 * - **time_format** varchar(64): time format, i.e. '%H:%i:%s'
 * - **datetime_format** varchar(64): datetime format, i.e. '%d-%m-%Y at %H:%i'
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class datetimeSettings extends model {

	/**
	 * @brief Constructs a datetimeSettings instance
	 * 
	 * @return datetimeSettings instance
	 */
	function __construct() {
	
		$id = 1;
		$this->_tbl_data = TBL_SYS_DATETIME_SETTINGS;

		parent::__construct($id);

		date_default_timezone_set($this->timezone);

	}

}

?>
