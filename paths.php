<?php
/**
 * @file paths.php
 * @brief Jeff system paths definition.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 *  @brief absolute path to the ADMIN ROOT directory 
 */
define('ABS_ADMIN', ABS_ROOT.DS.'admin');

/**
 *  @brief absolute path to the upload directory
 */
define('ABS_UPLOAD', ABS_ROOT.DS.'upload');

/**
 * @brief absolute path to the cache directory 
 */
define('ABS_CACHE', ABS_ROOT.DS.'cache');

/**
 * @brief absolute path to the core directory 
 */
define('ABS_CORE', ABS_ROOT.DS.'core');

/**
 * @brief absolute path to the MVC directory,
 * 
 * Contains the primitive mvc classes
 */
define('ABS_MVC', ABS_CORE.DS.'mvc');

/**
 *  @brief absolute path to the lib directory
 */
define('ABS_LIB', ABS_ROOT.DS.'lib');

/**
 * @brief absolute path to the php libraries directory 
 */
define('ABS_PHPLIB', ABS_LIB.DS.'php');

/**
 * @brief absolute path to the themes directory 
 */
define('ABS_THEMES', ABS_ROOT.DS.'themes');

/**
 * @brief aboslute path to the modules directory 
 */
define('ABS_MDL', ABS_ROOT.DS.'modules');

/**
 * @brief absolute path to the core db directory 
 */
define('ABS_DB', ABS_CORE.DS.'db');

/**
 * @brief absolute path to the core theme directory 
 */
define('ABS_THEME', ABS_CORE.DS.'theme');

/**
 * @brief absolute path to the core template directory 
 */
define('ABS_TEMPLATE', ABS_CORE.DS.'template');

/**
 * @brief absolute path to the plugins directory 
 */
define('ABS_PLUGINS', ABS_ROOT.DS.'plugins');

if(strrpos(php_uname(), "Windows")!==false) {
	
	$os = 'win';
	$sdocroot = preg_replace("#/#", "\\", $_SERVER['DOCUMENT_ROOT']);
	$root = preg_replace("#".preg_quote($sdocroot)."#", "", ABS_ROOT);
	$root_const = $root[0] != '/' ? '/'.$root : $root;
}
else {
	if(strrpos(php_uname(), "Darwin")!==false) {
		$os = 'mac';
	}
	elseif(strrpos(php_uname(), "Linux")!==false) {
		$os = 'linux';
	}
	else {
		$os = 'undefined';
	}

	$root_const = preg_replace("#".$_SERVER['DOCUMENT_ROOT']."#", "", ABS_ROOT);
}

/**
 * @brief server operating system 
 */
define('OS', $os);

/**
 * @brief relative path to ROOT directory  
 */
define('ROOT', $root_const);

/**
 * @brief relative path to ADMIN ROOT dierctory  
 */
define('ROOT_ADMIN', ROOT.'/admin');

/**
 * @brief relative path to javascript libraries  
 */
define('REL_JSLIB', ROOT.'/lib/js');

/**
 * @brief relative path to css directory  
 */
define('REL_CSS', ROOT.'/css');

/**
 * @brief relative path to upload directory  
 */
define('REL_UPLOAD', ROOT.'/upload');

?>
