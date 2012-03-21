<?php
/**
 * @file /var/www/jeff.git/pointer.php
 * @ingroup entry_point
 * @brief Front end entry point used to call a specific class method (and get its output).
 * @details This file is usually used when performing ajax requests.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * absolute path to the ROOT directory 
 */
define('ABS_ROOT', realpath(dirname(__FILE__)));

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
include(ABS_CORE.DS.'core.class.php');

/**
 * base path definition from which generate links 
 */
define('BASE_PATH', ROOT);

$core = new core();
$core->methodPointer();

?>
