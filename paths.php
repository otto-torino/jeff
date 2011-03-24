<?php
define('ABS_ADMIN', ABS_ROOT.DS.'admin');
define('ABS_UPLOAD', ABS_ROOT.DS.'upload');
define('ABS_CACHE', ABS_ROOT.DS.'cache');
define('ABS_CORE', ABS_ROOT.DS.'core');
define('ABS_MVC', ABS_CORE.DS.'mvc');
define('ABS_LIB', ABS_ROOT.DS.'lib');
define('ABS_PHPLIB', ABS_LIB.DS.'php');
define('ABS_THEMES', ABS_ROOT.DS.'themes');
define('ABS_MDL', ABS_ROOT.DS.'modules');
define('ABS_DB', ABS_CORE.DS.'db');
define('ABS_THEME', ABS_CORE.DS.'theme');

if(strrpos(php_uname(), "Windows")!==false) {
	define('OS', 'win');
	$sdocroot = preg_replace("#/#", "\\", $_SERVER['DOCUMENT_ROOT']);
	$root = preg_replace("#".preg_quote($sdocroot)."#", "", ABS_ROOT);
	define('ROOT', $root[0] != '/' ? '/'.$root : $root);
}
else {
	if(strrpos(php_uname(), "Darwin")!==false) define('OS', 'mac');
	elseif(strrpos(php_uname(), "Linux")!==false) define('OS', 'linux');
	else define('OS', 'undefined');

	define('ROOT', preg_replace("#".$_SERVER['DOCUMENT_ROOT']."#", "", ABS_ROOT));
}

define('ROOT_ADMIN', ROOT.'/admin');
define('REL_JSLIB', ROOT.'/lib/js');
define('REL_CSS', ROOT.'/css');
define('REL_UPLOAD', ROOT.'/upload');

?>
