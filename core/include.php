<?php
/**
 * @file include.php
 * @ingroup core
 * @brief Includes core classes and provides an autoload method.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

include(ABS_CORE.DS.'singleton.class.php');
include(ABS_CORE.DS.'registry.class.php');
include(ABS_PHPLIB.DS.'functions.php');
include(ABS_CORE.DS.'dtime.class.php');
include(ABS_CORE.DS.'authentication.class.php');
include(ABS_CORE.DS.'cache.php');
include(ABS_CORE.DS.'access.class.php');
include(ABS_PHPLIB.DS.'varFilters.php');
include(ABS_CORE.DS.'error.class.php');
include(ABS_MVC.DS.'controller.class.php');
include(ABS_CORE.DS.'router.class.php');
include(ABS_CORE.DS.'document.class.php');
include(ABS_CORE.DS.'adminTable.class.php');
include(ABS_CORE.DS.'export.class.php');
include(ABS_CORE.DS.'search.class.php');
include(ABS_CORE.DS.'form.class.php');
include(ABS_CORE.DS.'image.class.php');
include(ABS_TEMPLATE.DS.'template.class.php');
include(ABS_TEMPLATE.DS.'template.factory.php');
include(ABS_CORE.DS.'pagination.class.php');
include(ABS_THEME.DS.'theme.class.php');
include(ABS_DB.DS.'db.factory.php');
include(ABS_DB.DS.'mysql.php');

/**
 * @brief Auto includes the requested class model or exits with error 
 * 
 * @param mixed $class name of the model class
 * @return void
 */
function __autoload($class) {

   	if(is_file(ABS_MDL.DS.$class.DS.$class.'.php')) {
   		include_once(ABS_MDL.DS.$class.DS.$class.'.php');
	}
   		
	if (!class_exists($class, false)) {
		Error::syserrorMessage('include.php', 'autoload', sprintf(__("CantChargeModuleError"), $class, ABS_MDL.DS.$class.DS.$class.'.php'), __LINE__);
	}
}

?>
