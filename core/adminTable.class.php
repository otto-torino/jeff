<?php

class adminTable {

	protected $_registry, $_table;
	protected $_primary_key, $_fields, $_fkeys, $_sfields, $_pfields, $_html_fields;
	protected $_efp;
	protected $_cls_cbk_edit, $_mth_cbk_edit;
	protected $_cls_cbk_del, $_mth_cbk_del;

	protected $_insertion, $_edit_deny;
	protected $_changelist_fields;
	protected $_editor;
	protected $_custom_tpl;

	protected $_view;
	protected $_arrow_down_path, $_arrow_up_path;

	function __construct($registry, $table, $opts=null) {

		$this->_registry = $registry;
		$this->_table = $table;
		$this->_view = new view($registry);

		// allow record insertion
		$this->_insertion = gOpt($opts, 'insertion', true);
		// allow record deletion
		$this->_deletion = gOpt($opts, 'deletion', true);
		// denty all/some pkeys modifications
		$this->_edit_deny = gOpt($opts, 'edit_deny', array());
		// fields to show in changelist view (all if null)
		$this->_changelist_fields = gOpt($opts, 'changelist_fields', null);
		// dojo editor for html fields
		$this->_editor = gOpt($opts, 'editor', false);
		// export items
		$this->_export = gOpt($opts, 'export', false);
		// set custom views (i.e. array('view'=>'myview', 'edit'=>'otherview'))
		// views that may be set: view, insert, edit
		$this->_custom_tpl = gOpt($opts, 'custom_tpl', array());

		$this->_efp = gOpt($opts, "efp", 20);

		$structure = $this->_registry->db->getTableStructure($this->_table);
		$this->_primary_key = $structure['primary_key'];
		$this->_fields = $structure['fields'];
		$this->_fkeys = array();
		$this->_sfields = array();
		$this->_pfields = array();
		$this->_html_fields = array();

		$this->_arrow_down_path = ROOT."/img/down_arrow-black.png";
		$this->_arrow_up_path = ROOT."/img/up_arrow-black.png";

		$this->_cls_cbk_edit = gOpt($opts, "cls_callback_edit", null);
	        $this->_mth_cbk_edit = gOpt($opts, "mth_callback_edit", null);
		$this->_cls_cbk_del = gOpt($opts, "cls_callback_delete", null);
	        $this->_mth_cbk_del = gOpt($opts, "mth_callback_delete", null);	
	
	}

	public function setForeignKeys($fkeys) {
		$this->_fkeys = $fkeys;
		foreach($this->_fkeys as $k=>$v) {
			$fkts = $this->_registry->db->getTableStructure($v['table']);
			$this->_fkeys[$k]['key'] = $fkts['primary_key'];
		}
	}

	public function setSpecialFields($sfields) {
		$this->_sfields = $sfields;
		foreach($this->_sfields as $k=>$v) {
			if($v['type']=='multicheck') {
				$fkts = $this->_registry->db->getTableStructure($v['table']);
				$this->_sfields[$k]['key'] = $fkts['primary_key'];
			}
		}
	}
	
	public function setPluginFields($pfields) {

		foreach($pfields as $k=>$v) {
			if(!isset($this->_registry->plugins[$v['plugin']]))
				exit(error::syserrorMessage(get_class($this), 'setPluginFields', sprintf(__("cantFindPlugin"), $v['plugin']), __LINE__));
		}
		$this->_pfields = $pfields;

	}

	public function setChangelistFields($fields) {
		
		if(is_array($fields))
			$this->_changelist_fields = $fields;

	}

	// fields which allow html insertion, only char and text type
	public function setHtmlFields($fields) {
		
		if(is_array($fields))
			$this->_html_fields = $fields;

	}

	public function manage() {

		if(!$this->_primary_key) return __("NoPrimaryKeyTable");

		$edit = isset($_GET['edit']) ? true : false;
		$insert = isset($_GET['insert']) ? true : false;
		$save = isset($_GET['save']) ? true : false;

		if($save) {
			$order = cleanInput('post', 'order', 'string');
			$order_param = $order ? "?order=".$order : '';
			$res = $this->saveFields(); 
			if(isset($_POST['submit_c_insert']) || isset($_POST['submit_c_modify'])) {
				// save and continue editing
				$_SESSION['adminTable_f_s_'.$this->_table] = $res;
				header("Location: ".preg_replace("#\?.*$#", "?edit".($order ? "&order=$order" : ""), $_SERVER['REQUEST_URI']));
			}
			else
				header("Location: ".preg_replace("#\?.*$#", $order_param, $_SERVER['REQUEST_URI']));
		}
		elseif($edit || $insert) return $this->editFields();
		else return $this->view();

		
	}

	public function view() {

		$order = cleanInput('get', 'order', 'string');

		$tot_fk = count($this->_fkeys);
		$tot_sf = count($this->_sfields);
		$tot_pf = count($this->_pfields);

		// get order field and direction
		preg_match("#^([^ ,]*)\s?((ASC)|(DESC))?.*$#", $order, $matches);
		$field_order = isset($matches[1]) ? $matches[1] : null;
		$order_dir = isset($matches[2]) ? $matches[2] : null;

		$fields_names = $this->_changelist_fields ? $this->_changelist_fields : $this->_registry->db->getFieldsName($this->_table);

		$pag = new pagination($this->_registry, $this->_efp, $this->_registry->db->getNumRecords($this->_table, null, $this->_primary_key));

		$limit = array($pag->start(), $this->_efp);

		if(count($this->_changelist_fields)) {
			if(!in_array($this->_primary_key, $this->_changelist_fields)) { 
				array_unshift($this->_changelist_fields, $this->_primary_key);
				array_unshift($fields_names, $this->_primary_key);
			}
			$field_selection = isset($this->_fkeys[$field_order]) 
					? 'a.'.implode(', a.', $this->_changelist_fields)
					: implode(',', $this->_changelist_fields);
		}
		else 
			$field_selection = isset($this->_fkeys[$field_order]) ? "a.*" : "*"; 

		// different queries if the order field is a foreign key
		if(isset($this->_fkeys[$field_order])) {
			$records = $this->_registry->db->autoSelect($field_selection, array($this->_table." AS a", $this->_fkeys[$field_order]['table']." AS b"), "a.$field_order=b.".$this->_fkeys[$field_order]['key'], "b.".$this->_fkeys[$field_order]['order']." $order_dir", $limit);
		}
		else 
			$records = $this->_registry->db->autoSelect($field_selection, $this->_table, null, $order, $limit);

		$all = "<span class=\"link\" onclick=\"$$('#atbl_form input[type=checkbox]').setProperty('checked', 'checked');\">".__("all")."</span>";
		$none = "<span class=\"link\" onclick=\"$$('#atbl_form input[type=checkbox]').removeProperty('checked');\">".__("none")."</span>";
		$heads = ($this->_edit_deny != 'all' || $this->_export) ? array("0"=>"$all | $none") : array();
		foreach($fields_names as $fn) {
			if(!$this->_changelist_fields || in_array($fn, $this->_changelist_fields)) {
				$ord = $order == $fn." ASC" ? $fn." DESC" : $fn." ASC";

				if($order == $fn." ASC") {
					$jsover = "$(this).getNext('img').setProperty('src', '$this->_arrow_down_path')";
					$jsout = "$(this).getNext('img').setProperty('src', '$this->_arrow_up_path')";
					$a_style = "visibility:visible";
					$apath = $this->_arrow_up_path;
				}
				elseif($order == $fn." DESC") {
					$jsover = "$(this).getNext('img').setProperty('src', '$this->_arrow_up_path')";
					$jsout = "$(this).getNext('img').setProperty('src', '$this->_arrow_down_path')";
					$js = "$(this).getNext('img').getNext('img').setStyle('visibility', 'visible')";
					$a_style = "visibility:visible";
					$apath = $this->_arrow_down_path;
				}
				else {
					$js = '';
					$jsover = "$(this).getNext('img').setStyle('visibility', 'visible')";
					$jsout = "$(this).getNext('img').setStyle('visibility', 'hidden')";
					$a_style = "visibility:hidden";
					$apath = $this->_arrow_up_path;
				}

				$link = preg_replace("#/p/\d+/#", "/", $_SERVER['REQUEST_URI']);
				$link = preg_replace("#\?.*#", "", $link);
				$head_t = anchor($link."?order=$ord", __($fn), array('over'=>$jsover, 'out'=>$jsout));
				$heads[] = $head_t." <img src=\"$apath\" alt=\"down\" style=\"$a_style\" />";
			}
		}

		$rows = array();
		foreach($records as $r) {
			$input = "<input type=\"checkbox\" name=\"f[]\" value=\"".$r[$this->_primary_key]."\" />";
			if($tot_fk) $r = $this->parseForeignKeys($r);
			if($tot_sf) $r = $this->parseSpecialFields($r);
			if($tot_pf) $r = $this->parsePluginFields($r);
			$r = $this->parseDateFields($r);
			if($this->_edit_deny=='all' && !$this->_export) $rows[] = $r;
			elseif(is_array($this->_edit_deny) && in_array($r[$this->_primary_key], $this->_edit_deny)) $rows[] = array_merge(array(""), $r);
			else $rows[] = array_merge(array($input), $r);
		}
		

		$this->_view->setTpl('table');
		$this->_view->assign('class', 'generic wide');
		$this->_view->assign('caption', __("RecordInTable")." ".$this->_table);
		$this->_view->assign('heads', $heads);
		$this->_view->assign('rows', $rows);

		$table = $this->_view->render();

		if($this->_edit_deny!='all' || $this->_export) {
			$myform = new form($this->_registry, 'post', 'atbl_form', array("validation"=>false));
			$formstart = $myform->sform('?edit'.($order ? "&order=$order" : ""), null);
			$formend = $myform->cform();
		}
		else {
			$formstart = '';
			$formend = '';
		}

		if($this->_edit_deny=='all') {
			$input_edit = '';
			$input_delete = '';
		}
		else {
			$onclick = "var checked = false;
				    var felements = $$('#atbl_form input[type=checkbox]');
				    for(var i=0;i<felements.length;i++) if(felements[i].checked) {checked = true;break;}
				    if(!checked) {alert('".jsVar(__("SelectAtleastRecord"))."'); return false;}";
			$input_edit = $myform->input('submit_edit', 'submit', __("edit"), array("js"=>"onclick=\"$onclick\""));
			if($this->_deletion)
				$input_delete = $myform->input('submit_delete', 'submit', __("delete"), array("js"=>"onclick=\"$onclick return confirmSubmit('".jsVar(__("ProcedeDeleteSelectedRecords"))."')\""));
			else $input_delete = '';
		}

		if($this->_export) {
			$onclick = "var checked = false;
				    var felements = $$('#atbl_form input[type=checkbox]');
				    for(var i=0;i<felements.length;i++) if(felements[i].checked) {checked = true;break;}
				    if(!checked) {alert('".jsVar(__("SelectAtleastRecord"))."'); return false;}";
			$input_export_selected = $myform->input('submit_export_selected', 'submit', __("exportSelected"), array("js"=>"onclick=\"$onclick \""));
			$input_export_all = $myform->input('submit_export_all', 'submit', __("exportAll"), array());
			$input_where_query = $myform->hidden('where_query', '');
		
		}
		else {
			$input_export_selected = null;
			$input_export_all = null;
			$input_where_query = '';	
		}

		$link_insert = $this->_insertion ? anchor("?insert", __("insertNewRecord")) : null;
		
		$tpl_name = isset($this->_custom_tpl['view']) ? $this->_custom_tpl['view'] : 'admin_table';

		$this->_view->setTpl($tpl_name);
		$this->_view->assign('table', $table);
		$this->_view->assign('link_insert', $link_insert);
		$this->_view->assign('formstart', $formstart);
		$this->_view->assign('formend', $formend);
		$this->_view->assign('input_edit', $input_edit);
		$this->_view->assign('input_delete', $input_delete);
		$this->_view->assign('input_where_query', $input_where_query);
		$this->_view->assign('input_export_selected', $input_export_selected);
		$this->_view->assign('input_export_all', $input_export_all);
		$this->_view->assign('psummary', $pag->summary());
		$this->_view->assign('pnavigation', $pag->navigation());

		return $this->_view->render();
	}

	public function parseForeignKeys($row) {

		$res = array();

		foreach($row as $k=>$v) {
			if(isset($this->_fkeys[$k])) {
				$fkts = $this->_registry->db->getTableStructure($this->_fkeys[$k]['table']);
				$fk = $this->_registry->db->autoSelect($this->_fkeys[$k]['field'], $this->_fkeys[$k]['table'], $this->_fkeys[$k]['key']."='$v'" , null);
				$res[$k] = isset($fk[0]) ? $fk[0][$this->_fkeys[$k]['field']] : null;
			}
			else $res[$k] = $v;
		}

		return $res;

	}

	public function parseSpecialFields($row, $opts=null) {

		$res = array();
		foreach($row as $k=>$v) {
			if(isset($this->_sfields[$k])) {
				if($this->_sfields[$k]['type']=='password') $res[$k] = $v ? (gOpt($opts, 'show_pwd', false) ? $v : "**************") : '';
				elseif($this->_sfields[$k]['type']=='bool')
					$res[$k] = $v ? $this->_sfields[$k]['true_label'] : $this->_sfields[$k]['false_label'];
				elseif($this->_sfields[$k]['type']=='email') {
					$mailto = isset($this->_sfields[$k]['list_mailto']) && $this->_sfields[$k]['list_mailto'] && gOpt($opts, 'mailto', true) ? true : false;
					$res[$k] = $v ? ($mailto ? anchor('mailto:'.$v, $v) : $v) : '';
				}
				elseif($this->_sfields[$k]['type']=='multicheck') {
					$vf = array();
					foreach(explode(",", $v) as $vp) {
						$fkts = $this->_registry->db->getTableStructure($this->_sfields[$k]['table']);
						$fk = $this->_registry->db->autoSelect($this->_sfields[$k]['field'], $this->_sfields[$k]['table'], $this->_sfields[$k]['key']."='$vp'" , null);
						$vf[] = isset($fk[0]) ? $fk[0][$this->_sfields[$k]['field']] : '';
					}
					$res[$k] = implode(", ", $vf);
				}
				elseif($this->_sfields[$k]['type']=='file' || $this->_sfields[$k]['type']=='image') {
					$sf = $this->_sfields[$k];
					if($sf['preview'] && $v)
						$res[$k] = "<a title=\"$v\" href=\"".$sf['rel_path']."/$v\">".$v."</span><script>$$('a[href=".$sf['rel_path']."/$v]')[0].cerabox();</script>";
					else $res[$k] = $v;
				}
			}
			else $res[$k] = $v;
		}

		return $res;

	}

	public function parsePluginFields($row) {

		$res = array();

		foreach($row as $k=>$v) {
			if(isset($this->_pfields[$k])) {
				$plugin = $this->_pfields[$k]['plugin'];
				$res[$k] = $this->_registry->plugins[$plugin]->adminList($this->_pfields[$k], $v);
			}
			else $res[$k] = $v;	
		}	

		return $res;
	}
	
	public function parseDateFields($row) {

		$res = array();

		$structure = $this->_registry->db->getTableStructure($this->_table);

		foreach($row as $k=>$v) {
			if($structure['fields'][$k]['type']=='date') $res[$k] = $this->_registry->dtime->view($v, 'date');
			elseif($structure['fields'][$k]['type']=='datetime') $res[$k] = $this->_registry->dtime->view($v);
			elseif($structure['fields'][$k]['type']=='timestamp') $res[$k] = $this->_registry->dtime->view($v);
			else $res[$k] = $v;
		}

		return $res;

	}

	protected function checkUpload() {
		
		foreach($this->_sfields as $fname=>$finfo) if($finfo['type']=='file' || $finfo['type']=='image') return true;
		return false;

	}

	public function editFields($opts=null) {

		$insert = (isset($_GET['insert']) || gOpt($opts, 'insert')) ? true : false;
		$order = cleanInput('get', 'order', 'string');
		$order_param = $order ? "?order=".$order : '';
		$submit_edit = cleanInput('post', 'submit_edit', 'string');
		$submit_delete = cleanInput('post', 'submit_delete', 'string');
		$submit_export_selected = cleanInput('post', 'submit_export_selected', 'string');
		$submit_export_all = cleanInput('post', 'submit_export_all', 'string');

		if($insert && !$this->_insertion) header("Location: ".preg_replace("#\?.*$#", "", $_SERVER['REQUEST_URI']));

		$formaction = gOpt($opts, 'action', '?save');
		$f_s = gOpt($opts, "f_s", null);
		if(is_null($f_s)) {
			if(isset($_POST['f'])) $f_s = cleanInputArray('post', 'f', 'string');
			elseif(isset($_SESSION['adminTable_f_s_'.$this->_table])) $f_s = $_SESSION['adminTable_f_s_'.$this->_table]; 
			else $f_s = array();
		}
		if((!$insert && !$submit_export_all) && !$f_s) header("Location: ".preg_replace("#\?.*$#", "", $_SERVER['REQUEST_URI']));
		if($submit_export_selected) $this->export($f_s);
		if($submit_export_all) $this->export('all', cleanInput('post', 'where_query', 'string', array("escape"=>false)));
		if($submit_delete) {
			if(!$this->_deletion || $this->_edit_deny=='all') header("Location: ".preg_replace("#\?.*$#", "", $_SERVER['REQUEST_URI']));
			if(count($f_s)) {
				if($this->_cls_cbk_del && $this->_mth_cbk_del)
					call_user_func(array($this->_cls_cbk_del,$this->_mth_cbk_del), $this->_registry, $f_s);
				else {
					if(is_array($this->_edit_deny) && count($this->_edit_deny)) $f_s = array_diff($f_s, $this->_edit_deny);
					$this->deleteFiles($f_s);
					if(count($this->_pfields)) {
						foreach($this->_pfields as $k=>$v) {
							$this->_registry->plugins[$v['plugin']]->adminDelete($v, $f_s);
						}
					}
					$where = $this->_primary_key."='".implode("' OR ".$this->_primary_key."='", $f_s)."'";
					$this->_registry->db->delete($this->_table, $where);
				}
			}
			header("Location: ".preg_replace("#\?.*$#", $order_param, $_SERVER['REQUEST_URI']));
			exit();
		}

		$myform = new form($this->_registry, 'post', 'atbl_form', array("validation"=>true));
		$myform->load();

		$buffer = $myform->sform($formaction, null, array("upload"=>$this->checkUpload()));
		$buffer .= $myform->hidden('order', $order);

		if($insert) {
			foreach($this->_fields as $fname=>$field) {
				if($field['extra']!='auto_increment') $buffer .= $this->formElement($myform, $fname, $field, null);
			}
		}
		elseif(count($f_s)) {
			if($this->_edit_deny=='all') header("Location: ".preg_replace("#\?.*$#", "", $_SERVER['REQUEST_URI']));
			foreach($f_s as $f) {
				if(!is_array($this->_edit_deny) || !in_array($f, $this->_edit_deny)) {
					$content = $this->formRecord($f, $myform);
					if(array_key_exists($this->_primary_key, $this->_fkeys)) {
						$fk = $this->_fkeys[$this->_primary_key];
						$records = $this->_registry->db->autoSelect($fk['field'], $fk['table'], $fk['key']."='$f'" , null);
						$value_p = $records[0][$fk['field']];
					}
					else $value_p = $f;
					$buffer .= $myform->fieldset(__("Record")." ".$this->_primary_key." = $value_p", $content);
				}
			}
		}

		$buffer .= $myform->input('submit_'.($insert ? "insert" : "modify"), 'submit', __('save'), array());
		$buffer .= "&#160;".$myform->input('submit_c_'.($insert ? "insert" : "modify"), 'submit', __('saveContinueEditing'), array());

		$buffer .= $myform->cform();
		
		if($this->_editor) $buffer .= chargeEditor($this->_registry, "#atbl_form div[class=html]");

		if($insert && isset($this->_custom_tpl['insert'])) {
			$this->_view->setTpl($this->_custom_tpl['insert']);
			$this->_view->assign('form', $buffer);
			return $this->_view->render();
		}
		elseif(isset($this->_custom_tpl['edit'])) {
			$this->_view->setTpl($this->_custom_tpl['edit']);
			$this->_view->assign('form', $buffer);
			return $this->_view->render();
		}	
		else {
			return $buffer;
		}

	}

	protected function deleteFiles($f_s) {
	
		if(!count($this->_sfields)) return 0;

		foreach($f_s as $fid) {
			foreach($this->_sfields as $fname=>$fopt) {
				if($fopt['type']=='file') {
					$rows = $this->_registry->db->autoSelect($fname, $this->_table, $this->_primary_key."='$fid'");
					$filename = $rows[0][$fname];
					@unlink($fopt['path'].DS.$filename);	
				}	
				elseif($fopt['type']=='image') {
					$rows = $this->_registry->db->autoSelect($fname, $this->_table, $this->_primary_key."='$fid'");
					$filename = $rows[0][$fname];
					@unlink($fopt['path'].DS.$filename);	
					if($fopt['make_thumb']) {
						$prefix_thumb = isset($fopt['prefix_thumb']) ? $fopt['prefix_thumb'] : 'thumb_';
						@unlink($fopt['path'].DS.$prefix_thumb.$filename);	
					}
				}
			}
		}

		return 0;

	}

	public function formRecord($pk, $myform=null, $formaction=null) {

		$buffer = '';
		if(!$myform) {
			if(!$formaction) $formaction = '?save';
			$myform = new form($this->_registry, 'post', 'atbl_form', array("validation"=>true));
			$myform->load();
			$buffer .= $myform->sform($formaction, null, array("upload"=>$this->checkUpload()));
		}	

		$buffer .= $myform->hidden($this->_primary_key."[]", $pk);
		foreach($this->_fields as $fname=>$field) {
			if($fname != $this->_primary_key && $field['extra']!='auto_increment') 
				$buffer .= $this->formElement($myform, $fname, $field, $pk);
		}

		if(!$myform) $buffer .= $myform->cform();

		return $buffer;

	}

	 protected function formElement($myform, $fname, $field, $id) {
	
		$id_f = preg_replace("#\s#", "_", $id); // replace spaces with '_' in form names as POST do itself

		$required = $field['null']=='NO' ? true : false;

		$records = $this->_registry->db->autoSelect("*", $this->_table, $this->_primary_key."='$id'", null);
		$value = count($records) ? $records[0][$fname] : null;

		if(array_key_exists($fname, $this->_sfields)) {
			if($this->_sfields[$fname]['type']=='password') { 
				$label = ($id && isset($this->_sfields[$fname]['edit_lable'])) 
					? $this->_sfields[$fname]['edit_lable'] 
					: (isset($this->_sfields[$fname]['insert_label']) ? $this->_sfields[$fname]['insert_label']:'');
				$req = $id ? false : true;
				return $myform->cinput($fname."_".$id_f, 'password', '', array(htmlVar($fname), $label), array("required"=>$req, "size"=>40, "maxlength"=>$field['max_length']));
			}
			elseif($this->_sfields[$fname]['type']=='bool') {
				$t_l = 	$this->_sfields[$fname]['true_label'];
				$f_l = 	$this->_sfields[$fname]['false_label'];
				$dft = 	isset($this->_sfields[$fname]['default']) ? $this->_sfields[$fname]['default'] : 0;
				return $myform->cradio($fname."_".$id_f, $myform->retvar($fname."_".$id_f, $value), array(1=>$t_l,0=>$f_l), $dft, htmlVar($fname), array("required"=>$required));
			}
			elseif($this->_sfields[$fname]['type']=='email') {
				$pattern = "^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$";
				$hint = "mario.rossi@example.com";
				return $myform->cinput($fname."_".$id_f, 'text', $myform->retvar($fname."_".$id_f, $value), htmlVar($fname), array("pattern"=>$pattern, "required"=>$required)); 
			}
			elseif($this->_sfields[$fname]['type']=='multicheck') {
				$sf = $this->_sfields[$fname];
				$options = $this->_registry->db->autoSelect(array($sf['key']." AS value", $sf['field']), $sf['table'], $sf['where'], $sf['order']);
				return $myform->cmulticheckbox($fname."_".$id_f."[]", $myform->retvar($fname."_".$id_f, explode(",", $value)), $options, htmlVar($fname), array("required"=>$required));
			}
			elseif($this->_sfields[$fname]['type']=='file' || $this->_sfields[$fname]['type']=='image') {
				$sf = $this->_sfields[$fname];
				$preview = isset($sf['preview']) ? $sf['preview'] : false;
				$rel_path = $sf['rel_path'];
				return $myform->cinput_file($fname."_".$id_f, $myform->retvar($fname, $value), $sf['label'], array("required"=>$required, "extensions"=>$sf['extensions'], "preview"=>$preview, "rel_path"=>$rel_path));
			}
		}
		elseif(array_key_exists($fname, $this->_fkeys)) {
			$fk = $this->_fkeys[$fname];
			$options = $this->_registry->db->autoSelect(array($fk['key'], $fk['field']), $fk['table'], $fk['where'], $fk['order']);
			$data = array();
			foreach($options as $rec) 
				$data[htmlInput($rec[$fk['key']])] = htmlVar($rec[$fk['field']]);
			return $myform->cselect($fname."_".$id_f, $myform->retvar($fname."_".$id_f, $value), $data, htmlVar($fname), array("required"=>$required));
		}
		elseif(array_key_exists($fname, $this->_pfields)) {
			return $this->_registry->plugins[$this->_pfields[$fname]['plugin']]->formAdmin($this->_pfields[$fname], $fname."_".$id_f, $fname, $field, $myform, $myform->retvar($fname."_".$id_f, $value));
		}
		elseif($field['type'] == 'int') 
			return $myform->cinput($fname."_".$id_f, 'text', $myform->retvar($fname, $value), htmlVar($fname), array("required"=>$required, "size"=>$field['n_int'], "maxlength"=>$field['n_int']));
		elseif($field['type'] == 'float' || $field['type'] == 'double' || $field['type'] == 'decimal')
			return $myform->cinput($fname."_".$id_f, 'text', $myform->retvar($fname."_".$id_f, $value), htmlVar($fname), array("required"=>$required, "size"=>($field['n_int']+1+$field['n_precision']), "maxlength"=>($field['n_int']+1+$field['n_precision'])));
		elseif($field['type'] == 'varchar')
			return $myform->cinput($fname."_".$id_f, 'text', $myform->retvar($fname."_".$id_f, $value), htmlVar($fname), array("required"=>$required, "size"=>$field['max_length']<40 ? $field['max_length'] : 40, "maxlength"=>$field['max_length']));
		elseif($field['type'] == 'text')
                	return $myform->ctextarea($fname."_".$id_f, $myform->retvar($fname."_".$id_f, $value), htmlVar($fname), array("required"=>$required, "cols"=>45, "rows"=>6, "editor"=>(in_array($fname, $this->_html_fields) && $this->_editor)  ? true : false));
		elseif($field['type'] == 'date')
                	return $myform->cinput_date($fname."_".$id_f, $myform->retvar($fname."_".$id_f, $value), htmlVar($fname), array("required"=>$required));
		elseif($field['type'] == 'datetime')
			return $myform->cinput_datetime($fname."_".$id_f, $myform->retvar($fname."_".$id_f, $value), htmlVar($fname), array("required"=>$required));

	}

	public function saveFields() {

		$myform = new form($this->_registry, 'post', 'atbl_form', array("validation"=>false));
		$myform->save();

		// save and continue editing clear session
		if(isset($_SESSION['adminTable_f_s_'.$this->_table])) unset($_SESSION['adminTable_f_s_'.$this->_table]);

		$res = array();
		$pkeys = cleanInputArray('post', $this->_primary_key, 'string');
		$insert = false;
		if(!$pkeys) { 
			$pkeys = array(0=>null); 
			$insert = true; 
			if(!$this->_insertion) {
				header("Location: ".preg_replace("#\?.*$#", "", $_SERVER['REQUEST_URI']));
				exit();
			}
		}
		else {
			if($this->_edit_deny=='all') {
				header("Location: ".preg_replace("#\?.*$#", "", $_SERVER['REQUEST_URI']));
				exit();
			}
		}

		if(count($pkeys)) {
			foreach($pkeys as $pk) {
				$res[] = $this->saveRecord($pk, $pkeys);
			}
		}

		return $res;

	}

	protected function saveRecord($pk, $pkeys) {
		
		if(!in_array($pk, $this->_edit_deny)) {
			$res = array();
			if(is_null($pk)) {
				$fields = $this->_registry->db->getFieldsName($this->_table);
				foreach($fields as $f) $res[$f] = null;	
				$data = $res;
				$pkf = $pk;
				$insert = true;
			}
			else {
				$pkf = preg_replace("#\s#", "_", $pk); // POST replaces spaces with '_'
				$res = $this->_registry->db->autoSelect(array("*"), array($this->_table), $this->_primary_key."='$pk'", null);
				$data = $res[0];
				$insert = false;
			}
			$model = new model($data);
			$model->setRegistry($this->_registry);
			$model->setIdName($this->_primary_key);
			$model->setTable($this->_table);

			$structure = $this->_registry->db->getTableStructure($this->_table);

			foreach($this->_fields as $fname=>$field) 
				if(array_key_exists($fname, $this->_sfields)) 
					$this->cleanSpecialField($model, $fname, $pkf, $field['type'], $insert);
				elseif(array_key_exists($fname, $this->_pfields)) 					
					$this->_registry->plugins[$this->_pfields[$fname]['plugin']]->cleanField($this->_pfields[$fname], $model, $fname, $pkf, $insert);
				elseif(isset($_POST[$fname."_".$pkf]) && ($fname != $this->_primary_key || is_null($pk)) && $field['extra']!='auto_increment' && in_array($fname, $this->_html_fields)) 
					$model->{$fname} = $this->cleanField($fname."_".$pkf, 'html');
				elseif(isset($_POST[$fname."_".$pkf]) && ($fname != $this->_primary_key || is_null($pk)) && $field['extra']!='auto_increment') 
					$model->{$fname} = $this->cleanField($fname."_".$pkf, $field['type']);
			
			$res = $model->saveData($insert);

			if(count($this->_pfields)) 
				foreach($this->_pfields as $fname=>$pf) 
					if(method_exists($this->_registry->plugins[$pf['plugin']], 'afterModelSaved'))
							$this->_registry->plugins[$pf['plugin']]->afterModelSaved($pf, $model, $fname, $this->_primary_key, $insert);

			if(!$res) {
				if(!$insert) $_SESSION['adminTable_f_s_'.$this->_table] = $pkeys;
				$link_error = preg_replace("#\?.*$#", "?".($insert ? "insert" : "edit"), $_SERVER['REQUEST_URI']);
				$error = $this->_registry->db->getError();

				if($error['error']==1001) {
					$field = $structure['keys'][$error['key']-1];
					if(isset($this->_fkeys[$field])) {
						$fk = $this->_fkeys[$field];
						$er_values = $this->_registry->db->autoSelect(array($fk['field']), $fk['table'], $fk['key']."='".$error['value']."'", null);
						$er_value = substr($er_values[0][$fk['field']], 0, 50);
					}
					else $er_value = substr($error['value'], 0, 50);

					$errormsg = sprintf(__('duplicateKeyEntryError'), $error['value'], $field);
				}
				exit(error::errorMessage(array('error'=>$errormsg), $link_error));
				
			}

			return $model->{$this->_primary_key};
		}
	}

	protected function cleanField($name, $type) {
	
		if($type=='int') return cleanInput('post', $name, 'int');
		elseif($type=='float' || $type=='double' || $type=='decimal') return cleanInput('post', $name, 'float');
		elseif($type=='varchar' || $type=='text') return cleanInput('post', $name, 'string');
		elseif($type=='html') return cleanInput('post', $name, 'html');
		elseif($type=='date') return cleanInput('post', $name, 'date');
		elseif($type=='datetime') return cleanInput('post', $name, 'datetime');

	}

	protected function cleanSpecialField($model, $fname, $pk, $type, $insert) {
	
		if($this->_sfields[$fname]['type']=='password') {
			if(!$insert && !cleanInput('post', $fname.'_'.$pk, 'string')) return 0;

			if(PWD_HASH=='md5') $model->{$fname} = md5(cleanInput('post', $fname.'_'.$pk, 'string'));	
			elseif(PWD_HASH=='sha1') $model->{$fname} = sha1(cleanInput('post', $fname.'_'.$pk, 'string'));	
			else $model->{$fname} = cleanInput('post', $fname.'_'.$pk, 'string');	
		}
		elseif($this->_sfields[$fname]['type']=='bool') $model->{$fname} = cleanInput('post', $fname.'_'.$pk, 'int');
		elseif($this->_sfields[$fname]['type']=='email') $model->{$fname} = cleanInput('post', $fname.'_'.$pk, 'email');
		elseif($this->_sfields[$fname]['type']=='multicheck') {
			$checked = cleanInputArray('post', $fname.'_'.$pk, $this->_sfields[$fname]['value_type']);
			$model->{$fname} = implode(",", $checked);
		}
		elseif($this->_sfields[$fname]['type']=='file') {
			$link_error = preg_replace("#\?.*$#", "", $_SERVER['REQUEST_URI']);
			$sf = $this->_sfields[$fname];
			$myform = new form($this->_registry, 'post', 'atbl_form', array("validation"=>false));
			$model->{$fname} = $myform->uploadFile($fname.'_'.$pk, $sf['extensions'], $sf['path'], $link_error, null);
		}
		elseif($this->_sfields[$fname]['type']=='image') {
			$link_error = preg_replace("#\?.*$#", "", $_SERVER['REQUEST_URI']);
			$sf = $this->_sfields[$fname];
			$opts['resize'] = isset($sf['resize']) ? $sf['resize'] : false;
			$opts['scale'] = isset($sf['scale']) ? $sf['scale'] : false;
			$opts['enlarge'] = isset($sf['resize_enlarge']) ? $sf['resize_enlarge'] : false;
			$opts['make_thumb'] = isset($sf['make_thumb']) ? $sf['make_thumb'] : false;
			$opts['prefix'] = isset($sf['prefix']) ? $sf['prefix'] : '';
			$opts['prefix_thumb'] = isset($sf['prefix_thumb']) ? $sf['prefix_thumb'] : 'thumb_';
			$opts['resize_width'] = isset($sf['resize_width']) ? $sf['resize_width'] : null;
			$opts['resize_height'] = isset($sf['resize_height']) ? $sf['resize_height'] : null;
			$opts['thumb_width'] = isset($sf['thumb_width']) ? $sf['thumb_width'] : null;
			$opts['thumb_height'] = isset($sf['thumb_height']) ? $sf['thumb_height'] : null;
			$myform = new form($this->_registry, 'post', 'atbl_form', array("validation"=>false));
			$model->{$fname} = $myform->uploadImage($fname.'_'.$pk, $sf['extensions'], $sf['path'], $link_error, $opts);
		}

	}

	protected function export($f_s, $where='') {

		if(!is_array($f_s) && $f_s!='all') {
			header("Location: ".preg_replace("#\?.*$#", "", $_SERVER['REQUEST_URI']));
			exit();
		}

		if(is_array($f_s) && count($f_s)) $rids = implode(",", $f_s);
		elseif(!$where) $rids = '*';
		else {
			$rids_a = array();
			$records = $this->_registry->db->autoSelect($this->_primary_key, $this->_table, $where);
			foreach($records as $r) $rids_a[] = $r[$this->_primary_key];
			$rids = implode(",", $rids_a);
		}		

		$expObj = new export($this->_registry, array("table"=>$this->_table, "pkey"=>$this->_primary_key, "sfields"=>$this->_sfields, "fkeys"=>$this->_fkeys));
		$expObj->setRids($rids);

		$expObj->exportData($this->_table.'_'.$this->_registry->dtime->now('%Y%m%d').'.csv', 'csv');

		exit();
	
	}

}

?>
