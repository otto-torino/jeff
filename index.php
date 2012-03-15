<?php
/**
 * @file /var/www/jeff.git/index.php
 * @ingroup entry_point
 * @brief Front end entry point.
 * @details Every request passes from here, except from the ajax requests which points tio single class methods.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.98
 * @date 2011-2012
 * @copyright Otto srl MIT License \see http://www.opensource.org/licenses/mit-license.php
 */

/**
 * @defgroup entry_point Entry points
 *
 * <p>Group of file which are entry points for the application.<br />
 * There are two kinds of entry point, one used to render the whole document and the other used to render only the output of a controller method</p>
 */

/**
 * @brief absolute path to the ROOT directory 
 */
define('ABS_ROOT', realpath(dirname(__FILE__)));

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
define('BASE_PATH', ROOT);

/**
 * @brief render the application  
 */
$core = new core();
$core->renderApp();

?>
