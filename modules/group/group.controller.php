<?php

class groupController extends controller {

	function __construct($registry) {

		parent::__construct($registry);

		$this->_cpath = dirname(__FILE__);
		$this->_mdl_name = "group";

		// privileges
		$this->_class_privilege = $this->_mdl_name;
		$this->_admin_privilege = 1;
	}

	public function manage() {
	
		access::check($this->_registry, $this->_class_privilege, $this->_admin_privilege, array("exitOnFailure"=>true));

		$id = cleanInput('get', 'id', 'int');

		if($id || cleanInput('get', 'action', 'string')=='new') { 
			$g = new group($this->_registry, $id);
			return $this->manageGroup($g);
		}
		
		$text = __("ManageGroupsExp");

		$heads = array(__("label"), __("description"), __("privileges"));
		$rows = array();
		foreach(group::get($this->_registry) as $gid) {
			$g = new group($this->_registry, $gid);
			$gpriv = array();
			foreach(explode(",", $g->privileges) as $pid) {
				$p = new privilege($this->_registry, $pid);
				$gpriv[] = $p->label;
			}
			$label = $g->id == 1 
				? htmlVar($g->label)
				: anchor($this->_router->linkHref($this->_mdl_name, 'manage', array("id"=>$gid)), htmlVar($g->label));

			$rows[] = array($label, htmlVar($g->description), implode(", ", $gpriv));	
		}

		$this->_view->setTpl('table');
		$this->_view->assign('caption', __("GroupsInSystem"));
		$this->_view->assign('class', 'wide generic');
		$this->_view->assign('heads', $heads);
		$this->_view->assign('rows', $rows);

		$table = $this->_view->render();

		$link_insert = anchor($this->_router->linkHref($this->_mdl_name, 'manage', array("action"=>"new")), __("insertNewRecord"));

		$this->_view->setTpl('group_manage_list');
		$this->_view->assign('title', __("ManageGroups"));
		$this->_view->assign('text', $text);
		$this->_view->assign('link_insert', $link_insert);
		$this->_view->assign('table', $table);

		return $this->_view->render();
	}

	private function manageGroup($g) {
		
		$form = $this->formGroup($g);

		$title = $g->id ? __("Edit")." \"".htmlVar($g->label)."\"" : __("NewGroup");
		$this->_view->setTpl('group_manage');
		$this->_view->assign('title', $title);
		$this->_view->assign('form', $form);

		return $this->_view->render();

	}

	private function formGroup($g) {

		$myform = new form($this->_registry, 'post', 'group_form', array("validation"=>true));
		$myform->load('group_form');

		$required = '';
		$buffer = $myform->sform($this->_router->linkHref($this->_mdl_name, 'saveGroup'), $required);
		$buffer .= $myform->hidden('id', $g->id);

		if($g->id > 5 || !$g->id) $buffer .= $this->formGroupData($g, $myform);
		else $buffer .= "<p>".htmlVar($g->description)."</p>";

		$buffer .= $this->formPrivileges($g, $myform);

		$buffer .= $myform->input('submit_edit', 'submit', __("edit"), array("class"=>"left"));
		if($g->id>5) {
			$onclick = "onclick=\"if(confirm('Sicuro di voler procedere con l\'eliminazione?')) location.href='".$this->_router->linkHref($this->_mdl_name, 'deleteGroup', array("id"=>$g->id))."'\"";
			$buffer .= " ".$myform->input('submit_delete', 'button', __("delete"), array("class"=>"right", "js"=>$onclick));
		}
		$buffer .= clearFloat();

		$buffer .= $myform->cform();

		return $buffer;

	}

	private function formGroupData($g, $myform) {

		$content = $myform->cinput('label', 'text', $myform->retvar('label', htmlInput($g->label)), __("label"), array("required"=>true, "size"=>40, "maxlength"=>200));
		$content .= $myform->ctextarea('description', $myform->retvar('description', htmlInput($g->description)), __("description"), array("required"=>false, "cols"=>60, "rows"=>4));

		return $myform->fieldset(__("GeneralData"), $content);

	}

	private function formPrivileges($g, $myform) {
	
		$buffer = '';

		$privileges_ids = privilege::get($this->_registry);

		$old_ctg = null;
		$odd = true;
		$form_left = ""; 
		$form_right = ""; 
		$checked = explode(",", $g->privileges);

		$i = 0;
		$odd = false;
		$tot = count($privileges_ids);
		foreach($privileges_ids as $pid) {
			$i++;
			$p = new privilege($this->_registry, $pid);
			if($old_ctg != $p->category) {
				if(!is_null($old_ctg)) {
					$field = $myform->cmulticheckbox("pids[]", $checked, $mcelements, $label, array("label_class"=>"block"));
					if($odd) $form_left .= $field;
					else $form_right .= $field;
				}
				$mcelements = array();
				$old_ctg = $p->category;
				$label = htmlVar($p->category);
				$odd = !$odd;
			}
			$mcelements[] = array("label"=>htmlVar($p->label)." ".tooltip("(?)", htmlVar($p->label), htmlVar($p->description), array("class"=>"help")), "value"=>$p->id);
			if($i==$tot) {
				$field = $myform->cmulticheckbox("pids[]", $checked, $mcelements, $label, array("label_class"=>"block"));
				if($odd) $form_left .= $field;
				else $form_right .= $field;
			}

		}

		$view = new view($this->_registry);
		$view->setTpl('group_form_privilege');
		$view->assign('form_left', $form_left);
		$view->assign('form_right', $form_right);

		$content = $view->render();

		return $myform->fieldset(__("ManagePrivileges"), $content);
			
	}

	public function saveGroup() {
	
		access::check($this->_registry, $this->_class_privilege, $this->_admin_privilege, array("exitOnFailure"=>true));

		$id = cleanInput('post', 'id', 'int');
		$g = new group($this->_registry, $id);

		if($g->id == 1) header("Location: ".$this->_router->linkHref($this->_mdl_name, 'manage'));

		if($g->id > 5 || !$g->id) {
			$g->label = cleanInput('post', 'label', 'string');
			$g->description = cleanInput('post', 'description', 'string');
		}
		$checked_ids = cleanInputArray('post', 'pids', 'int');
		$g->privileges = $checked_ids ? implode(",", $checked_ids) : '';

		$g->saveData();

		header("Location: ".$this->_router->linkHref($this->_mdl_name, 'manage'));

	}

	public function deleteGroup() {
	
		access::check($this->_registry, $this->_class_privilege, $this->_admin_privilege, array("exitOnFailure"=>true));

		$id = cleanInput('get', 'id', 'int');
		$g = new group($this->_registry, $id);

		if($g->id < 6) header("Location: ".$this->_router->linkHref($this->_mdl_name, 'manage'));

		$g->deleteData();

		header("Location: ".$this->_router->linkHref($this->_mdl_name, 'manage'));

	}
}

?>
