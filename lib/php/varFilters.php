<?php
/**
 * @file varFilters.php
 * @ingroup php_lib core
 * @brief PHP functions used a to work with strings 
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/*
 * Input filters
 */

/**
 * @brief Clean variables
 * @todo Not used by now, but should be implemented looking at @ref cleanInput 
 * 
 * @param mixed $var variable to clean
 * @param string $type variable type
 * @param array $opts associative array of options
 * @return cleaned var
 */
function cleanVar($var, $type, $opts=array()) {
	return $var;
}

/**
 * @brief Sanitize user inputs 
 * 
 * @param string $method input method ('get', 'post' or 'request')
 * @param string $name input name 
 * @param string $type input type ('string', 'int', 'float', 'date', 'datetime', 'email', 'html') 
 * @param array $opts 
 *   associative array of options:
 *   - **escape**: bool default true. Whether to escape input for db insertion or not
 * @return the sanitized input
 */
function cleanInput($method, $name, $type, $opts=array()) {

	$db = db::instance();

	$flags = array();
	$filter_opts = null;

	if($method=='get') $method_string = INPUT_GET;
	elseif($method=='post') $method_string = INPUT_POST;
	elseif($method=='request') $method_string = INPUT_REQUEST;

	if($type=='date' || $type=='datetime') {
		if($type=='date' && !preg_match("#^\d{4}-\d{2}-\d{2}$#", $_REQUEST[$name])) return null;
		if($type=='datetime' && !preg_match("#^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$#", $_REQUEST[$name])) return null;
		$type = 'string';
	}

	if($type=='string' || $type=='email' || $type=='html') {
	    	if($type=='email' && !preg_match("#^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$#i", $_REQUEST[$name])) return null;

		if($type=='email') $filter = FILTER_SANITIZE_EMAIL;
		elseif($type=='html') { $filter = FILTER_CALLBACK; $filter_opts = "sanitizeHtml"; }
		else $filter = FILTER_SANITIZE_STRING;

		$flags[] = FILTER_FLAG_NO_ENCODE_QUOTES;
		$type = 'string';
	}
	elseif($type=='int') $filter = FILTER_SANITIZE_NUMBER_INT;
	elseif($type=='float') {
		$filter = FILTER_SANITIZE_NUMBER_FLOAT;
		$flags[] = FILTER_FLAG_ALLOW_FRACTION;
	}

	$f = null;
	$tot = count($flags);
	if($tot)  
		for($i=0;$i<$tot;$i++) 
			$f = $i ? $f | $flags[$i] : $flags[0];

	$options = array("flags"=>$f);
	if($filter_opts) $options["options"] = $filter_opts;


	$input = filter_input($method_string, $name, $filter, $options);

	if(get_magic_quotes_gpc()) $input = stripslashes($input);	// magic_quotes_gpc = On

	if(is_null($filter)) exit($input);
	settype($input, $type);

	if(!(gOpt($opts, 'escape', true)===false)) {
		$input = $db->escapeString($input);
	}

	return $input;

}

/**
 * @brief Sanitize user array inputs 
 * 
 * @param string $method input method ('get', 'post' or 'request')
 * @param string $name input name 
 * @param string $type input array elements type ('string', 'int', 'float') 
 * @param array $opts 
 *   associative array of options:
 *   - **escape**: bool default true. Whether to escape inputs for db insertion or not
 * @return the sanitized array
 */
function cleanInputArray($method, $name, $type=null, $opts=array()) {
	
	$db = db::instance();

	$flags = array(FILTER_REQUIRE_ARRAY);

	if($method=='get') $method_string = INPUT_GET;
	elseif($method=='post') $method_string = INPUT_POST;
	elseif($method=='request') $method_string = INPUT_REQUEST;

	if($type=='string') {
		$filter = FILTER_SANITIZE_STRING;
		$flags[] = FILTER_FLAG_NO_ENCODE_QUOTES;
	}
	elseif($type=='int') $filter = FILTER_SANITIZE_NUMBER_INT;
	elseif($type=='float') {
		$filter = FILTER_SANITIZE_NUMBER_FLOAT;
		$flags[] = FILTER_FLAG_ALLOW_FRACTION;
	}
	else $filter = FILTER_SANITIZE_STRING;

	$f = null;
	$tot = count($flags);
	if($tot)  
		for($i=0;$i<$tot;$i++) 
			$f = $i ? $f | $flags[$i] : $flags[0];

	$options = array("flags"=>$f);

	$input = filter_input($method_string, $name, $filter, $options);

	if(!(gOpt($opts, 'escape', true)===false) && count($input)) 
		foreach($input as $k=>$in) {
			if(get_magic_quotes_gpc()) $input[$k] = stripslashes($in);	// magic_quotes_gpc = On
			$input[$k] = $db->escapeString($in);
		}

	return $input;

}

/**
 * @brief Sanitize html content 
 * @todo strip dangerous tags if needed, check user privileges or which tags are allowed for the input
 * 
 * @param string $html html string 
 * @return the sanitized string
 */
function sanitizeHtml($html) {

	// strip dangerous tags here
	return $html;

}

/*
 * Output filters
 */

/**
 * @brief Filters text coming from database before rendering it in the html document 
 * @todo maybe useful to strip dangerous tags to avoid XSS or similar if the db insertion sanitization process fails
 * 
 * @param string $string string to filter 
 * @return filtered string
 */
function htmlVar($string) {
	return $string;
}

/**
 * @brief Prepares a string which has to be inserted in an input field 
 * 
 * @param string $string string to prepare 
 * @return prepared string
 */
function htmlInput($string) {
	$string = preg_replace('#"#', '&#34;', $string);
	return $string;
}

/**
 * @brief Javascript variable escaping
 *
 * Escapes strings that has to be used as javascript variables
 * 
 * @param string $string string to escape
 * @return escaped string
 */
function jsVar($string) {

	$string = preg_replace("#\n|\r|\t#", "", $string);
	$string = preg_replace("#'#", "\'", $string);
	$string = preg_replace("/&#039;/", "\'", $string);
	$string = preg_replace("#\"#", "\'", $string);
	
	return $string;
}

?>
