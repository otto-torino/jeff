<?php

class siteSettingsController extends controller {

	private $_admin_privilege;

	function __construct() {

		parent::__construct();

		$this->_cpath = dirname(__FILE__);
		$this->_mdl_name = "siteSettings";

		// privileges
		$this->_class_privilege = $this->_mdl_name;
		$this->_admin_privilege = 1;

	}

	public function manage() {
	
		access::check($this->_class_privilege, $this->_admin_privilege, array("exitOnFailure"=>true));

		$at = new adminTable(TBL_SYS_SETTINGS, array('insertion'=>false, 'deletion'=>false));

		$table = $at->manage();

		$this->_view->setTpl('manage_table');
		$this->_view->assign('title', __("ManageTable")." ".TBL_SYS_SETTINGS);
		$this->_view->assign('table', $table);

		return $this->_view->render();
	}

}

?>
