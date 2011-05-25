<?php

class form {

	private $_registry, $_view;
	private $_method, $_name, $_validation;

	function __construct($registry, $method, $name, $opts=null) {

		$this->_registry = $registry;
		$this->_method = $method;	
		$this->_name = $name;

		$this->_view = new view($registry);

		$this->_validation = gOpt($opts, 'validation', false);

		if(gOpt($opts, 'verifyToken'))
			if(!$this->verifyFormToken($this->_name)) 
				exit(error::syserrorMessage("form", "construct", __("CSRFDetectError")));
		
		$this->_requestVars = $this->_method == 'post' ? $_POST : ($this->_method == 'get' ? $_GET : $_REQUEST);	
	
	}
	
	private function generateFormToken() {
  		$token = md5(uniqid(microtime(), true));
  		$_SESSION[$this->_name.'_token'] = $token;
  		return $token;
	}

	private function verifyFormToken() {
  		$index = $this->_name.'_token';
		// There must be a token in the session
  		if (!isset($_SESSION[$index])) return false;
  		// There must be a token in the form
  		if (!isset($_POST['token'])) return false;
  		// The token must be identical
  		if ($_SESSION[$index] !== $_POST['token']) return false;
  		return true;
	}

	public function load() {
		
		$this->_registry->fvars = array();
		$vars = array();

		if(isset($_SESSION["formvars_".$this->_name])) {
			if(isset($_SESSION['ERRORMSG']) AND !empty($_SESSION['ERRORMSG'])) 
				foreach($_SESSION['formvars_'.$this->_name] as $k=>$v)
						$vars[$k] = $v;
			$this->_registry->fvars = $vars;

			unset($_SESSION['formvars_'.$this->_name]);
		}

	}

	public function save() {
		
		$_SESSION["formvars_".$this->_name] = array();
		foreach($this->_requestVars as $key => $value)
			$_SESSION["formvars_".$this->_name][$key] = $value;

	}

	public function retvar($name, $dft=null) {

		return isset($this->_registry->fvars[$name]) ? $this->_registry->fvars[$name] : $dft;

	}

	public function free() {
	
			unset($_SESSION['formvars_'.$this->_name]);

	}

	public function setRequired($required) {
		
		return !empty($required) ? $this->hidden('required', $required) : '';

	}

	public function checkRequired() {
		
		$error = false;
		$required = isset($this->_requestVars['required']) ? cleanInput($this->_method, 'required', 'string') : '';
		
		if(!empty($required))
			foreach(explode(",", $required) as $fieldname)
				if($this->_requestVars[trim($fieldname)]=='') $error = true;
		return $error;

	}

	public function sform($action, $required, $opts=null) {
	
		$buffer = "<form name=\"$this->_name\" id=\"".$this->_name."\" method=\"$this->_method\" action=\"$action\"";
		if(gOpt($opts, 'upload')) $buffer .= " enctype=\"multipart/form-data\"";
		if($this->_validation) $buffer .= " onsubmit=\"return validateForm($(this))\"";
		$buffer .= ">\n";
		if(gOpt($opts, 'generateToken')) 
			$buffer .= $this->hidden('token', $this->generateFormToken());

		$buffer .= $this->setRequired($required);

		return $buffer;
	}

	public function cform() {

		return "</form>";

	}
	
	public function fieldset($legend, $content, $opts=null) {

		$this->_view->setTpl('form_fieldset');
		$this->_view->assign('id', gOpt($opts, "id"));
		$this->_view->assign('legend', $legend);
		$this->_view->assign('content', $content);

		return $this->_view->render();
	}

	public function label($text){

		if(!$text) return array(null, null);
		if(is_array($text) && count($text)==2) {
			$label = isset($text['label']) ? $text['label'] : $text[0];
			$description = isset($text['description']) ? $text['description'] : $text[1];
		}
		else {$label = $text; $description = null;}
		
		return array($label, $description);
	}

	public function hidden($name, $value, $opts=null) {

		$buffer = "<input type=\"hidden\" name=\"$name\" value=\"$value\" ".(gOpt($opts, 'id')?"id=\"".gOpt($opts, 'id')."\"":"")."/>\n";

		return $buffer;
	}

	private function prepareView($name, $l, $d, $req, $tadd, $opts=null) {
		
		$this->_view->setTpl('form_element');
		$this->_view->assign('name', $name);
		$this->_view->assign('label', $l);
		if(gOpt($opts, 'label_class')) 
			$this->_view->assign('label_class', gOpt($opts, 'label_class'));
		$this->_view->assign('required', $req);
		$this->_view->assign('description', $d);
		$this->_view->assign('textadd', $tadd);
		$this->_view->assign('more', null);

	}

	public function freeInput($cleft, $cright, $opts=null) {
	
		$this->_view->setTpl('form_cell');
		$this->_view->assign('idleft', gOpt($opts, 'idleft') ? gOpt($opts, 'idleft'):null);
		$this->_view->assign('cleft', $cleft);
		$this->_view->assign('idright', gOpt($opts, 'idright') ? gOpt($opts, 'idright'):null);
		$this->_view->assign('cright', $cright);

		return $this->_view->render();
	}

	public function input($name, $type, $value, $opts=null){

		$buffer = "<input type=\"$type\" name=\"$name\" value=\"$value\" ";
		
		$buffer .= gOpt($opts, 'id') ? "id=\"".gOpt($opts, 'id')."\" ":"";
		$buffer .= gOpt($opts, 'class') ? "class=\"".gOpt($opts, 'class')."\" ":"";
		$buffer .= gOpt($opts, 'pattern') ? "pattern=\"".gOpt($opts, 'pattern')."\" ":"";
		$buffer .= gOpt($opts, 'hint') ? "placeholder=\"".gOpt($opts, 'hint')."\" ":"";
		$buffer .= gOpt($opts, 'size') ? "size=\"".gOpt($opts, 'size')."\" ":"";
		$buffer .= gOpt($opts, 'maxlength') ? "maxlength=\"".gOpt($opts, 'maxlength')."\" ":"";
		$buffer .= gOpt($opts, 'readonly') ? "readonly=\"".gOpt($opts, 'readonly')."\" ":"";
		$buffer .= gOpt($opts, 'js') ? gOpt($opts, 'js')." ":"";
		$buffer .= gOpt($opts, 'other') ? gOpt($opts, 'other')." ":"";
	
		$buffer .= " />";

		return $buffer;
	}
	
	public function cinput($name, $type, $value, $label, $opts){

		list($l, $d) = $this->label($label);
		$this->prepareView($name, $l, $d, gOpt($opts, 'required'), gOpt($opts, 'text_add'));
		$this->_view->assign('formfield', $this->input($name, $type, $value, $opts));
		
		return $this->_view->render();
	}
	
	public function cinput_date($name, $value, $label, $opts){

		$opts['size'] = 10;
		$opts['maxlength'] = 10;
		$opts['pattern'] = "^\d\d\d\d-\d\d-\d\d$";
		$opts['hint'] = "dd/mm/yyyy";

		$dpjs = "<script type=\"text/javascript\">";
		$dpjs .= "window.int_input_date_$name = setInterval(activateDatePicker$name, 100);";
		$dpjs .= "function activateDatePicker$name() {
			if(typeof $$('input[name=$name]')[0] != undefined) {
				clearInterval(window.int_input_date_$name);
				new DatePicker($$('input[name=$name]')[0], {
					pickerClass: 'datepicker_dashboard', 
					days: ['".__("Sunday")."', '".__("Monday")."', '".__("Tuesday")."', '".__("Wednesday")."', '".__("Thursday")."', '".__("Friday")."', '".__("Saturday")."'], 
					months:['".__("January")."', '".__("February")."', '".__("March")."', '".__("April")."', '".__("May")."', '".__("June")."', '".__("July")."', '".__("August")."', '".__("September")."', '".__("October")."', '".__("November")."', '".__("December")."'], 
					format:'d/m/Y', 
					inputOutputFormat:'Y-m-d', 
					startDay: 1, 
					allowEmpty: ".(gOpt($opts, 'init') ? "false" : "true")."});
			}
		}";
		$dpjs .= "</script>";

		list($l, $d) = $this->label($label);
		$this->prepareView($name, $l, $d, gOpt($opts, 'required'), gOpt($opts, 'text_add'));
		$this->_view->assign('formfield', $this->input($name, 'text', $value, $opts));
		$this->_view->assign('more', $dpjs);
		
		return $this->_view->render();
	}
	
	public function cinput_datetime($name, $value, $label, $opts){

		$opts['size'] = gOpt($opts, 'seconds')==true ? 19 : 16;
		$opts['maxlength'] = gOpt($opts, 'seconds')==true ? 19 : 16;
		$opts['pattern'] = "^\d\d\d\d-\d\d-\d\d \d\d:\d\d(:\d\d)?$";
		$opts['hint'] = "dd/mm/yyyy hh:mm";

		$dpjs = "<script type=\"text/javascript\">";
		$dpjs .= "window.int_input_datetime_$name = setInterval(activateDatetimePicker$name, 100);";
		$dpjs .= "function activateDatetimePicker$name() {
			if(typeof $$('input[name=$name]')[0] != undefined) {
				clearInterval(window.int_input_datetime_$name);
				new DatePicker($$('input[name=$name]'), {
					timePicker: true, 
					pickerClass: 'datepicker_dashboard', 
					days: ['".__("Sunday")."', '".__("Monday")."', '".__("Tuesday")."', '".__("Wednesday")."', '".__("Thursday")."', '".__("Friday")."', '".__("Saturday")."'], 
					months:['".__("January")."', '".__("February")."', '".__("March")."', '".__("April")."', '".__("May")."', '".__("June")."', '".__("July")."', '".__("August")."', '".__("September")."', '".__("October")."', '".__("November")."', '".__("December")."'], 
					format: 'd/m/Y H:i".(gOpt($opts, 'seconds')==true ? ":s":"")."', 
					inputOutputFormat:'Y-m-d H:i:s', 
					startDay: 1, 
					allowEmpty: ".(gOpt($opts, 'init') ? "false" : "true")."});
			}
		}";
		$dpjs .= "</script>";
		
		list($l, $d) = $this->label($label);
		$this->prepareView($name, $l, $d, gOpt($opts, 'required'), gOpt($opts, 'text_add'));
		$this->_view->assign('formfield', $this->input($name, 'text', $value, $opts));
		$this->_view->assign('more', $dpjs);
		
		return $this->_view->render();
	}

	public function ctextarea($name, $value, $label, $opts=null){

		list($l, $d) = $this->label($label);
		$this->prepareView($name, $l, $d, gOpt($opts, 'required'), gOpt($opts, 'text_add'));
		$this->_view->assign('formfield', $this->textarea($name, $value, $opts));

		return $this->_view->render();
	}

	public function textarea($name, $value, $opts){
		
		$buffer = "<textarea name=\"$name\" ";

		$buffer .= gOpt($opts, 'id') ? "id=\"".gOpt($opts, 'id')."\" ":"";
		$buffer .= gOpt($opts, 'pattern') ? "pattern=\"".gOpt($opts, 'pattern')."\" ":"";
		$buffer .= gOpt($opts, 'hint') ? "placeholder=\"".gOpt($opts, 'hint')."\" ":"";
		$buffer .= gOpt($opts, 'cols') ? "cols=\"".gOpt($opts, 'cols')."\" ":"";
		$buffer .= gOpt($opts, 'rows') ? "rows=\"".gOpt($opts, 'rows')."\" ":"";
		$buffer .= gOpt($opts, 'readonly') ? "readonly=\"".gOpt($opts, 'readonly')."\" ":"";
		$buffer .= gOpt($opts, 'js') ? gOpt($opts, 'js')." ":"";
		$buffer .= gOpt($opts, 'other') ? gOpt($opts, 'other')." ":"";
		$buffer .= ">";
		$buffer .= "$value</textarea>";
		
		return $buffer;
	}

	public function cradio($name, $value, $data, $default, $label, $opts=null){
		
		list($l, $d) = $this->label($label);
		$this->prepareView($name, $l, $d, gOpt($opts, 'required'), gOpt($opts, 'text_add'));
		$this->_view->assign('formfield', $this->radio($name, $value, $data, $default, $opts));
		
		return $this->_view->render();

	}

	public function radio($name, $value, $data, $default, $opts){
		
		$buffer = '';

		$comparison = is_null($value)? $default:$value;
		$space = gOpt($opts, 'aspect')=='v'? "<br />":"&nbsp;";
			
		if(is_array($data)) {
			$i=0;
			foreach($data AS $k => $v) {
				$buffer .= ($i?$space:'')."<input type=\"radio\" name=\"$name\" value=\"$k\" ".($comparison==$k?"checked=\"checked\"":"")." ";
				$buffer .= gOpt($opts, 'id') ? "id=\"".gOpt($opts, 'id')."\" ":"";
				$buffer .= gOpt($opts, 'js') ? gOpt($opts, 'js')." ":"";
				$buffer .= gOpt($opts, 'other') ? gOpt($opts, 'other')." ":"";
				$buffer .= "/>".$v;
				$i++;
			}
		}
		
		return $buffer;
	}
	
	public function cselect($name, $value, $data, $label, $opts=null) {
		
		list($l, $d) = $this->label($label);
		$this->prepareView($name, $l, $d, gOpt($opts, 'required'), gOpt($opts, 'text_add'));
		$this->_view->assign('formfield', $this->select($name, $value, $data, $opts));
		
		return $this->_view->render();

	}
	
	public function select($name, $selected, $data, $opts) {
		
		$buffer = "<select name=\"$name\" ";
		$buffer .= gOpt($opts, 'id') ? "id=\"".gOpt($opts, 'id')."\" ":"";
		$buffer .= gOpt($opts, 'classField') ? "class=\"".gOpt($opts, 'classField')."\" ":"";
		$buffer .= gOpt($opts, 'size') ? "size=\"".gOpt($opts, 'size')."\" ":"";
		$buffer .= gOpt($opts, 'multiple') ? "multiple=\"multiple\" ":"";
		$buffer .= gOpt($opts, 'js') ? gOpt($opts, 'js')." ":"";
		$buffer .= gOpt($opts, 'other') ? gOpt($opts, 'other')." ":"";
		$buffer .= ">\n";

		if(!gOpt($opts, 'noFirst')) $buffer .= "<option value=\"\"></option>\n";
		elseif(gOpt($opts, 'firstVoice')) $buffer .= "<option value=\"".gOpt($opts, 'firstValue')."\">".gOpt($opts, "firstVoice")."</option>";
		
		if(is_array($data)) {
			if(sizeof($data) > 0) {
				foreach ($data as $key=>$value) {
					$title = null;
					if(is_array($value)) { $label = $value['label']; $title = $value['title']; }
					else $label = $value;
					if(gOpt($opts, 'maxChars')) $label = cutHtmlText($label, gOpt($opts, 'maxChars'), '...', true, gOpt($opts, 'cutWords')?gOpt($opts, 'cutWords'):false, true);
					$buffer .= "<option value=\"$key\" ".($key==$selected?"selected=\"selected\"":"")." ".($title ? "title=\"$title\"":"").">".$label."</option>\n";
				}
			}
			else return __("noAvailableOptions");
		}

		$buffer .= "</select>\n";

		return $buffer;
	}
	
	public function ccheckbox($name, $checked, $value, $label, $opts=null){
		
		list($l, $d) = $this->label($label);
		$this->prepareView($name, $l, $d, gOpt($opts, 'required'), gOpt($opts, 'text_add'));
		$this->_view->assign('formfield', $this->checkbox($name, $checked, $value, $opts));
		
		return $this->_view->render();

	}
	
	public function checkbox($name, $checked, $value, $opts=null){
		
		$buffer = "<input type=\"checkbox\" name=\"$name\" value=\"$value\" ".($checked?"checked=\"checked\"":"")." ";
		$buffer .= gOpt($opts, 'id') ? "id=\"".gOpt($opts, 'id')."\" ":"";
		$buffer .= gOpt($opts, 'classField') ? "class=\"".gOpt($opts, 'classField')."\" ":"";
		$buffer .= gOpt($opts, 'js') ? gOpt($opts, 'js')." ":"";
		$buffer .= gOpt($opts, 'other') ? gOpt($opts, 'other')." ":"";
		$buffer .= "/>\n";
		
		return $buffer;
	}

	public function cmulticheckbox($name, $checked, $values, $label, $opts=null){
		
		$label_class = gOpt($opts, 'label_class', '');
		list($l, $d) = $this->label($label);
		$this->prepareView($name, $l, $d, gOpt($opts, 'required'), gOpt($opts, 'text_add'), array("label_class"=>$label_class));
		$this->_view->assign('formfield', $this->multiplecheckbox($name, $checked, $values, $opts));
		
		return $this->_view->render();

	}

	public function multiplecheckbox($name, $checked, $values, $opts=null){

		$rows = array();
		$buffer = '';
		$i=0;
		foreach($values as $value) {
			$rows[$i] = array($value['label']);
			$buffer = "<input type=\"checkbox\" name=\"".$name."\" value=\"".$value['value']."\" ".(in_array($value['value'], $checked) ? "checked=\"checked\"":"")." ";
			$buffer .= gOpt($opts, 'id') ? "id=\"".gOpt($opts, 'id')."\" ":"";
			$buffer .= gOpt($opts, 'classField') ? "class=\"".gOpt($opts, 'classField')."\" ":"";
			$buffer .= gOpt($opts, 'js') ? gOpt($opts, 'js')." ":"";
			$buffer .= gOpt($opts, 'other') ? gOpt($opts, 'other')." ":"";
			$buffer .= "/>\n";
			$rows[$i][] = $buffer;
			$i++;
		}
		
		$view = new view($this->_registry);
		$view->setTpl('form_multicheckbox');
		$view->assign('class', '');
		$view->assign('rows', $rows);

		return $view->render();
	}

	public function cinput_file($name, $value, $label, $opts=null){

		$valid_extension = gOpt($opts, 'extensions', null);
		
		list($l, $d) = $this->label($label);
		$d = $valid_extension ? implode(", ", $valid_extension)."<br />".$d : '';
		$this->prepareView($name, $l, $d, gOpt($opts, 'required'), gOpt($opts, 'text_add'));
		$this->_view->assign('formfield', $this->input_file($name, $value, $opts));

		return $this->_view->render();
	}

	public function input_file($name, $value, $opts=null) {

		$buffer = $this->input($name, 'file', $value, $opts);

		$rel_path = gOpt($opts, 'rel_path') ? (substr(gOpt($opts, 'rel_path'), -1)=='/' ? gOpt($opts, 'rel_path') : gOpt($opts, 'rel_path').'/') : null;

		if($value) {
			$buffer .= "<input type=\"hidden\" name=\"old_$name\" value=\"$value\" />\n";
			$buffer .= "<div style=\"margin-top:5px;\">";
			$buffer .= $this->checkbox("del_".$name, false, 1)." ".__("elimina")." ";
			if(gOpt($opts, 'preview') && $rel_path) 
				$value = "<span class=\"link lightbox\" onclick=\"Slimbox.open('".$rel_path.$value."')\">$value</span>";
			$buffer .= sprintf(__("chargedFileForm"), $value);
			$buffer .= "</div>\n";
		}

		return $buffer;

	}

	public function uploadFile($name, $valid_extension, $path, $link_error, $opts) {
	
		$path = substr($path, -1) == DS ? $path : $path.DS;

		if(!is_dir($path)) mkdir($path, 0755, true);
		
		$def_contents = array(
			"text/plain",
			"text/html",
			"text/xml",
			"image/jpeg",
			"image/gif",
			"image/png",
			"video/mpeg",
			"audio/midi",
			"application/x-zip-compressed",
			"application/vnd.ms-excel",
			"application/x-msdos-program",
			"application/octet-stream"
		);

		$error_query = gOpt($opts, 'error_query', null);
		$check_content = gOpt($opts, 'check_content', true);
		$contents_allowed = gOpt($opts, 'contents', $def_contents); 
		$prefix = gOpt($opts, 'prefix', '');
		$max_file_size = gOpt($opts, 'max_file_size', null);

		if(isset($_FILES[$name]['name']) && $_FILES[$name]['name']) {
			$nfile_size = $_FILES[$name]['size'];
			if($max_file_size && $nfile_size>$max_file_size) {
				if($error_query) $this->registry->db->executeQuery($error_query);
				exit(error::errorMessage(array('error'=>__("MaxSizeError")), $link_error));
			}
			$tmp_file = $_FILES[$name]['tmp_name'];
			$nfile = $this->setFileName($name, $path, $prefix); 

			if(!$this->checkExtension($nfile, $valid_extension) || preg_match('#%00#', $nfile) || ($check_content && !in_array( $_FILES[$name]['type'], $contents_allowed))) {
				if($error_query) $this->registry->db->executeQuery($error_query);
				exit(error::errorMessage(array('error'=>__("FileConsistentError")), $link_error));
			}

		}
		else { $nfile = ''; $tmp_file = ''; }

		$del_file = isset($this->_requestVars['del_'.$name]) && $this->_requestVars['del_'.$name];

		$upload = $delete = false;

		$upload = !empty($nfile);
		$delete = (!empty($nfile) && !empty($this->_requestVars['old_'.$name])) || $del_file;
		
		if($delete) {
			if(is_file($path.$this->_requestVars['old_'.$name]))	
			if(!@unlink($path.$this->_requestVars['old_'.$name])) {
				if($error_query) $this->registry->db->executeQuery($error_query);
				exit(error::errorMessage(array('error'=>__("CantDeleteUploadedFileError")), $link_error));
			}

		}

		if($upload) {
			if(!$this->upload($tmp_file, $nfile, $path)) { 
				if($error_query) $this->registry->db->executeQuery($error_query);
				exit(error::errorMessage(array('error'=>__("CantUploadError")), $link_error));
			}
		}

		if($upload) return $nfile;
		elseif($delete) return '';
		else return $this->_requestVars['old_'.$name];

	}

	private function setFileName($name, $path, $prefix) {
	
		$init_name = $_FILES[$name]['name'];
		$n_name = preg_replace("#[^a-zA-Z0-9_\.-]#", "_", $prefix.$init_name);

		$p_files = scandir($path);

		$i=1;
		while(in_array($n_name, $p_files)) { $n_name = substr($n_name, 0, strrpos($n_name, '.')+1).$i.substr($n_name, strrpos($n_name, '.')); $i++; }

		return $n_name;

	}

	private function checkExtension($filename, $valid_extension) {
	
		if(!$valid_extension) return true;

		$fa = explode(".", $filename);
		$extension = end($fa);

		if(!in_array($extension, $valid_extension)) return false;
		return true;
	
	} 

	private function upload($tmp_file, $filename, $path) {
	
		$file = $path.$filename;
		return move_uploaded_file($tmp_file, $file) ? true : false;

	}

}

?>