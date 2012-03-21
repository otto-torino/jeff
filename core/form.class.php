<?php
/**
 * @file form.class.php
 * @brief Contains the form class.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup forms Form management
 * Set of classes used to manage data forms. The @ref form class provides methods to create html form elements and upload files. 
 *
 * The @ref captcha class allows the generation of captcha images.
 *
 * The @ref image class provides methods to manipulate images.
 */

/**
 * @ingroup core forms
 * @brief Class used to manage forms
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class form {
	
	/**
	 * @brief the registry singleton instance 
	 */
	private $_registry;

	/**
	 * @brief a @ref view instance
	 */
	private $_view;

	/**
	 * @brief form method (post or get)
	 */
	private $_method;

	/**
	 * @brief form name and id 
	 */
	private $_name;
	
	/**
	 * @brief whether to add javascript validation or not 
	 */
	private $_validation;

	/**
	 * @brief Array containing the submitted data
	 */
	private $_requestVars;

	/**
	 * @brief Constructs a form instance 
	 * 
	 * @param string $method form method: post or get
	 * @param string $name form name and id
	 * @param array $opts 
	 *   associative array of options:
	 *   - **validation**: whether to perform javascript validation or not
	 *   - **verifyToken**: whether to verify the token used to detect CSRF attacks or not
	 * @return void
	 */
	function __construct($method, $name, $opts=null) {

		$this->_registry = registry::instance();

		$this->_method = $method;	
		$this->_name = $name;

		$this->_view = new view();

		$this->_validation = gOpt($opts, 'validation', false);

		if(gOpt($opts, 'verifyToken')) {
			if(!$this->verifyFormToken($this->_name)) {
				exit(error::syserrorMessage("form", "construct", __("CSRFDetectError")));
			}
		}
		
		$this->_requestVars = $this->_method == 'post' ? $_POST : ($this->_method == 'get' ? $_GET : $_REQUEST);	
	
	}
	
	/**
	 * @brief CSRF token generation 
	 * 
	 * @return the generated token
	 */
	private function generateFormToken() {
  		$token = md5(uniqid(microtime(), true));
  		$_SESSION[$this->_name.'_token'] = $token;
  		return $token;
	}

	/**
	 * @brief CSRF token verification 
	 * 
	 * @return bool, the verification result
	 */
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

	/**
	 * @brief Loads the submitted data previously saved to session 
	 * 
	 * @param bool $noerror whether to loads data even if no error occurs 
	 * @return void
	 */
	public function load($noerror=false) {
		
		$this->_registry->fvars = array();
		$vars = array();

		if(isset($_SESSION["formvars_".$this->_name])) {
			if($noerror || (isset($_SESSION['ERRORMSG']) AND !empty($_SESSION['ERRORMSG'])))
				foreach($_SESSION['formvars_'.$this->_name] as $k=>$v)
						$vars[$k] = $v;
			$this->_registry->fvars = $vars;

			unset($_SESSION['formvars_'.$this->_name]);
		}

	}

	/**
	 * @brief Saves submitted data to session 
	 * 
	 * @return void
	 */
	public function save() {
		
		$_SESSION["formvars_".$this->_name] = array();
		foreach($this->_requestVars as $key => $value)
			$_SESSION["formvars_".$this->_name][$key] = $value;

	}

	/**
	 * @brief Retrieves the value of a variable loaded from session (and so previously submitted) 
	 * 
	 * @param string $name the field name 
	 * @param mixed $dft deafult value if variable was not loaded 
	 * @return void
	 */
	public function retvar($name, $dft=null) {

		return isset($this->_registry->fvars[$name]) ? $this->_registry->fvars[$name] : $dft;

	}

	/**
	 * @brief Deletes the session array used to save and load submitted data 
	 * 
	 * @return void
	 */
	public function free() {
	
			unset($_SESSION['formvars_'.$this->_name]);

	}

	/**
	 * @brief Sets the required fields 
	 * 
	 * @param string $required comma separated list of required fields
	 * @return void
	 */
	public function setRequired($required) {
		
		return !empty($required) ? $this->hidden('required', $required) : '';

	}

	/**
	 * @brief Checks if the required fields have been filled 
	 * 
	 * @return bool, the check result, true if all required fields were filled, false otherwise
	 */
	public function checkRequired() {
		
		$error = false;
		$required = isset($this->_requestVars['required']) ? cleanInput($this->_method, 'required', 'string') : '';
		
		if(!empty($required)) {
			foreach(explode(",", $required) as $fieldname) {
				if($this->_requestVars[trim($fieldname)]=='') $error = true;
			}
		}

		return $error;

	}

	/**
	 * @brief Form open tag 
	 * 
	 * @param string $action form action attribute
	 * @param string $required comma separated list of required fields
	 * @param mixed $opts 
	 *   Associative array:
	 *   - **upload**: bool. Whether the form allows file upload or not
	 *   - **generateToken**: bool. Whether the generate a CSRF token
	 * @return the form open tag
	 */
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

	/**
	 * @brief Form close tag 
	 * 
	 * @return string the form close tag
	 */
	public function cform() {

		return "</form>";

	}

	/**
	 * @brief Captcha complete field (label, image and input field) 
	 * 
	 * @param array $opts 
	 *   associative array of options:
	 *   - **text_add**: text added after the field 
	 *   - see @ref form::captcha
	 * @return void
	 */
	public function ccaptcha($opts=null) {
	
		list($l, $d) = array(__("SecureCode"), __("SecureCodeExp"));

		$this->prepareView('captcha_code', $l, $d, true, gOpt($opts, 'text_add'));
		$this->_view->assign('formfield', $this->captcha($opts));
		
		return $this->_view->render();
	}

	/**
	 * @brief Captcha field (image and input field) 
	 * 
	 * @param array $opts 
	 *   associative array of options:
	 *   - **class**: css class of the div which contains the captcha 
	 * @return void
	 */
	public function captcha($opts=null) {

		$class = gOpt($opts, "class", "left captcha");
	
		require_once(ABS_CORE.DS.'captcha.class.php');
		$captcha = new captcha('captcha_code');
	
		return "<div class=\"$class\">".$captcha->render()."</div>";

	}

	/**
	 * @brief Captcha code check 
	 * 
	 * @return bool, check result
	 */
	public function checkCaptcha() {
	
		require_once(ABS_CORE.DS.'captcha.class.php');

		$captcha = new captcha('captcha_code');

		return $captcha->check();	

	}
	
	/**
	 * @brief Fieldset element 
	 * 
	 * @param string $legend the legend content
	 * @param string $content the fieldset content 
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **id**: fieldset id 
	 * @return the fieldset element
	 */
	public function fieldset($legend, $content, $opts=null) {

		$this->_view->setTpl('form_fieldset');
		$this->_view->assign('id', gOpt($opts, "id"));
		$this->_view->assign('legend', $legend);
		$this->_view->assign('content', $content);

		return $this->_view->render();
	}

	/**
	 * @brief Label preparation 
	 * 
	 * @param mixed $text label text. Possible values are:
	 *   - string: the label text
	 *   - array: the first or 'label' element is the label text, the second or 'description' element is the description text
	 * @return void
	 */
	public function label($text){

		if(!$text) return array(null, null);
		if(is_array($text) && count($text)==2) {
			$label = isset($text['label']) ? $text['label'] : $text[0];
			$description = isset($text['description']) ? $text['description'] : $text[1];
		}
		else {$label = $text; $description = null;}
		
		return array($label, $description);
	}

	/**
	 * @brief Hidden field 
	 * 
	 * @param string $name field name
	 * @param mixed $value field value
	 * @param array $opts 
	 *   associative array of options:
	 *   - **id**: field id 
	 * @return void
	 */
	public function hidden($name, $value, $opts=null) {

		$buffer = "<input type=\"hidden\" name=\"$name\" value=\"$value\" ".(gOpt($opts, 'id')?"id=\"".gOpt($opts, 'id')."\"":"")."/>\n";

		return $buffer;
	}

	/**
	 * @brief Prepare the form element view 
	 * 
	 * @param string $name field name 
	 * @param mixed $l field label
	 * @param mixed $d field label description 
	 * @param bool $req whether the field is required or not 
	 * @param string $tadd text added after the input element
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **label_class**: string. css class for the label element 
	 *   - **label_form**: string. form attribute of the label element 
	 *   - **more**: string. Additional content after the input element and text added 
	 * @return void
	 */
	private function prepareView($name, $l, $d, $req, $tadd, $opts=null) {
		
		$this->_view->setTpl('form_element');
		$this->_view->assign('name', $name);
		$this->_view->assign('label', $l);
		if(gOpt($opts, 'label_class')) 
			$this->_view->assign('label_class', gOpt($opts, 'label_class'));
		if(gOpt($opts, 'label_form', true)) 
			$this->_view->assign('label_form', $this->_name);
		$this->_view->assign('required', $req);
		$this->_view->assign('description', $d);
		$this->_view->assign('textadd', $tadd);
		$this->_view->assign('more', null);

	}

	/**
	 * @brief Custom content formatted as a form element (label and field) 
	 * 
	 * @param string $cleft left content (as a label)
	 * @param string $cright right content (as an input)
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **idLeft**: string. id attribute of the left content 
	 *   - **idRight**: string. id attribute of the right content 
	 * @return void
	 */
	public function freeInput($cleft, $cright, $opts=null) {
	
		$this->_view->setTpl('form_cell');
		$this->_view->assign('idleft', gOpt($opts, 'idleft', null));
		$this->_view->assign('cleft', $cleft);
		$this->_view->assign('idright', gOpt($opts, 'idright', null));
		$this->_view->assign('cright', $cright);

		return $this->_view->render();
	}

	/**
	 * @brief Form input element 
	 * 
	 * @param string $name the field name
	 * @param string $type the input type (text, password, checkbox, ...)
	 * @param mixed $value the input value
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **id**: string. id attribute of the input element 
	 *   - **class**: string. css class of the input element 
	 *   - **pattern**: string. pattern attribute of the input element 
	 *   - **hint**: string. hint message shown when the specified pattern checks fails 
	 *   - **placeholder**: string. placeholder attribute of the input element 
	 *   - **size**: string. size attribute of the input element 
	 *   - **maxlength**: string. maxlength attribute of the input element 
	 *   - **readonly**: bool. whether the input element is readonly or not 
	 *   - **required**: bool. whether the field is required or not 
	 *   - **formnovalidate**: bool. whether the input element is not to be validated or not 
	 *   - **js**: string. js event handler 
	 *   - **other**: string. other element attributes 
	 * @return the input element
	 */
	public function input($name, $type, $value, $opts=null){

		$dft_pattern = $dft_hint = null;

		$buffer = "<input type=\"$type\" name=\"$name\" value=\"$value\" ";

		if($type == 'email') {
			$dft_pattern = "^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$";
			$dft_hint = __("insertValidEmail");	
		}
		
		$buffer .= gOpt($opts, 'id') ? "id=\"".gOpt($opts, 'id')."\" ":"";
		$buffer .= gOpt($opts, 'class') ? "class=\"".gOpt($opts, 'class')."\" ":"";
		$buffer .= gOpt($opts, 'pattern', $dft_pattern) ? "pattern=\"".gOpt($opts, 'pattern', $dft_pattern)."\" ":"";
		$buffer .= gOpt($opts, 'hint', $dft_hint) ? "data-hint=\"".gOpt($opts, 'hint', $dft_hint)."\" ":"";
		$buffer .= gOpt($opts, 'placeholder') ? "placeholder=\"".gOpt($opts, 'placeholder')."\" ":"";
		$buffer .= gOpt($opts, 'size') ? "size=\"".gOpt($opts, 'size')."\" ":"";
		$buffer .= gOpt($opts, 'maxlength') ? "maxlength=\"".gOpt($opts, 'maxlength')."\" ":"";
		$buffer .= gOpt($opts, 'readonly') ? "readonly=\"".gOpt($opts, 'readonly')."\" ":"";
		$buffer .= gOpt($opts, 'required') ? "required=\"required\" ":"";
		$buffer .= gOpt($opts, 'formnovalidate') ? "formnovalidate ":"";
		$buffer .= gOpt($opts, 'js') ? gOpt($opts, 'js')." ":"";
		$buffer .= gOpt($opts, 'other') ? gOpt($opts, 'other')." ":"";
	
		$buffer .= " />";

		return $buffer;
	}
	
	/**
	 * @brief Complete form input element (label and input field) 
	 * 
	 * @param string $name field name 
	 * @param string $type input type
	 * @param mixed $value field value 
	 * @param mixed $label field label
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **text_add**: text added after the field 
	 *   - see @ref form::input
	 * @return the complete input element
	 */
	public function cinput($name, $type, $value, $label, $opts){

		list($l, $d) = $this->label($label);
		$this->prepareView($name, $l, $d, gOpt($opts, 'required'), gOpt($opts, 'text_add'));
		$this->_view->assign('formfield', $this->input($name, $type, $value, $opts));
		
		return $this->_view->render();
	}
	
	/**
	 * @brief Complete form date input element (label and input field) 
	 * 
	 * @param string $name field name 
	 * @param string $value field value 
	 * @param mixed $label field label
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **text_add**: text added after the field 
	 *   - see @ref form::input except from options size, maxlength, pattern, hint which have default values
	 * @return the complete datetime element
	 */
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
	
	/**
	 * @brief Complete form datetime input element (label and input field) 
	 * 
	 * @param string $name field name 
	 * @param string $value field value 
	 * @param mixed $label field label
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **text_add**: text added after the field 
	 *   - see @ref form::input except from options size, maxlength, pattern, hint which have default values
	 * @return the datetime element
	 */
	public function cinput_datetime($name, $value, $label, $opts){

		$opts['size'] = gOpt($opts, 'seconds')==true ? 19 : 16;
		$opts['maxlength'] = 19; // the input/output format has always seconds
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

	/**
	 * @brief Complete form textarea element (label and textarea) 
	 * 
	 * @param string $name field name 
	 * @param string $value field value 
	 * @param mixed $label field label
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **text_add**: text added after the field 
	 *   - see @ref form::textarea
	 * @return the complete textarea element
	 */
	public function ctextarea($name, $value, $label, $opts=null){

		list($l, $d) = $this->label($label);
		$this->prepareView($name, $l, $d, gOpt($opts, 'required'), gOpt($opts, 'text_add'));
		$this->_view->assign('formfield', $this->textarea($name, $value, $opts));

		return $this->_view->render();
	}

	/**
	 * @brief Textarea form element
	 * 
	 * @param string $name field name 
	 * @param string $value field value 
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **editor**: whether to charge dojo editor or not 
	 *   - **id**: string. id attribute of the textarea element 
	 *   - **class**: string. css class of the textarea element 
	 *   - **pattern**: string. pattern attribute of the textarea element 
	 *   - **hint**: string. hint message shown when the specified pattern checks fails 
	 *   - **placeholder**: string. placeholder attribute of the textarea element 
	 *   - **cols**: int. cols attribute of the textarea element 
	 *   - **rows**: int. rows attribute of the textarea element 
	 *   - **readonly**: bool. whether the textarea element is readonly or not 
	 *   - **required**: bool. whether the field is required or not 
	 *   - **js**: string. js event handler 
	 *   - **other**: string. other element attributes 
	 * @return the texarea element
	 */
	public function textarea($name, $value, $opts){
		
		if(gOpt($opts, 'editor', false)) {
			$buffer = "<div id=\"$name\" class=\"html\">$value</div>";
			$buffer .= $this->hidden($name, '');
		}
		else {
			$buffer = "<textarea name=\"$name\" ";

			$buffer .= gOpt($opts, 'id') ? "id=\"".gOpt($opts, 'id')."\" ":"";
			$buffer .= gOpt($opts, 'class') ? "class=\"".gOpt($opts, 'class')."\" ":"";
			$buffer .= gOpt($opts, 'pattern') ? "pattern=\"".gOpt($opts, 'pattern')."\" ":"";
			$buffer .= gOpt($opts, 'hint') ? "data-hint=\"".gOpt($opts, 'hint')."\" ":"";
			$buffer .= gOpt($opts, 'placeholder') ? "placeholder=\"".gOpt($opts, 'placeholder')."\" ":"";
			$buffer .= gOpt($opts, 'cols') ? "cols=\"".gOpt($opts, 'cols')."\" ":"";
			$buffer .= gOpt($opts, 'rows') ? "rows=\"".gOpt($opts, 'rows')."\" ":"";
			$buffer .= gOpt($opts, 'readonly') ? "readonly=\"".gOpt($opts, 'readonly')."\" ":"";
			$buffer .= gOpt($opts, 'required') ? "required=\"required\" ":"";
			$buffer .= gOpt($opts, 'js') ? gOpt($opts, 'js')." ":"";
			$buffer .= gOpt($opts, 'other') ? gOpt($opts, 'other')." ":"";
			$buffer .= ">";
			$buffer .= "$value</textarea>";
		}
		
		return $buffer;
	}

	/**
	 * @brief Complete form radio button (label and input element) 
	 * 
	 * @param string $name field name
	 * @param mixed $value field value
	 * @param array $data radio choices in the array form array('value'=>'label')
	 * @param mixed $default default choice selected 
	 * @param mixed $label field label 
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **text_add**: text added after the field 
	 *   - **required**: bool. whether the field is required or not 
	 *   - see @ref form::radio
	 * @return the complete radio button
	 */
	public function cradio($name, $value, $data, $default, $label, $opts=null){
		
		list($l, $d) = $this->label($label);
		$this->prepareView($name, $l, $d, gOpt($opts, 'required'), gOpt($opts, 'text_add'));
		$this->_view->assign('formfield', $this->radio($name, $value, $data, $default, $opts));
		
		return $this->_view->render();

	}

	/**
	 * @brief Form radio button
	 * 
	 * @param string $name field name
	 * @param mixed $value field value
	 * @param array $data radio choices in the array form array('value'=>'label')
	 * @param mixed $default default choice selected 
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **aspect**: string. whether the choices should be vertically or horizontally listed 
	 *   - **id**: string. id attribute of the element 
	 *   - **js**: string. js event handler 
	 *   - **other**: string. other element attributes 
	 * @return the radio button
	 */
	public function radio($name, $value, $data, $default, $opts){
		
		$buffer = '';
		$comparison = is_null($value)? $default:$value;
		$space = gOpt($opts, 'aspect')=='v'? "<br />":"&nbsp;";
			
		if(is_array($data)) {
			$i=0;
			foreach($data AS $k => $v) {
				$buffer .= ($i?$space:'')."<input type=\"radio\" name=\"$name\" value=\"$k\" ".($comparison==$k && !($comparison==='' && $k===0) ?"checked=\"checked\"":"")." ";
				$buffer .= gOpt($opts, 'id') ? "id=\"".gOpt($opts, 'id')."\" ":"";
				$buffer .= gOpt($opts, 'js') ? gOpt($opts, 'js')." ":"";
				$buffer .= gOpt($opts, 'other') ? gOpt($opts, 'other')." ":"";
				$buffer .= "/>".$v;
				$i++;
			}
		}
		
		return $buffer;
	}
	
	/**
	 * @brief Complete form select element (label and select element) 
	 * 
	 * @param string $name field name
	 * @param mixed $value field value
	 * @param array $data select options in the array form array('value'=>'label')
	 * @param mixed $label field label 
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **text_add**: text added after the field
	 *   - see @ref form::select
	 * @return the complete select element
	 */
	public function cselect($name, $value, $data, $label, $opts=null) {
		
		list($l, $d) = $this->label($label);
		$this->prepareView($name, $l, $d, gOpt($opts, 'required'), gOpt($opts, 'text_add'));
		$this->_view->assign('formfield', $this->select($name, $value, $data, $opts));
		
		return $this->_view->render();

	}
	
	/**
	 * @brief Select form element 
	 * 
	 * @param string $name field name
	 * @param mixed $value field value
	 * @param array $data select options in the array form array('value'=>'label')
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **id**: string. id attribute of the element 
	 *   - **classField**: string. css class of the select element 
	 *   - **size**: string. size attribute of the select element 
	 *   - **multiple**: bool. whether to allow multiple selection or not 
	 *   - **required**: bool. whether the field is required or not 
	 *   - **js**: string. js event handler 
	 *   - **other**: string. other element attributes 
	 *   - **firstVoice**: string. custom first option label 
	 *   - **firstValue**: string. custom first option value 
	 *   - **noFirst**: bool. Whether to show or not an empty first option 
	 *   - **maxChars**: int. If set the option labels are cut at the given number of characters 
	 *   - **cutWords**: bool default false. Whether to allow cut of words when truncating labels or not 
	 * @return the select element
	 */
	public function select($name, $selected, $data, $opts) {
		
		$buffer = "<select name=\"$name\" ";
		$buffer .= gOpt($opts, 'id') ? "id=\"".gOpt($opts, 'id')."\" ":"";
		$buffer .= gOpt($opts, 'classField') ? "class=\"".gOpt($opts, 'classField')."\" ":"";
		$buffer .= gOpt($opts, 'size') ? "size=\"".gOpt($opts, 'size')."\" ":"";
		$buffer .= gOpt($opts, 'multiple') ? "multiple=\"multiple\" ":"";
		$buffer .= gOpt($opts, 'required') ? "required=\"required\" ":"";
		$buffer .= gOpt($opts, 'js') ? gOpt($opts, 'js')." ":"";
		$buffer .= gOpt($opts, 'other') ? gOpt($opts, 'other')." ":"";
		$buffer .= ">\n";

		if(!is_array($selected)) $selected = array($selected);

		if(gOpt($opts, 'firstVoice')) $buffer .= "<option value=\"".gOpt($opts, 'firstValue')."\">".gOpt($opts, "firstVoice")."</option>";
		elseif(!gOpt($opts, 'noFirst')) $buffer .= "<option value=\"\"></option>\n";
		
		if(is_array($data)) {
			if(sizeof($data) > 0) {
				foreach ($data as $key=>$value) {
					$title = null;
					if(is_array($value)) { $label = $value['label']; $title = $value['title']; }
					else $label = $value;
					if(gOpt($opts, 'maxChars')) $label = cutHtmlText($label, gOpt($opts, 'maxChars'), '...', true, gOpt($opts, 'cutWords', false), true);
					$buffer .= "<option value=\"$key\" ".(in_array($key, $selected)?"selected=\"selected\"":"")." ".($title ? "title=\"$title\"":"").">".$label."</option>\n";
				}
			}
		}

		$buffer .= "</select>\n";

		return $buffer;
	}
	
	/**
	 * @brief Complete checkbox form element (label and input element) 
	 * 
	 * @param string $name field name
	 * @param  bool $checked whether to check the field or not
	 * @param mixed $value field value
	 * @param mixed $label field label
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **text_add**: text added after the field
	 *   - see @ref form::checkbox
	 * @return the complete checkbox element
	 */
	public function ccheckbox($name, $checked, $value, $label, $opts=null){
		
		list($l, $d) = $this->label($label);
		$this->prepareView($name, $l, $d, gOpt($opts, 'required'), gOpt($opts, 'text_add'));
		$this->_view->assign('formfield', $this->checkbox($name, $checked, $value, $opts));
		
		return $this->_view->render();

	}
	
	/**
	 * @brief Checkbox form element 
	 * 
	 * @param string $name field name
	 * @param  bool $checked whether to check the field or not
	 * @param mixed $value field value
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **id**: string. id attribute of the element 
	 *   - **classField**: string. css class of the element
	 *   - **required**: bool. whether the field is required or not 
	 *   - **js**: string. js event handler 
	 *   - **other**: string. other element attributes 
	 * @return the checkbox element
	 */
	public function checkbox($name, $checked, $value, $opts=null){
		
		$buffer = "<input type=\"checkbox\" name=\"$name\" value=\"$value\" ".($checked?"checked=\"checked\"":"")." ";
		$buffer .= gOpt($opts, 'id') ? "id=\"".gOpt($opts, 'id')."\" ":"";
		$buffer .= gOpt($opts, 'classField') ? "class=\"".gOpt($opts, 'classField')."\" ":"";
		$buffer .= gOpt($opts, 'required') ? "required=\"required\" ":"";
		$buffer .= gOpt($opts, 'js') ? gOpt($opts, 'js')." ":"";
		$buffer .= gOpt($opts, 'other') ? gOpt($opts, 'other')." ":"";
		$buffer .= "/>\n";
		
		return $buffer;
	}

	/**
	 * @brief Complete multi checkbox form elements (label and elements) 
	 * 
	 * @param string $name field name
	 * @param array $checked array containing the checked items
	 * @param array $values array of items in the form array(array('label'=>'item_label', 'value'=>'item_value'))
	 * @param mixed $label form label 
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **label_class**: label css class
	 *   - **text_add**: text added after the field
	 *   - see @ref form::multiplecheckbox
	 * @return the complete multicheck element
	 */
	public function cmulticheckbox($name, $checked, $values, $label, $opts=null){
		
		$label_class = gOpt($opts, 'label_class', '');
		list($l, $d) = $this->label($label);
		$this->prepareView($name, $l, $d, gOpt($opts, 'required'), gOpt($opts, 'text_add'), array("label_class"=>$label_class));
		$this->_view->assign('formfield', $this->multiplecheckbox($name, $checked, $values, $opts));
		
		return $this->_view->render();

	}

	/**
	 * @brief Multi checkbox form elements 
	 * 
	 * @param string $name field name
	 * @param array $checked array containing the checked items
	 * @param array $values array of items in the form array(array('label'=>'item_label', 'value'=>'item_value'))
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **id**: string. id attribute of every single element 
	 *   - **classField**: string. css class of every single element
	 *   - **js**: string. js event handler of every single element
	 *   - **other**: string. other element attributes of every single element
	 * @return the multicheck element
	 */
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

	/**
	 * @brief Complete form input file element 
	 * 
	 * @param string $name field name
	 * @param string $value field value
	 * @param mixed $label field label
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **extensions**: array. List of valid file extensions
	 *   - **text_add**: text added after the field
	 *   - see \ref form::input_file
	 * @return the complete input file element
	 */
	public function cinput_file($name, $value, $label, $opts=null){

		$valid_extension = gOpt($opts, 'extensions', null);
		
		list($l, $d) = $this->label($label);
		$d = $valid_extension ? implode(", ", $valid_extension)."<br />".$d : '';
		$this->prepareView($name, $l, $d, gOpt($opts, 'required'), gOpt($opts, 'text_add'));
		$this->_view->assign('formfield', $this->input_file($name, $value, $opts));

		return $this->_view->render();
	}

	/**
	 * @brief Form input file element 
	 * 
	 * @param string $name field name
	 * @param string $value field value
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **rel_path**: string. Relative path of the directory of upload
	 *   - **preview**: whether to show a lightbox style file previwe or not (to use with images only)
	 *   - see @ref form::input
	 * @return the input file element
	 */
	public function input_file($name, $value, $opts=null) {

		$required = gOpt($opts, 'required', false);
		if($value) $opts['required'] = false;

		$buffer = $this->input($name, 'file', $value, $opts);

		$rel_path = gOpt($opts, 'rel_path') ? (substr(gOpt($opts, 'rel_path'), -1)=='/' ? gOpt($opts, 'rel_path') : gOpt($opts, 'rel_path').'/') : null;

		if($value) {
			$buffer .= "<input type=\"hidden\" name=\"old_$name\" value=\"$value\" />\n";
			$buffer .= "<div style=\"margin-top:5px;\">";
			if(!$required) $buffer .= $this->checkbox("del_".$name, false, 1)." ".__("delete")." ";
			$file_size = $rel_path ? filesize(preg_replace("#".preg_quote(ROOT)."#", "", ABS_ROOT).preg_replace("#/#", DS, $rel_path.$value)) : null;
			if(gOpt($opts, 'preview') && $rel_path) 
				$value = "<a title=\"$value\" href=\"".$rel_path.$value."\">$value</a><script>$$('a[href=".$rel_path.$value."]')[0].cerabox();</script>";
			$buffer .= $file_size ? sprintf(__("chargedFileFormWithSize"), $value, round($file_size/1024, 1)." Kb") : sprintf(__("chargedFileForm"), $value);
			$buffer .= "</div>\n";
		}

		return $buffer;

	}

	/**
	 * @brief Management of form file uploads 
	 *
	 * Method for managing files via the upload form. If a new file is uploaded the old file is deleted.
	 *
	 * The old file can also be deleted without uploading a new one. If no file is uploaded and the 
	 * deletion checkbox in the input file element is unchecked then all stands as is.
	 * 
	 * @param string $name field name 
	 * @param array $valid_extension list of valid extensions
	 * @param string $path absolute path to the upload directory
	 * @param string $link_error redirection url in case of error
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **error_query**: string. Query to execute in case of file upload error
	 *   - **check_content**: string. Whether to check file mime type or not
	 *   - **contents**: array. List of allowed mime types, if not given the default one is taken
	 *   - **prefix**: string. Filename prefix
	 *   - **max_file_size**: int. Maximum size allowed for the file
	 * @return the name of the uploaded file or an empty string or the old file name 
	 */
	public function uploadFile($name, $valid_extension, $path, $link_error, $opts) {
	
		$path = substr($path, -1) == DS ? $path : $path.DS;

		if(!is_dir($path)) mkdir($path, 0755, true);
		
		$def_contents = array(
			"text/plain",
			"text/html",
			"text/xml",
			"image/jpeg",
			"image/pjpeg",
			"image/gif",
			"image/png",
			"image/bmp",
			"video/mpeg",
			"audio/midi",
			"application/pdf",
			"application/msword",
			"application/x-compressed",
			"application/x-gtar",
			"application/x-gzip",
			"multipart/x-gzip",
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
	
	/**
	 * @brief Uploads the submitted image to the given directory 
	 * 
	 * Method for managing images via the upload form. If a new file is uploaded the old file is deleted.
	 *
	 * The old file can also be deleted without uploading a new one. If no file is uploaded and the 
	 * deletion checkbox in the input file element is unchecked then all stands as is.
	 *
	 * @param string $name field name 
	 * @param array $valid_extension list of valid extensions
	 * @param string $path absolute path to the upload directory
	 * @param string $link_error redirectino url in case of error
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **error_query**: string. Query to execute in case of file upload error
	 *   - **check_content**: string. Whether to check file mime type or not
	 *   - **contents**: array. List of allowed mime types, if not given the default one is taken
	 *   - **prefix**: string. Filename prefix
	 *   - **prefix_thumb**: string default 'thumb_'. Thumbnail prefix
	 *   - **make_thumb**: bool default false. Whether to create a thumbnail or not
	 *   - **resize**: bool default false. Whether to resize the image or not
	 *   - **scale**: bool default false. Whether to scale the image or not
	 *   - **resize_enlarge**: bool default false. Whether to allow image enlargement during resizing or not
	 *   - **resize_width**: int default null. With used to resize the image (if only width or height are given the proportions are maintained)
	 *   - **resize_height**: int default null. Height used to resize the image
	 *   - **thumb_width**: int default null. With of the thumbnail (if only width or height are given the proportions are maintained)
	 *   - **thumb_height**: int default null. Height of the thumbnail
	 *   - **max_file_size**: int. Maximum size allowed for the file
	 * @return void
	 */
	public function uploadImage($name, $valid_extension, $path, $link_error, $opts) {
	
		$path = substr($path, -1) == DS ? $path : $path.DS;

		if(!is_dir($path)) mkdir($path, 0755, true);
		
		$def_contents = array(
			"image/jpeg",
			"image/pjpeg",
			"image/gif",
			"image/png"
		);

		$error_query = gOpt($opts, 'error_query', null);
		$check_content = gOpt($opts, 'check_content', true);
		$contents_allowed = gOpt($opts, 'contents', $def_contents); 
		$prefix = gOpt($opts, 'prefix', '');
		$prefix_thumb = gOpt($opts, 'prefix_thumb', 'thumb_');
		$make_thumb = gOpt($opts, 'make_thumb', false);
		$resize = gOpt($opts, 'resize', false);
		$scale = gOpt($opts, 'scale', false);
		$resize_enlarge = gOpt($opts, 'resize_enlarge', false);
		$resize_width = gOpt($opts, 'resize_width', null);
		$resize_height = gOpt($opts, 'resize_height', null);
		$thumb_width = gOpt($opts, 'thumb_width', null);
		$thumb_height = gOpt($opts, 'thumb_height', null);
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
			if($make_thumb) {
				$old_file_thumb = $prefix_thumb.$this->_requestVars['old_'.$name];
				
				if(is_file($path.$old_file_thumb))	
					if(!@unlink($path.$old_file_thumb)) {
						if($error_query) $this->registry->db->executeQuery($error_query);
						exit(error::errorMessage(array('error'=>__("CantDeleteUploadedFileError")), $link_error));
					}
			}

		}

		if($upload) {
			
			if(!$this->upload($tmp_file, $nfile, $path)) { 
				if($error_query) $this->registry->db->executeQuery($error_query);
				exit(error::errorMessage(array('error'=>__("CantUploadError")), $link_error));
			}
			
			$image = new image();
			$image->load($path.$nfile);

			if($resize) {
				$opts = array("enlarge"=>$resize_enlarge);
				if($resize_width && $resize_height) {
					$image->resize($resize_width, $resize_height, $opts);	
				}
				elseif($resize_width) {
					$image->resizeToWidth($resize_width, $opts);	
				}
				elseif($resize_height) {
					$image->resizeToHeight($resize_height, $opts);	
				}
			}
			elseif($scale) {
				$image->scale($scale);	
			}
			
			$image->save($path.$nfile, $image->type());

			if($make_thumb) {
				$nthumbfile = $prefix_thumb.$nfile; 
				$opts = array("enlarge"=>true);
				if($thumb_width && $thumb_height) {
					$image->resize($thumb_width, $thumb_height, $opts);	
				}
				elseif($thumb_width) {
					$image->resizeToWidth($thumb_width, $opts);	
				}
				elseif($thumb_height) {
					$image->resizeToHeight($thumb_height, $opts);	
				}

				$image->save($path.$nthumbfile, $image->type());
			}
		}

		if($upload) return $nfile;
		elseif($delete) return '';
		else return $this->_requestVars['old_'.$name];

	}

	/**
	 * @brief Sets the upload file name
	 *
	 * If another file with the same name exists in the upload directory adds .1, .2, etc.. at the end of the name 
	 * 
	 * @param string $name file name
	 * @param string $path absolute path of the upload directory
	 * @param string $prefix file name prefix
	 * @return the file name
	 */
	private function setFileName($name, $path, $prefix) {
	
		$init_name = $_FILES[$name]['name'];
		$n_name = preg_replace("#[^a-zA-Z0-9_\.-]#", "_", $prefix.$init_name);

		$p_files = scandir($path);

		$i=1;
		while(in_array($n_name, $p_files)) { $n_name = substr($n_name, 0, strrpos($n_name, '.')+1).$i.substr($n_name, strrpos($n_name, '.')); $i++; }

		return $n_name;

	}

	/**
	 * @brief Checks if the file extension is allowed 
	 * 
	 * @param string $filename file name
	 * @param array $valid_extension list of valid extensions
	 * @return bool, the check result
	 */
	private function checkExtension($filename, $valid_extension) {
	
		if(!$valid_extension) return true;

		$fa = explode(".", $filename);
		$extension = end($fa);

		if(!in_array($extension, $valid_extension)) return false;
		return true;
	
	} 

	/**
	 * @brief Upload a file to the given directory 
	 * 
	 * @param string $tmp_file name of the temporary file
	 * @param string $filename name of the file saved to filesystem
	 * @param string $path path of the saving directory
	 * @return bool, result of file uploading
	 */
	private function upload($tmp_file, $filename, $path) {
	
		$file = $path.$filename;
		return move_uploaded_file($tmp_file, $file) ? true : false;

	}

}

?>
