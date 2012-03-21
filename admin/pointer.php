<?php
/**
 * @file admin/pointer.php
 * @ingroup entry_point
 * @brief Entry point used to call a specific class method in the administrative area (and get its output).
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @brief absolute path to the ROOT directory 
 */
define('ABS_ROOT', realpath(dirname(dirname(__FILE__))));

/**
 * @brief operating system directory separator 
 */
define( 'DS', DIRECTORY_SEPARATOR );

/**
 * @brief include system paths 
 */
include(ABS_ROOT.DS.'paths.php');

/**
 * @brief include system configuration  
 */
include(ABS_ROOT.DS.'configuration.php');

/**
 * @brief include core class  
 */
include(ABS_CORE.DS.'core.class.php');

/**
 * @brief base path definition from which generate links 
 */
define('BASE_PATH', ROOT_ADMIN);

$core = new core();
$core->methodPointer();

?>
