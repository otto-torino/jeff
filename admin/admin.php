<?php
/**
 * \file admin.php
 * Jeff administrative area entry point.
 *
 * @version 0.98
 * @copyright 2011 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors abidibo abidibo@gmail.com
 */

/**
 * absolute path to the ROOT directory 
 */
define('ABS_ROOT', realpath(dirname(dirname(__FILE__))));

/**
 * operating system directory separator 
 */
define( 'DS', DIRECTORY_SEPARATOR );

/**
 * include system paths 
 */
include(ABS_ROOT.DS.'paths.php');

/**
 * include system configuration  
 */
include(ABS_ROOT.DS.'configuration.php');

/**
 * include core class  
 */
include(ABS_CORE.DS.'core.php');

/**
 * base path definition from which generate links 
 */
define('BASE_PATH', ROOT_ADMIN);

$core = new core();
$core->renderApp('admin');

?>
