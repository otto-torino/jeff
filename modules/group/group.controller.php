<?php
/**
 * @file group.controller.php
 * @brief Contains the controller of the group module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup group_module User groups
 * @ingroup modules security
 *
 * Modules for the management of user groups (the association of privileges and users is done through groups)
 */

/**
 * @ingroup group_module
 * @brief Group module controller
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class groupController extends controller {

	/**
	 * module's administration privilege class 
	 */
	private $_class_privilege;

	/**
	 * module's administration privilege id 
	 */
	private $_admin_privilege;

	/**
	 * @brief Constructs a group controller instance 
	 * 
	 * @return group controller instance
	 */
	function __construct() {

		parent::__construct();

		$this->_cpath = dirname(__FILE__);
		$this->_mdl_name = "group";

		// privileges
		$this->_class_privilege = $this->_mdl_name;
		$this->_admin_privilege = 1;
	}

	/**
	 * @brief User groups backoffice 
	 * 
	 * @access public
	 * @return the user groups table back office
	 */
	public function manage() {
	
		access::check($this->_class_privilege, $this->_admin_privilege, array("exitOnFailure"=>true));

		$id = cleanInput('get', 'id', 'int');

		if($id || cleanInput('get', 'action', 'string')=='new') { 
			$g = new group($id);
			return $this->manageGroup($g);
		}
		
		$text = __("ManageGroupsExp");

		$heads = array(__("label"), __("description"), __("privileges"));
		$rows = array();
		foreach(group::get($this->_registry) as $gid) {
			$g = new group($gid);
			$gpriv = array();
			foreach(explode(",", $g->privileges) as $pid) {
				$p = new privilege($pid);
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

		$link_insert = anchor($this->_router->linkHref($this->_mdl_name, 'manage', array("action"=>"new")), __("insertNewRecord"), array('class'=>'submit'));

		$this->_view->setTpl('group_manage_list');
		$this->_view->assign('title', __("ManageGroups"));
		$this->_view->assign('text', $text);
		$this->_view->assign('link_insert', $link_insert);
		$this->_view->assign('table', $table);

		return $this->_view->render();
	}

	/**
	 * @brief Group insertion or modification management 
	 * 
	 * @param group $g the group model instance 
	 * @return group insertion or modification view
	 */
	private function manageGroup($g) {
		
		$form = $this->formGroup($g);

		$title = $g->id ? __("Edit")." \"".htmlVar($g->label)."\"" : __("NewGroup");
		$this->_view->setTpl('group_manage');
		$this->_view->assign('title', $title);
		$this->_view->assign('form', $form);

		return $this->_view->render();

	}

	/**
	 * @brief Group insertion, deletion and modification format 
	 * 
	 * @param group $g the group model instance 
	 * @return group insertion or modification format
	 */
	private function formGroup($g) {

		$myform = new form('post', 'group_form', array("validation"=>true));
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

	/**
	 * @brief Descriptive part of the insertion/modification group format
	 * 
	 * @param group $g group model instance 
	 * @param form $myform form object instance 
	 * @return descriptive form elements
	 */
	private function formGroupData($g, $myform) {

		$content = $myform->cinput('label', 'text', $myform->retvar('label', htmlInput($g->label)), __("label"), array("required"=>true, "size"=>40, "maxlength"=>200));
		$content .= $myform->ctextarea('description', $myform->retvar('description', htmlInput($g->description)), __("description"), array("required"=>false, "cols"=>60, "rows"=>4));

		return $myform->fieldset(__("GeneralData"), $content);

	}

	/**
	 * @brief Privileges selection part of the insertion/modification group format
	 * 
	 * @param group $g group model instance 
	 * @param form $myform form object instance 
	 * @return privileges selection form elements
	 */
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
			$p = new privilege($pid);
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

		$view = new view();
		$view->setTpl('group_form_privilege');
		$view->assign('form_left', $form_left);
		$view->assign('form_right', $form_right);

		$content = $view->render();

		return $myform->fieldset(__("ManagePrivileges"), $content);
			
	}

	/**
	 * @brief Save group model after form submission 
	 * 
	 * @return void
	 */
	public function saveGroup() {
	
		access::check($this->_class_privilege, $this->_admin_privilege, array("exitOnFailure"=>true));

		$id = cleanInput('post', 'id', 'int');
		$g = new group($id);

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

	/**
	 * @brief Deletes submitted groups
	 * 
	 * @return void
	 */
	public function deleteGroup() {
	
		access::check($this->_class_privilege, $this->_admin_privilege, array("exitOnFailure"=>true));

		$id = cleanInput('get', 'id', 'int');
		$g = new group($id);

		if($g->id < 6) header("Location: ".$this->_router->linkHref($this->_mdl_name, 'manage'));

		$g->deleteData();

		header("Location: ".$this->_router->linkHref($this->_mdl_name, 'manage'));

	}
}

?>
