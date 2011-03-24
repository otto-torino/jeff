<?php

define('ABS_ROOT', realpath(dirname(__FILE__)));
define( 'DS', DIRECTORY_SEPARATOR );

include(ABS_ROOT.DS.'paths.php');
include(ABS_ROOT.DS.'configuration.php');
include(ABS_CORE.DS.'core.php');

define('BASE_PATH', ROOT);

$core = new core();
$core->renderApp();

?>
