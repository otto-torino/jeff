<?php

class userController extends controller {

	function __construct($registry) {

		parent::__construct($registry);

		$this->_cpath = dirname(__FILE__);
		$this->_mdl_name = "user";

		// privileges
		$this->_class_privilege = $this->_mdl_name;
		$this->_admin_privilege = 1;
	}

	public function manage() {
	
		access::check($this->_registry, $this->_class_privilege, $this->_admin_privilege, array("exitOnFailure"=>true));

		/*$f_keys = array(
			"main_group"=>array(
				"table"=>TBL_SYS_GROUPS,
				"field"=>"label",
				"where"=>null,
				"order"=>"id"
			)
		);*/

		$s_fields = array(
			"password"=>array(
				"type"=>"password",
				"edit_lable"=>__("userEditPwdLabel")
			),
			"groups"=>array(
				"type"=>"multicheck",
				"required"=>true,
				"value_type"=>'int',
				"table"=>TBL_SYS_GROUPS,
				"field"=>"label",
				"where"=>null,
				"order"=>"id"
			)
		);

		$at = new adminTable($this->_registry, TBL_USERS, array("edit_deny"=>array(1)));
		//$at->setForeignKeys($f_keys);
		$at->setSpecialFields($s_fields);

		$table = $at->manage();

		$this->_view->setTpl('manage_table');
		$this->_view->assign('title', __("ManageTable")." ".TBL_USERS);
		$this->_view->assign('table', $table);

		return $this->_view->render();
	}

}

?>
