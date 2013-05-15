<?php
/**
 * @file adminTable.class.php
 * @brief Contains the class which manages the auto generation of the backoffice.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup backoffice Backoffice
 * 
 * ### Auto generated backoffice functionality
 * 
 * With a few lines of code is possible to handle the insertion, modification, deletion and export of data of a database table.
 *
 * If there is a need to implement specific behaviour it is possible to extend the adminTable class in order to modify or add the desired functionality.
 */

/**
 * @ingroup core backoffice
 * @brief Class which generates automatically the backoffice for the management of a database table 
 * 
 * This class creates a default back-office interface for managing a database table (which has a primary key field). It gets the table structure directly from the database 
 * and constructs a navigation view to surf through inserted data, an insertion/edit view (the form) to add or update records and all the necessary actions to perform insertion, 
 * modification, deletion and exportation.
 *
 * The adminTable class can also manage automatically foreign keys, some particular fields (constant, password, bool, enum, email, multicheck, file, image, date, datetime), 
 * html fields (charging dojo html editor if needed) and fields managed by extra plugins.
 * Also it is possible to automatically add filters for the list view. It's enough to set the filters fields and the class adds for you the filters form and performs the research.
 * Filters field may be text, int, float, bool and foreign keys fields.
 *
 * So if it's not necessary to have a specific logic that regulates the access and actions doable over a database table, 
 * with few lines of code is possible to create the entire table back office. All you have to do is set the foreign keys and/or special fields if needed
 * and then call the manage method. Then Jeff shows you a list of inserted records with the possibility to edit/delete them or insert a new one.
 * All these operations are managed automatically by the manage method.
 * In alternative you may use only some features of this class, like using only the records paginated list (setting as not editable the fields: edit_deny='all') 
 * or using only the auto generated form.
 * Clearly even if the module requires something different, it's always possible to extend this class in order to overwrite only the aspects that need a proper customization.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class adminTable {
	
	/**
	 * @brief the registry singleton instance 
	 */
	protected $_registry;
	
	/**
	 * @brief the database table 
	 */
	protected $_table;

	/**
	 * @brief the table model 
	 */
	protected $_model;

	/**
	 * @brief text displayed before the list of records 
	 */
	protected $_backoffice_text;
	
	/**
	 * @brief the primary key name defined for the table 
	 */
	protected $_primary_key;

	/**
	 * @brief the table column names 
	 */
	protected $_fields;

	/**
	 * @brief the table column labels to use in form and visualization
	 */
	protected $_fields_labels;

	/**
	 * @brief definition of the foreign keys 
	 */
	protected $_fkeys;
	
	/**
	 * @brief definition of the special fields 
	 */
	protected $_sfields;
	
	/**
	 * @brief definition of the plugin fields 
	 */
	protected $_pfields;

	/**
	 * @brief definition of the html fields 
	 */
	protected $_html_fields;

	/**
	 * @brief definition tables and some properties which have the model as foreign key 
	 */
	protected $_is_foreign;

	/**
	 * @brief definition of form fieldsets 
	 */
	protected $_fieldsets;
	
	/**
	 * @brief number of records for page 
	 */
	protected $_efp;
	
	/**
	 * @brief delete action callback class 
	 */
	protected $_cls_cbk_del;
	
	/**
	 * @brief delete action callback method 
	 */
	protected $_mth_cbk_del;
	
	/**
	 * @brief allow record insertion 
	 */
	protected $_insertion;

	/**
	 * @brief show save and continue editing button 
	 */
	protected $_save_and_continue;
	
	/**
	 * @brief allow record deletion 
	 */
	protected $_deletion;

	/**
	 * @brief deny record modification 
	 */
	protected $_edit_deny;

	/**
	 * @brief fields not inserted and edit directly by form elements 
	 */
	protected $_no_form_fields;

	/**
	 * @brief array of fields shown in the admin list 
	 */
	protected $_changelist_fields;

	/**
	 * @brief information to show before the insert and edit form 
	 */
	protected $_backoffice_form_text;
	
	/**
	 * @brief array of fields used to filter records in the admin list 
	 */
	protected $_filter_fields;
	
	/**
	 * @brief charge dojo editor for html fields 
	 */
	protected $_editor;
	
	/**
	 * @brief associative array of custom templates 
	 */
	protected $_custom_tpl;
	
	/**
	 * @brief @ref view instance 
	 */
	protected $_view;

	/**
	 * @brief relative path to the arrow down icon
	 */
	protected $_arrow_down_path;

	/**
	 * @brief relative path to the arrow up icon
	 */
	protected $_arrow_up_path;

	/**
	 * @brief Constructs an adminTable instance 
	 * 
	 * @param string $table the database table
	 * @param array $opts
	 *   Associative array of options: 
	 *   - **insertion**: bool default true. Whether to allow records insertion or not. 
	 *   - **save_and_continue**: bool default true. Whether to show the save and continue editing option in forms. 
	 *   - **deletion**: bool default true. Whether to allow records deletion or not. 
	 *   - **edit_deny**: mixed default array(). Deny modification for some records. Possible values are 'all', or an array of record id. 
	 *   - **changelist_fields**: array default null. Array of fields to be shown in the admin list. 
	 *   - **backoffice_form_text**: string default ''. A brief information to show before the edit and insert form. 
	 *   - **no_form_fields**: array default null. Array of fields not controlled directly by form elements. 
	 *   - **editor**: bool default false. Charge dojo editor for html fields insertion/modification. 
	 *   - **export**: bool default false. Add export buttons in the admin list. 
	 *   - **custom_tpl**: array default array(). Associative array defining custom templates, available keys are 'view', 'insert', 'edit'. 
	 *   - **efp**: int default 20. Number of records shown in the admin list. 
	 *   - **cls_callback_delete**: string default null. Class to call when performing a delete action. 
	 *   - **mth_callback_delete**: string default null. Method to call when performing a delete action. 
	 *   - **backoffice_text**: string default null. Introduction text displayed before the list of records. 
	 *
	 * @return void
	 */
	function __construct($table, $opts=null) {

		$this->_registry = registry::instance();
		$this->_table = $table;
		$this->_view = new view();

		/* options */
		$this->_insertion = gOpt($opts, 'insertion', true);
		$this->_save_and_continue = gOpt($opts, 'save_and_continue', true);
		$this->_deletion = gOpt($opts, 'deletion', true);
		$this->_edit_deny = gOpt($opts, 'edit_deny', array());
		$this->_changelist_fields = gOpt($opts, 'changelist_fields', null);
		$this->_no_form_fields = gOpt($opts, 'no_form_fields', array());
		$this->_editor = gOpt($opts, 'editor', false);
		$this->_export = gOpt($opts, 'export', false);
		$this->_custom_tpl = gOpt($opts, 'custom_tpl', array());
		$this->_efp = gOpt($opts, "efp", 20);
		$this->_cls_cbk_del = gOpt($opts, "cls_callback_delete", null);
	        $this->_mth_cbk_del = gOpt($opts, "mth_callback_delete", null);
		$this->_backoffice_text = gOpt($opts, 'backoffice_text', '');	
		$this->_backoffice_form_text = gOpt($opts, 'backoffice_form_text', '');	

		$structure = $this->_registry->db->getTableStructure($this->_table);

		$this->_primary_key = $structure['primary_key'];
		$this->_fields = $structure['fields'];
    $this->_fields_labels = array();
		$this->_fkeys = array();
		$this->_sfields = array();
		$this->_pfields = array();
		$this->_html_fields = array();
		$this->_model = null;
		$this->_is_foreign = array();
		$this->_fieldsets = array();
    $this->_filter_fields = array();

		$this->_arrow_down_path = ROOT."/img/down_arrow-black.png";
		$this->_arrow_up_path = ROOT."/img/up_arrow-black.png";
	
	}

	/**
	 * @brief Sets the table model 
	 * 
	 * @param string $model the model class 
	 *
	 * @return void
	 */
	public function setModel($model) {

		if(!class_exists($model)) {
			error::syserrorMessage('adminTable', 'setModel', __('modelDosntExist'), __LINE__);
		}
		$this->_model = $model;
	} 

	/**
	 * @brief Sets the fields' labels 
	 * 
	 * @param array $fields_labels 
	 *   Associative array in the form 'field_name'=>properties. Properties is an associative array having keys:
	 *   - **label**: the field label
	 *   - **helptext**: the help text shown in the label of the input form 
	 *
	 * @return void
	 */
  public function setFieldsLabels($fields_labels) {
    $this->_fields_labels = $fields_labels;
  }

	/**
	 * @brief Sets table foreign keys 
	 * 
	 * @param array $fkeys 
	 *   Associative array in the form 'field_name'=>properties. Properties is an associative array having keys:
	 *   - **table**: the table related with the field 
	 *   - **field**: the representative field of the related table record 
	 *   - **where**: the where clause used to filter the related table records 
	 *   - **order**: the order clause used to order the related table records 
	 *
	 * @return void
	 */
	public function setForeignKeys($fkeys) {
		$this->_fkeys = $fkeys;
		foreach($this->_fkeys as $k=>$v) {
			$fkts = $this->_registry->db->getTableStructure($v['table']);
			$this->_fkeys[$k]['key'] = $fkts['primary_key'];
		}
	}

	/**
	 * @brief Sets table special fields 
	 *
	 * The supported special fields types are: constant, password, bool, enum, email, multicheck (many to many), file, image, date, datetime.    
	 * Date and datetime types are automatically recognized, so they have to be used as special fields only if some behaviors are needed, like the autonow (last edit date), or the autonow_add (insertion date)
	 * 
	 * @param array $sfields 
	 *   Associative array in the form 'field_name'=>properties. Properties is an associative array having keys:
	 *   - **type**: string. The type of special field. Possible values are: 'constant', 'password', 'bool', 'enum', 'email', 'multicheck', 'file', 'image', 'date', 'datetime'
	 *   - **value**: mixed. (constant type) The value of the field
	 *   - **true_label**: (bool type) string. The label tied to the value true
	 *   - **false_label**: (bool type) string. The label tied to the value false
	 *   - **default**: (bool type) mixed. The deafult value
	 *   - **data**: (enum type) array. The associative array containing the allowed values in the form 'value'=>'label'
	 *   - **key_type**: (enum type) string. The data type of the values (int, string, float, ...)
	 *   - **list_mailto**: (email type) bool. Whether to show or not a mailto link in the admin list view
	 *   - **value_type**: (multicheck and constant types) string. The type of the values (int, string, float, ...)
	 *   - **table**: (multicheck type) string. The name of the related table
	 *   - **field**: (multicheck type) string. The field of the related table to display in the multicheck form element
	 *   - **where**: (multicheck type) string. The where clause used to select only some records from the related table
	 *   - **order**: (multicheck type) string. The order clause used to order the records selected from the related table
	 *   - **path**: (image and file types) string. The absolute path of the uploading directory
	 *   - **rel_path**: (image and file types) string. The relative path of the uploading directory
	 *   - **preview**: (image and file types) bool. Whether to show file preview in the list view and the edit form view
	 *   - **extensions**: (image and file types) array. List of allowed extensions (all allowed if empty)
	 *   - **check_content**: (image and file types) bool default true. Whether to check the file mime-type agains the allowed ones
	 *   - **contents_allowed**: (image and file types) array. List of allowed mime types in substitution of the default ones
	 *   - **resize**: (image type) bool. Whether to resize the image or not
	 *   - **scale**: (image type) mixed. Whether to scale the image or not. Possible values: false or an integer which represents the percentage to scale the image to
	 *   - **resize_enlarge**: (image type) mixed. Whether to allow image enlargement during resize or not. The enlargement is always allowed for the thumbnail
	 *   - **make_thumb**: (image type) bool. Whether to make a thumbnail of the image or not
	 *   - **prefix**: (image type) string. The prefix added to the image filename
	 *   - **prefix_thumb**: (image type) string default 'thumb_'. The prefix added to the thumbnail file. It sums to the image prefix
	 *   - **resize_width**: (image type) int. The width of the resized image
	 *   - **resize_height**: (image type) int. The height of the resized image
	 *   - **thumb_width**: (image type) int. The width of the thumb image
	 *   - **thumb_height**: (image type) int. The height of the thumb image
	 *   - **autonow: (date and datetime type) bool, default false. Whether to automatically set the field to now when the record is saved or not
	 *   - **autonow_add: (date and datetime type) bool, default true. Whether to automatically set the field to now when the record is saved for the first time or not
	 *
	 * @return void
	 */
	public function setSpecialFields($sfields) {
		$this->_sfields = $sfields;
		foreach($this->_sfields as $k=>$v) {
			if($v['type']=='multicheck') {
				$fkts = $this->_registry->db->getTableStructure($v['table']);
				$this->_sfields[$k]['key'] = $fkts['primary_key'];
			}
		}
	}
	
	/**
	 * @brief Sets plugin fields 
	 * 
	 * @param array $pfields associative array where the key is the name of the "plugin type" field and the value is an array which contains the options required by the plugin itself 
	 *
	 * @return void
	 */
	public function setPluginFields($pfields) {

		foreach($pfields as $k=>$v) {
			if(!isset($this->_registry->plugins[$v['plugin']]))
				exit(error::syserrorMessage(get_class($this), 'setPluginFields', sprintf(__("cantFindPlugin"), $v['plugin']), __LINE__));
		}
		$this->_pfields = $pfields;

	}

	/**
	 * @brief Sets the tables which have such model has foreign 
	 *
	 * When deleting records of the table cheks aer done in order to test if the given record is present as a foreign key in other 
	 * tables. SO that if it is, deletion is avoided.
	 * 
	 * @param array $is_foreign 
	 *   Associative array in the form 'tbl_name'=>properties. Properties is an associative array having keys:
	 *   - **hide_where**: whether or not to hide which related records have it as a foreign.
	 *   - **this_model**: this model which is a foreign for another model
	 *   - **label**: the label of the model which uses this model as foreign key 
	 *   - **field_name**: the field_name of the foreign key which refers to this model 
	 *   - **model**: the model which has a foreign key over this model 
	 *
	 * @return void
	 */
	public function setIsForeign($is_foreign) {
		if(!$this->_model) {
			error::syserrorMessage('adminTable', 'setIsForeign', __('modelUnsetAdminTable'), __LINE__);
		}
		$this->_is_foreign = $is_foreign;
	}

	/**
	 * @brief Sets the form fieldsets 
	 * 
	 * @param array $fieldsets 
	 *   Associative array in the form 'first_field'=>legend
	 *
	 * @return void
	 */
	public function setFieldsets($fieldsets) {

		$this->_fieldsets = $fieldsets;

	}


	/**
	 * @brief Sets the $_changelist_field property 
	 * 
	 * @param array $fields See @ref adminTable::__construct() options
	 *
	 * @return void
	 */
	public function setChangelistFields($fields) {
		
		if(is_array($fields))
			$this->_changelist_fields = $fields;

	}
	
	/**
	 * @brief Sets the filter fields 
	 * 
	 * @param array $filter_fields Array of fields used to filter records in the admin list
	 *
	 * @return void
	 */
	public function setFilterFields($filter_fields) {
		
		$this->_filter_fields = $filter_fields;

	}

	/**
	 * @brief Sets the table html fields 
	 * 
	 * @param mixed $fields array of fields which allow html content 
	 *
	 * @return void
	 */
	public function setHtmlFields($fields) {
		
		if(is_array($fields))
			$this->_html_fields = $fields;

	}

	/**
	 * @brief Entry point for the autogenerated backoffice gui
	 *
	 * This is the method called by client classes to generate their tables backoffice 
	 * 
	 * @return the requested view or action
	 */
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

	/**
	 * @brief Returns the records to display and the pagination object 
	 * 
	 * @return an array where the first element is the pagination object and the second the array of the records selected from the table
	 */
	protected function viewRecords() {

		$order = cleanInput('get', 'order', 'string');

		$tot_ff = count($this->_filter_fields);

		// get order field and direction
		preg_match("#^([^ ,]*)\s?((ASC)|(DESC))?.*$#", $order, $matches);
		$field_order = isset($matches[1]) ? $matches[1] : null;
		$order_dir = isset($matches[2]) ? $matches[2] : null;

		$where_pag = $this->setWhereClause(false);
		$pag = new pagination($this->_efp, $this->_registry->db->getNumRecords($this->_table, $where_pag, $this->_primary_key));
		$limit = array($pag->start(), $this->_efp);

		if(count($this->_changelist_fields)) {
			if(!in_array($this->_primary_key, $this->_changelist_fields)) { 
				// always select the primary key
				array_unshift($this->_changelist_fields, $this->_primary_key);
			}
			$field_selection = isset($this->_fkeys[$field_order]) 
					? 'a.'.implode(', a.', $this->_changelist_fields)
					: implode(',', $this->_changelist_fields);
		}
		else 
			$field_selection = isset($this->_fkeys[$field_order]) ? "a.*" : "*"; 

		$where = $this->setWhereClause(isset($this->_fkeys[$field_order]));

		// different queries if the order field is a foreign key
		if(isset($this->_fkeys[$field_order])) {
			$records = $this->_registry->db->autoSelect($field_selection, array($this->_table." AS a", $this->_fkeys[$field_order]['table']." AS b"), ($where ? $where." AND " : "")."a.$field_order=b.".$this->_fkeys[$field_order]['key'], "b.".$this->_fkeys[$field_order]['order']." $order_dir", $limit);
		}
		else 
			$records = $this->_registry->db->autoSelect($field_selection, $this->_table, $where, $order, $limit);

		return array($pag, $records, $where);

	}

	/**
	 * @brief Returns the table of the records to be displayed 
	 * 
	 * @param array $fields_names the fields to be displayed 
	 * @return the table
	 */
	protected function viewTable($fields_names, $records) {

		$order = cleanInput('get', 'order', 'string');

		$tot_fk = count($this->_fkeys);
		$tot_sf = count($this->_sfields);
		$tot_pf = count($this->_pfields);
		$tot_ff = count($this->_filter_fields);

    $toggle = "<span class=\"uncheck_all_toggle\" onclick=\"toggleAllChecks($('atbl_form'), this)\"></span>";
		$heads = ($this->_edit_deny != 'all' || $this->_export) ? array("0"=>$toggle) : array();
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

        $label = isset($this->_fields_labels[$fn]['label']) ? $this->_fields_labels[$fn]['label'] : __($fn);

				$head_t = anchor($link."?order=$ord", $label, array('over'=>$jsover, 'out'=>$jsout));
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

			$pk = $r[$this->_primary_key];
			if(!in_array($this->_primary_key, $fields_names)) {
				// remove primary key
				array_shift($r);
			}
			if($this->_edit_deny=='all' && !$this->_export) $rows[] = $r;
			elseif(is_array($this->_edit_deny) && in_array($pk, $this->_edit_deny)) $rows[] = array_merge(array(""), $r);
			else $rows[] = array_merge(array($input), $r);
		}
		

		$this->_view->setTpl('table');
		$this->_view->assign('class', 'generic wide');
		$this->_view->assign('caption', __("RecordInTable")." ".$this->_table);
		$this->_view->assign('heads', $heads);
		$this->_view->assign('rows', $rows);

		return $this->_view->render();

	}

	/**
	 * @brief The method which returns the admin list view 
	 * 
	 * @return the admin list view
	 */
	public function view() {

		$order = cleanInput('get', 'order', 'string');

		$tot_ff = count($this->_filter_fields);

		if($tot_ff) $this->setSessionSearch();

		$fields_names = $this->_changelist_fields ? $this->_changelist_fields : $this->_registry->db->getFieldsName($this->_table);

		list($pag, $records, $where) = $this->viewRecords();

		$table = $this->viewTable($fields_names, $records);
		
		if($this->_edit_deny!='all' || $this->_export) {
			$myform = new form('post', 'atbl_form', array("validation"=>false));
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
			$input_where_query = $myform->hidden('where_query', $where);
		
		}
		else {
			$input_export_selected = null;
			$input_export_all = null;
			$input_where_query = '';	
		}

		$link_insert = $this->_insertion ? anchor("?insert", __("insertNewRecord"), array('class'=>'submit')) : null;
		

		if(isset($this->_custom_tpl['view'])) {
			$tpl_name = $this->_custom_tpl['view'];
		}
		elseif($tot_ff) {
			$tpl_name = 'admin_table_filter';
			$this->_view->assign('form_filters_title', __("Filters"));
			$this->_view->assign('form_filters', $this->formFilters());
		}
		else {
			$tpl_name = 'admin_table';
		}

		$this->_view->setTpl($tpl_name);
		$this->_view->assign('backoffice_text', $this->_backoffice_text);
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

	/**
	 * @brief Sets the session variables used to filter records in the admin list 
	 * 
	 * @return void
	 */
	protected function setSessionSearch() {

		foreach($this->_filter_fields as $fname) {

			if(!isset($_SESSION[$this->_table.'_'.$fname.'_filter'])) $_SESSION[$this->_table.'_'.$fname.'_filter'] = null;

		}

		if(isset($_POST['ats_submit'])) {

			foreach($this->_filter_fields as $fname) {

				$type = $this->_fields[$fname]['type'];

				if(isset($_POST[$fname.'_filter'])) {
					if(isset($this->_sfields[$fname]) && $this->_sfields[$fname]['type'] == 'multicheck') {
						$_SESSION[$this->_table.'_'.$fname.'_filter'] = $this->cleanField($fname."_filter", 'array');
					}
					elseif($type=='int' || $type=='float') {
						if($_POST[$fname.'_filter']==='') {
							$_SESSION[$this->_table.'_'.$fname.'_filter'] = null;
						}
						else {
							$_SESSION[$this->_table.'_'.$fname.'_filter'] = $this->cleanField($fname."_filter", $type);
						}
					}
					else {
						$_SESSION[$this->_table.'_'.$fname.'_filter'] = $this->cleanField($fname."_filter", $type, array("escape"=>false));
					}
				
				}
				else {
					$_SESSION[$this->_table.'_'.$fname.'_filter'] = null; 
				}
			}
			
		}

	}
	
	/**
	 * @brief Sets the where clause used to filter records in the admin list view 
	 * 
	 * @param bool $fkeysorder Whether records are ordered by a foreign key field or not
	 *
	 * @return the where clause
	 */
	protected function setWhereClause($fkeysorder) {

		$where_a = array();
		$prefix = $fkeysorder ? "a." : "";

		foreach($this->_filter_fields as $fname) {
			if(isset($this->_sfields[$fname]) && $this->_sfields[$fname]['type'] == 'multicheck') {
				$value = $_SESSION[$this->_table.'_'.$fname.'_filter'];
				$where_part = array();
				if($value) {
					foreach($value as $v) {
						$where_part[] = $prefix.$fname." REGEXP '[[:<:]]".$v."[[:>:]]'";

					}
					$where_a[] = "(".implode(' OR ', $where_part).")";
				}
			}
			elseif($this->_fields[$fname]['type']=='varchar' || $this->_fields[$fname]['type']=='text') {
				if(isset($_SESSION[$this->_table.'_'.$fname.'_filter']) && $_SESSION[$this->_table.'_'.$fname.'_filter']) {
					$value = $_SESSION[$this->_table.'_'.$fname.'_filter'];
					if(preg_match("#^\"([^\"]*)\"$#", $value, $matches))
						$where_a[] = $prefix.$fname."='".$matches[1]."'"; 
					elseif(preg_match("#^\"([^\"]*)$#", $value, $matches))
						$where_a[] = $prefix.$fname." LIKE '".$matches[1]."%'"; 
					else
						$where_a[] = $prefix.$fname." LIKE '%".$value."%'"; 
				}
			}
			else {
				if(isset($_SESSION[$this->_table.'_'.$fname.'_filter']) && !is_null($_SESSION[$this->_table.'_'.$fname.'_filter'])) {
					$value = $_SESSION[$this->_table.'_'.$fname.'_filter'];
					$where_a[] = $prefix.$fname."='".$value."'";
				}
			}
		}

		return count($where_a) ? implode(" AND ", $where_a) : null;

	}

	/**
	 * @brief Form which contains filters in the admin list view 
	 * 
	 * @return the filters html form
	 */
	protected function formFilters() {

		$myform = new form('post', 'atbl_filter_form', array("validation"=>false));
		$myform->load();

		$form = $myform->sform('', null);

		foreach($this->_filter_fields as $fname) {
			$field = $this->_fields[$fname];
			$field['null'] = '';
			if(isset($this->_sfields[$fname]) && $this->_sfields[$fname]['type'] == 'multicheck') {
				if($_SESSION[$this->_table.'_'.$fname.'_filter']) {
					$value = implode(',', $_SESSION[$this->_table.'_'.$fname.'_filter']); 
				}
				else {
					$value = '';
				}
			}
			else {
				$value = htmlInput($_SESSION[$this->_table.'_'.$fname.'_filter']);
			}
			$form .= $this->formElement($myform, $fname, $field, 'filter', array("size"=>20, "value"=>$value));
		}

		$onclick = "onclick=\"$$('#atbl_filter_form *[name*=_filter]').each(function(el) { 
			if(el.get('type')==='text') el.value='';
			else if(el.get('type')==='radio') el.removeProperty('checked');
			else if(el.get('type')==='checkbox') el.removeProperty('checked');
			else if(el.get('tag')=='select') el.getChildren('option').removeProperty('selected');
			});
			\"";

		$input_reset = $myform->input('ats_reset', 'button', __("reset"), array("js"=>$onclick)); 
		$form .= $myform->cinput('ats_submit', 'submit', __("filter"), '', array("text_add"=>' '.$input_reset)); 
		$form .= $myform->cform();

		return $form;

	}

	/**
	 * @brief Parses the results from a database query substituting the foreign keys values with the related fields values of the related table 
	 * 
	 * @param array $row associative array deriving from a db query result
	 *
	 * @return void
	 */
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

	/**
	 * @brief Parses the results from a database query substituting the special fields values with the way they have to be displayed 
	 * 
	 * @param mixed $row associative array deriving from a db query result
	 * @param mixed $opts 
	 *   Associative array of options: 
	 *   - **show_pwd**: bool default false: Whether to show a clear-text password or not
	 *   - **mailto**: bool default false: Whether to ass a mailto link or not
	 *
	 * @return void
	 */
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
				elseif($this->_sfields[$k]['type']=='enum') {
					$res[$k] = $v ? $this->_sfields[$k]['data'][$v] : '';
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
					if(isset($sf['preview']) && $sf['preview'] && $v && gOpt($opts, 'preview', true)) {
						if($this->_sfields[$k]['type']=='image') {
							$res[$k] = "<a title=\"$v\" href=\"".$sf['rel_path']."/$v\">".$v."</span><script>$$('a[href=".$sf['rel_path']."/$v]')[0].cerabox();</script>";
						}
						else {
							$res[$k] = "<a title=\"$v\" href=\"".$sf['rel_path']."/$v\">".$v."</span>";
						}
					}
					else $res[$k] = $v;
				}
				else {
					$res[$k] = $v;
				}
			}
			else $res[$k] = $v;
		}

		return $res;

	}

	/**
	 * @brief Parses the results from a database query substituting the plugin fields values with the way they have to be displayed 
	 * 
	 * @param mixed $row associative array deriving from a db query result 
	 *
	 * @return void
	 */
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
	
	/**
	 * @brief Parses the results from a database query substituting the date/datetime fields values with format set in the site configuration 
	 * 
	 * @param mixed $row associative array deriving from a db query result 
	 *
	 * @return void
	 */
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

	/**
	 * @brief Checks if at least one of the special fields requires a file upload 
	 * 
	 * @return bool, true if there's at least an image or file special field set.
	 */
	protected function checkUpload() {
		
		foreach($this->_sfields as $fname=>$finfo) if($finfo['type']=='file' || $finfo['type']=='image') return true;
		return false;

	}

	/**
	 * @brief The method which returns the admin insertion/modification/deletion/export views and functionality 
	 * 
	 * @param mixed $opts 
	 *   associative array of options
	 *   - **insert**: bool default null. Force a record insertion 
	 *   - **action**: string default '?save'. The url of the form action
	 *   - **f_s**: array default null. Array containing the identifiers of the records that have to be managed. By default are taken from POST or SESSION.
	 *
	 * @return string the requested view (insertion form, modification form) or the requested action (export, deletion)
	 */
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
		if($submit_delete) $this->deleteFields($f_s);

		$myform = new form('post', 'atbl_form', array("validation"=>true));
		$myform->load();

    $buffer = '';

    if($this->_backoffice_form_text) {
      $buffer .= $this->_backoffice_form_text;
    }

		$buffer .= $myform->sform($formaction, null, array("upload"=>$this->checkUpload()));
		$buffer .= $myform->hidden('order', $order);

		if($insert) {
			$buffer .= $this->formRecord(null, $myform);
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
		if($this->_save_and_continue) {
			$buffer .= "&#160;".$myform->input('submit_c_'.($insert ? "insert" : "modify"), 'submit', __('saveContinueEditing'), array());
		}

		$buffer .= $myform->cform();
		
		if($this->_editor) $buffer .= chargeEditor("#atbl_form div[class=html]");

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

	/**
	 * @brief Tries to delete the given records from the table  
	 * 
	 * @param array $f_s Array containing the identifiers of the records that have to be deleted
	 * @return void
	 */
	protected function deleteFields($f_s) {

		if(!$this->_deletion || $this->_edit_deny=='all') header("Location: ".preg_replace("#\?.*$#", "", $_SERVER['REQUEST_URI']));
		if(count($f_s)) {
			if($this->_cls_cbk_del && $this->_mth_cbk_del) {
				call_user_func(array($this->_cls_cbk_del,$this->_mth_cbk_del), $this->_registry, $f_s);
			}
			else {
				if(is_array($this->_edit_deny) && count($this->_edit_deny)) $f_s = array_diff($f_s, $this->_edit_deny);
				if(count($this->_is_foreign)) {
					$wngs = 0;
					$wngs_exp = array();
					foreach($this->_is_foreign as $tbl=>$prop) {
						$where = $prop['field_name']."='".implode("' OR ".$prop['field_name']."='", $f_s)."'";
						$selection = array('id', $prop['field_name']);
						$res = $this->_registry->db->autoSelect($selection, $tbl, $where, null, null);
	
						if(count($res)) {
							$wngs++;
							$wng_exp = array();
							if(!gOpt($prop, 'hide_where', false)) {
								foreach($res as $row) {
									$model_id = $row['id'];
									$field_name_value = $row[$prop['field_name']];
									$this_model = new $this->_model($field_name_value);
									$model = new $prop['model']($model_id);
									$wng_exp[] = sprintf(__('warningForeignPresentInWithReference'), jsVar((string) $this_model), jsVar((string) $model)); 
								}		
							}
							else {
								$wng_exp[] = __('tooManyValuesToDisplay');
							}
							$wngs_exp[$prop['label']] = $wng_exp;
						}
					}
					if($wngs) {
						$warning = '\n'.__('cantDeleteRecordsForeign').'\n';
						foreach($wngs_exp as $lb=>$ws) {
							$warning .= '\n'.jsVar($lb).'\n';
							foreach($ws as $w) {
								$warning .= ' - '.$w.'\n';
							}
						}
						exit(error::warningMessage(array('warning'=>$warning), preg_replace("#\?.*$#", $order_param, $_SERVER['REQUEST_URI'])));
					}
				}
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

	/**
	 * @brief Checks if the given records are associated to files and deletes them.  
	 * 
	 * @param array $f_s Array containing the identifiers of the records that have to be deleted
	 *
	 * @return int 0 if there aren't special fields or 1
	 */
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

		return 1;

	}

	/**
	 * @brief The insertion/modification form of the given record 
	 *
	 * This method can return all the form elements without the start and end form tags, or a complete form.<br />
	 * The complete form is generally used when calling this method outside from the adminTable class. In this case it is possible to set 
	 * the form action which by default is '?save'
	 * 
	 * @param mixed $pk the value of the record primary key 
	 * @param form $myform a form instance default null. If the form is started and closed independently by this method.
	 * @param string $formaction default null. The url of the form action if the method starts the form
	 *
	 * @return void
	 */
	public function formRecord($pk, $myform=null, $formaction=null) {

		$buffer = '';
		if(!$myform) {
			if(!$formaction) $formaction = '?save';
			$myform = new form('post', 'atbl_form', array("validation"=>true));
			$myform->load();
			$buffer .= $myform->sform($formaction, null, array("upload"=>$this->checkUpload()));
		}	

		$buffer .= $myform->hidden($this->_primary_key."[]", $pk);
		$open_fieldset = false;
		foreach($this->_fields as $fname=>$field) {
			if($fname != $this->_primary_key && $field['extra']!='auto_increment') { 
				if(array_key_exists($fname, $this->_fieldsets)) {
					if($open_fieldset) {
						$buffer .= "</fieldset>\n";
					}
					$open_fieldset = true;
					$buffer .= "<fieldset class=\"inside_record\">\n";
					$buffer .= "<legend>".htmlVar($this->_fieldsets[$fname])."</legend>\n";
				}
				$buffer .= $this->formElement($myform, $fname, $field, $pk);
			}
		}

		if($open_fieldset) {
			$buffer .= "</fieldset>\n";
		}

		if(!$myform) $buffer .= $myform->cform();

		return $buffer;

	}

	/**
	 * @brief The html form element for the given field 
	 * 
	 * @param form $myform the form instance
	 * @param mixed $fname the field name 
	 * @param array $field associative array containing the database structure information for the field (null, max_length, int...)  
	 * @param mixed $id the value of the primary key
	 * @param mixed $opts  
	 *   Associative array of options
	 *   - **value**: mixed. The field value 
	 *   - **size**: int default 40. The size attribute of the input element 
	 *
	 * @return the html form element
	 */
	protected function formElement($myform, $fname, $field, $id, $opts=null) {

		if(in_array($fname, $this->_no_form_fields)) return null;
	
		$id_f = preg_replace("#\s#", "_", $id); // replace spaces with '_' in form names as POST do itself

		$required = $field['null']=='NO' ? true : false;

		if(isset($opts['value'])) {
			$value = gOpt($opts, 'value', '');
		}
		else {
			$records = $this->_registry->db->autoSelect("*", $this->_table, $this->_primary_key."='$id'", null);
			$value = count($records) ? $records[0][$fname] : null;
		}

    $label = isset($this->_fields_labels[$fname]['label']) ? htmlVar($this->_fields_labels[$fname]['label']) : htmlVar(__($fname));
    $helptext = isset($this->_fields_labels[$fname]['helptext']) ? htmlVar($this->_fields_labels[$fname]['helptext']) : htmlVar('');

		if(array_key_exists($fname, $this->_sfields)) {
			if($this->_sfields[$fname]['type']=='password') { 
				$req = $id ? false : true;
				return $myform->cinput($fname."_".$id_f, 'password', '', array($label, $helptext), array("required"=>$req, "size"=>gOpt($opts, 'size', 40), "maxlength"=>$field['max_length']));
			}
			elseif($this->_sfields[$fname]['type']=='bool') {
				$t_l = 	$this->_sfields[$fname]['true_label'];
				$f_l = 	$this->_sfields[$fname]['false_label'];
				$dft = 	isset($this->_sfields[$fname]['default']) ? $this->_sfields[$fname]['default'] : 0;
				return $myform->cradio($fname."_".$id_f, $myform->retvar($fname."_".$id_f, $value), array(1=>$t_l,0=>$f_l), $dft, array($label, $helptext), array("required"=>$required));
			}
			elseif($this->_sfields[$fname]['type']=='enum') {
				return $myform->cselect($fname."_".$id_f, $myform->retvar($fname."_".$id_f, $value), $this->_sfields[$fname]['data'], array($label, $helptext), array("required"=>$required));
			}
			elseif($this->_sfields[$fname]['type']=='constant') {
				return $myform->hidden($fname."_".$id_f, $this->_sfields[$fname]['value']);
			}
			elseif($this->_sfields[$fname]['type']=='date' || $this->_sfields[$fname]['type']=='datetime') {
				return $myform->hidden($fname."_".$id_f, '');
			}
			elseif($this->_sfields[$fname]['type']=='email') {
				return $myform->cinput($fname."_".$id_f, 'email', $myform->retvar($fname."_".$id_f, $value), array($label, $helptext), array("required"=>$required)); 
			}
			elseif($this->_sfields[$fname]['type']=='multicheck') {
				$sf = $this->_sfields[$fname];
				$options = $this->_registry->db->autoSelect(array($sf['key']." AS value", $sf['field']." AS label"), $sf['table'], $sf['where'], $sf['order']);
				return $myform->cmulticheckbox($fname."_".$id_f."[]", $myform->retvar($fname."_".$id_f, explode(",", $value)), $options, array($label, $helptext), array("required"=>$required));
			}
			elseif($this->_sfields[$fname]['type']=='file' || $this->_sfields[$fname]['type']=='image') {
				$sf = $this->_sfields[$fname];
				$preview = isset($sf['preview']) ? $sf['preview'] : false;
				$rel_path = $sf['rel_path'];
				return $myform->cinput_file($fname."_".$id_f, $myform->retvar($fname, $value), array($label, $helptext), array("required"=>$required, "extensions"=>$sf['extensions'], "preview"=>$preview, "rel_path"=>$rel_path));
			}
		}
		elseif(array_key_exists($fname, $this->_fkeys)) {
			$fk = $this->_fkeys[$fname];
			$options = $this->_registry->db->autoSelect(array($fk['key'], $fk['field']), $fk['table'], $fk['where'], $fk['order']);
			$data = array();
			foreach($options as $rec) 
				$data[htmlInput($rec[$fk['key']])] = htmlVar($rec[$fk['field']]);
			return $myform->cselect($fname."_".$id_f, $myform->retvar($fname."_".$id_f, $value), $data, array($label, $helptext), array("required"=>$required));
		}
		elseif(array_key_exists($fname, $this->_pfields)) {
			return $this->_registry->plugins[$this->_pfields[$fname]['plugin']]->formAdmin($this->_pfields[$fname], $fname."_".$id_f, $fname, $field, $myform, $myform->retvar($fname."_".$id_f, $value));
		}
		elseif($field['type'] == 'int') {
			return $myform->cinput($fname."_".$id_f, 'text', $myform->retvar($fname, $value), array($label, $helptext), array("required"=>$required, "size"=>$field['n_int'], "maxlength"=>$field['n_int']));
		}
		elseif($field['type'] == 'float' || $field['type'] == 'double' || $field['type'] == 'decimal') {
			return $myform->cinput($fname."_".$id_f, 'text', $myform->retvar($fname."_".$id_f, $value), array($label, $helptext), array("required"=>$required, "size"=>($field['n_int']+1+$field['n_precision']), "maxlength"=>($field['n_int']+1+$field['n_precision'])));
		}
		elseif($field['type'] == 'varchar') {
			$size = gOpt($opts, 'size', null) ? gOpt($opts, 'size') : ($field['max_length']<40 ? $field['max_length'] : 40);
			return $myform->cinput($fname."_".$id_f, 'text', $myform->retvar($fname."_".$id_f, $value), array($label, $helptext), array("required"=>$required, "size"=>$size, "maxlength"=>$field['max_length']));
		}
		elseif($field['type'] == 'text') {
                	return $myform->ctextarea($fname."_".$id_f, $myform->retvar($fname."_".$id_f, $value), array($label, $helptext), array("required"=>$required, "cols"=>45, "rows"=>6, "editor"=>(in_array($fname, $this->_html_fields) && $this->_editor)  ? true : false));
		}
		elseif($field['type'] == 'date') {
                	return $myform->cinput_date($fname."_".$id_f, $myform->retvar($fname."_".$id_f, $value), array($label, $helptext), array("required"=>$required));
		}
		elseif($field['type'] == 'datetime') {
			return $myform->cinput_datetime($fname."_".$id_f, $myform->retvar($fname."_".$id_f, $value), array($label, $helptext), array("required"=>$required));
		}

	}

	/**
	 * @brief Saves the form submitted data (one or more records) 
	 * 
	 * @param string $model_name the class name of the model (if given is used instead of the parent object model)
	 *
	 * @return void
	 */
	public function saveFields($model_name = null) {

		$myform = new form('post', 'atbl_form', array("validation"=>false));
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
				$res[] = $this->saveRecord($pk, $pkeys, $model_name);
			}
		}

		return $res;

	}

	/**
	 * @brief Saves the given record 
	 * 
	 * @param mixed $pk the primary key value
	 * @param mixed $pkeys List of primary keys of the records edited in the form 
	 * @param string $model_name the class name of the model (if given is used instead of the parent object model)
	 *
	 * @return void
	 */
	protected function saveRecord($pk, $pkeys, $model_name = null) {

		if(!in_array($pk, $this->_edit_deny)) {
			$res = array();
			if(is_null($pk)) {
				$pkf = $pk;
				$insert = true;
			}
			else {
				$pkf = preg_replace("#\s#", "_", $pk); // POST replaces spaces with '_'
				$insert = false;
			}

			if(is_null($model_name)) {
				$model = new model($pk, $this->_table);
				$model->setIdName($this->_primary_key);
			}
			else {
				$model = new $model_name($pk, $this->_table);
			}

			$structure = $this->_registry->db->getTableStructure($this->_table);

			foreach($this->_fields as $fname=>$field) { 

				if(array_key_exists($fname, $this->_sfields)) { 
					$this->cleanSpecialField($model, $fname, $pkf, $field['type'], $insert);
				}
				elseif(array_key_exists($fname, $this->_pfields)) { 					
					$this->_registry->plugins[$this->_pfields[$fname]['plugin']]->cleanField($this->_pfields[$fname], $model, $fname, $pkf, $insert);
				}
				elseif(isset($_POST[$fname."_".$pkf]) && ($fname != $this->_primary_key || is_null($pk)) && $field['extra']!='auto_increment' && in_array($fname, $this->_html_fields)) { 
					$model->{$fname} = $this->cleanField($fname."_".$pkf, 'html');
				}
				elseif(isset($_POST[$fname."_".$pkf]) && ($fname != $this->_primary_key || is_null($pk)) && $field['extra']!='auto_increment') {
					$model->{$fname} = $this->cleanField($fname."_".$pkf, $field['type']);
				}
			}
			
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

	/**
	 * @brief Cleans inputs from the user
	 * 
	 * @param string $name the field name 
	 * @param string $type the field data type 
	 * @param mixed $opts 
	 *   Associative array of options
	 *   - **escape** bool default true. Whether to escape values or not.
	 *
	 * @return cleaned input
	 */
	protected function cleanField($name, $type, $opts=null) {

		if(isset($opts['escape'])) {
			$options = array("escape"=>gOpt($opts, 'escape', true));
		}
		else {
			$options = array();
		}
	
		if($type=='array') return cleanInputArray('post', $name, 'int');
		elseif($type=='int') return cleanInput('post', $name, 'int');
		elseif($type=='float' || $type=='double' || $type=='decimal') return cleanInput('post', $name, 'float');
		elseif($type=='varchar' || $type=='text') return cleanInput('post', $name, 'string', $options);
		elseif($type=='html') return cleanInput('post', $name, 'html', $options);
		elseif($type=='date') return cleanInput('post', $name, 'date');
		elseif($type=='datetime') return cleanInput('post', $name, 'datetime');

	}

	/**
	 * @brief Cleans special fields inputs from the user and sets the model properties 
	 * 
	 * @param mixed $model the model instance
	 * @param string $fname the field name
	 * @param mixed $pk the primary key value
	 * @param string $type the field data type
	 * @param bool $insert whether the user action is an insertion or not. 
	 *
	 * @return void
	 */
	protected function cleanSpecialField($model, $fname, $pk, $type, $insert) {
		
		if($this->_sfields[$fname]['type']=='password') {
			if(!$insert && !cleanInput('post', $fname.'_'.$pk, 'string')) return 0;

			if(PWD_HASH=='md5') $model->{$fname} = md5(cleanInput('post', $fname.'_'.$pk, 'string'));	
			elseif(PWD_HASH=='sha1') $model->{$fname} = sha1(cleanInput('post', $fname.'_'.$pk, 'string'));	
			else $model->{$fname} = cleanInput('post', $fname.'_'.$pk, 'string');	
		}
		elseif($this->_sfields[$fname]['type']=='bool') $model->{$fname} = cleanInput('post', $fname.'_'.$pk, 'int');
		elseif($this->_sfields[$fname]['type']=='constant') $model->{$fname} = cleanInput('post', $fname.'_'.$pk, $this->_sfields[$fname]['value_type']);
		elseif($this->_sfields[$fname]['type']=='enum') $model->{$fname} = cleanInput('post', $fname.'_'.$pk, $this->_sfields[$fname]['key_type']);
		elseif($this->_sfields[$fname]['type']=='date' || $this->_sfields[$fname]['type']=='datetime') {
			if($this->_sfields[$fname]['autonow'] || ($this->_sfields[$fname]['autonow_add'] && !$model->{$this->_primary_key})) {
				$model->{$fname} = $this->_sfields[$fname]['type']=='date' ? date("Y-m-d") : date("Y-m-d H:i:s");
			}
		}
		elseif($this->_sfields[$fname]['type']=='email') $model->{$fname} = cleanInput('post', $fname.'_'.$pk, 'email', $options);
		elseif($this->_sfields[$fname]['type']=='multicheck') {
			$checked = cleanInputArray('post', $fname.'_'.$pk, $this->_sfields[$fname]['value_type']);
			$model->{$fname} = implode(",", $checked);
		}
		elseif($this->_sfields[$fname]['type']=='file') {
			$link_error = preg_replace("#\?.*$#", "", $_SERVER['REQUEST_URI']);
			$sf = $this->_sfields[$fname];
			$opts['check_content'] = isset($sf['check_content']) ? $sf['check_content'] : true;
			$opts['contents'] = isset($sf['contents_allowed']) ? $sf['contents_allowed'] : null;
			$myform = new form('post', 'atbl_form', array("validation"=>false));
			$model->{$fname} = $myform->uploadFile($fname.'_'.$pk, $sf['extensions'], $sf['path'], $link_error, $opts);
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
			$myform = new form('post', 'atbl_form', array("validation"=>false));
			$model->{$fname} = $myform->uploadImage($fname.'_'.$pk, $sf['extensions'], $sf['path'], $link_error, $opts);
		}

	}

	/**
	 * @brief Exports the given records 
	 * 
	 * @param mixed $f_s the id of the fields that have to be exported. Possible values are 'all' or an array of fields' id 
	 * @param string $where the where clause used to select records if the $f_s parameter is null 
	 *
	 * @return void
	 */
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

		$expObj = new export(array("table"=>$this->_table, "pkey"=>$this->_primary_key, "sfields"=>$this->_sfields, "fkeys"=>$this->_fkeys, 'flabels'=>$this->_fields_labels));
		$expObj->setRids($rids);

		$expObj->exportData($this->_table.'_'.$this->_registry->dtime->now('%Y%m%d').'.csv', 'csv');

		exit();
	
	}

}

?>
