<?php

class datetimeSettingsController extends controller {

	function __construct($registry) {

		parent::__construct($registry);

		$this->_cpath = dirname(__FILE__);
		$this->_mdl_name = "datetimeSettings";

		// privileges
		$this->_class_privilege = $this->_mdl_name;
		$this->_admin_privilege = 1;

	}

	public function manage() {

		access::check($this->_registry, $this->_class_privilege, $this->_admin_privilege, array("exitOnFailure"=>true));

		$at = new adminTable($this->_registry, TBL_SYS_DATETIME_SETTINGS, array('insertion'=>false));

		$table = $at->manage();

		$this->_view->setTpl('manage_table');
		$this->_view->assign('title', __("ManageTable")." ".TBL_SYS_DATETIME_SETTINGS);
		$this->_view->assign('table', $table);

		return $this->_view->render();
	}

}

?>
