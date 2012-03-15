<?php

class datetimeSettings extends model {

	function __construct() {
	
		$id = 1;
		$this->_registry = registry::instance();
		$this->_tbl_data = TBL_SYS_DATETIME_SETTINGS;

		parent::__construct($id);

		date_default_timezone_set($this->timezone);

	}

}

?>
