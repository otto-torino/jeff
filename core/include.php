<?php

include(ABS_CORE.DS.'registry.class.php');
include(ABS_CORE.DS.'dtime.class.php');
include(ABS_CORE.DS.'authentication.php');
include(ABS_CORE.DS.'cache.php');
include(ABS_CORE.DS.'access.class.php');
include(ABS_PHPLIB.DS.'functions.php');
include(ABS_PHPLIB.DS.'varFilters.php');
include(ABS_CORE.DS.'error.class.php');
include(ABS_MVC.DS.'controller.class.php');
include(ABS_CORE.DS.'router.class.php');
include(ABS_CORE.DS.'document.class.php');
include(ABS_CORE.DS.'adminTable.class.php');
include(ABS_CORE.DS.'export.class.php');
include(ABS_CORE.DS.'form.class.php');
include(ABS_CORE.DS.'image.class.php');
include(ABS_CORE.DS.'template.class.php');
include(ABS_CORE.DS.'template.factory.php');
include(ABS_CORE.DS.'pagination.class.php');
include(ABS_THEME.DS.'theme.class.php');
include(ABS_DB.DS.'db.factory.php');

function __autoload($class)
{

   	if(is_file(ABS_MDL.DS.$class.DS.$class.'.php'))
   		include_once(ABS_MDL.DS.$class.DS.$class.'.php');
   		
	if (!class_exists($class, false))
		Error::syserrorMessage('include.php', 'autoload', sprintf(__("CantChargeModuleError"), $class, ABS_MDL.DS.$class.DS.$class.'.php'), __LINE__);

}

?>
