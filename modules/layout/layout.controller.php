<?php

require_once('layout.php');

class layoutController extends controller {

	function __construct($registry) {

		parent::__construct($registry);

		$this->_cpath = dirname(__FILE__);
		$this->_mdl_name = "layout";

		// privileges
		$this->_class_privilege = $this->_mdl_name;
		$this->_admin_privilege = 1;
	}

	public function manage() {
	
		access::check($this->_registry, $this->_class_privilege, $this->_admin_privilege, array("exitOnFailure"=>true));

		$items = array();
		foreach(layout::getThemes($this->_registry) as $theme) {
			require_once(ABS_THEMES.DS.$theme->name.DS.$theme->name.'.php');
			$themeClassName = $theme->name."Theme";
			$themeObj = new $themeClassName($this->_registry);
			$items[] = array(
				'link_activate'=>$this->_router->linkHref($this->_mdl_name, 'activateTheme', array("id"=>$theme->id)), 
				'image'=>htmlVar($themeObj->getImage()), 
				'name'=>htmlVar($themeObj->getName()), 
				'description'=>htmlVar($themeObj->getDescription()), 
				'active'=>$theme->active ? true : false
			);
		}

		$this->_view->setTpl('layout_list');
		$this->_view->assign('title', __("ManageLayout"));
		$this->_view->assign('text', __("ManageLayoutExp"));
		$this->_view->assign('items', $items);

		return $this->_view->render();

	}

	public function activateTheme() {

		access::check($this->_registry, $this->_class_privilege, $this->_admin_privilege, array("exitOnFailure"=>true));

		$id = cleanInput('get', 'id', 'int');
		layout::activateTheme($this->_registry, $id);

		header("Location: ".$this->_router->linkHref($this->_mdl_name, 'manage'));

	}

}

