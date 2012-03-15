<?php

class languageController extends controller {

	function __construct() {

		parent::__construct();

		$this->_cpath = dirname(__FILE__);
		$this->_mdl_name = "language";

		// privileges
		$this->_class_privilege = $this->_mdl_name;
		$this->_admin_privilege = 1;
	}

	public function manage() {
	
		access::check($this->_class_privilege, $this->_admin_privilege, array("exitOnFailure"=>true));

		$s_fields = array(
			"main"=>array(
				"type"=>"bool",
				"required"=>true,
				"true_label"=>__("yes"),
				"false_label"=>__("no")
			),
			"active"=>array(
				"type"=>"bool",
				"required"=>true,
				"true_label"=>__("yes"),
				"false_label"=>__("no")
			)
		);

		$at = new adminTable(TBL_LNG);
		$at->setSpecialFields($s_fields);
		$table = $at->manage();

		$this->_view->setTpl('manage_table');
		$this->_view->assign('title', __("ManageTable")." ".TBL_LNG);
		$this->_view->assign('table', $table);

		return $this->_view->render();
	}

	public function choose() {
	
		$active_lngs = language::get($this->_registry, array('where'=>"active='1'"));
		
		$lngs = array();

		foreach($active_lngs as $l) {
			$href = "?lng=".$l->code;
			$link = anchor($href, htmlVar($l->language));
			$lngs[] = $link;
		}

		return implode(" | ", $lngs);
	}

}

?>
