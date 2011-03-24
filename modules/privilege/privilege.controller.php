<?php

class privilegeController extends controller {

	function __construct($registry) {

		parent::__construct($registry);

		$this->_cpath = dirname(__FILE__);
		$this->_mdl_name = "privilege";

		// privileges
		$this->_class_privilege = $this->_mdl_name;
		$this->_view_privilege = 1;
	}

	public function manage() {
	
		access::check($this->_registry, $this->_class_privilege, $this->_view_privilege, array("exitOnFailure"=>true));

		$at = new adminTable($this->_registry, TBL_SYS_PRIVILEGES, array('insertion'=>false, 'edit_deny'=>'all'));

		$text = __("ManagePrivilegesExp");

		$table = $at->manage();

		$this->_view->setTpl('manage_table');
		$this->_view->assign('text', $text);
		$this->_view->assign('title', __("TableContent")." ".TBL_SYS_PRIVILEGES);
		$this->_view->assign('table', $table);

		return $this->_view->render();
	}

}

?>
