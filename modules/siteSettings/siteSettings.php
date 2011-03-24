<?php

class siteSettings extends model {


	function __construct($registry) {
	
		$id = 1;
		$this->_registry = $registry;
		$this->_tbl_data = TBL_SYS_SETTINGS;
		parent::__construct($this->initP($id));

	}

	private function initP($id) {

		return $this->initDbProp($id);

	}
	

}

?>
