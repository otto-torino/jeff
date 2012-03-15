<?php

class siteSettings extends model {

	function __construct() {
	
		$id = 1;
		$this->_tbl_data = TBL_SYS_SETTINGS;

		parent::__construct($id);

	}

}

?>
