<?php

/*
 * Input filters
 */
function cleanVar($var, $type, $opts=array()) {
	return $var;
}

function cleanInput($method, $name, $type, $opts=array()) {

	$db = db::getInstance();

	$flags = array();

	if($method=='get') $method_string = INPUT_GET;
	elseif($method=='post') $method_string = INPUT_POST;
	elseif($method=='request') $method_string = INPUT_REQUEST;

	if($type=='date' || $type=='datetime') {
		if($type=='date' && !preg_match("#^\d{4}-\d{2}-\d{2}$#", $_REQUEST[$name])) return null;
		if($type=='datetime' && !preg_match("#^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$#", $_REQUEST[$name])) return null;
		$type = 'string';
	}

	if($type=='string') {
		$filter = FILTER_SANITIZE_STRING;
		$flags[] = FILTER_FLAG_NO_ENCODE_QUOTES;
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


	$input = filter_input($method_string, $name, $filter, $options);
	settype($input, $type);

	if(!(gOpt($opts, 'escape')===false)) {
		if(get_magic_quotes_gpc()) $input = stripslashes($input);	// magic_quotes_gpc = On
		$input = $db->escapeString($input);
	}

	return $input;

}

function cleanInputArray($method, $name, $type=null, $opts=array()) {
	
	$db = db::getInstance();

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

	if(!(gOpt($opts, 'escape')===false) && count($input)) 
		foreach($input as $k=>$in) {
			if(get_magic_quotes_gpc()) $input[$k] = stripslashes($in);	// magic_quotes_gpc = On
			$input[$k] = $db->escapeString($in);
		}

	return $input;

}

function dbInput($string) {
	
	return mysql_real_escape_string($string);

}

/*
 * Output filters
 */
function htmlVar($var) {
	return $var;
}

function htmlInput($var) {
	$var = preg_replace('#"#', '&#34;', $var);
	return $var;
}

function jsVar($string) {

	$string = preg_replace("#\n|\r|\t#", "", $string);
	$string = preg_replace("#'#", "\'", $string);
	$string = preg_replace("/&#039;/", "\'", $string);
	$string = preg_replace("#\"#", "\'", $string);
	
	return $string;
}

?>
