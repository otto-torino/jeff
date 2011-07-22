<?php

define('TBL_SYS_SETTINGS', 'sys_site_settings');
define('TBL_SYS_DATETIME_SETTINGS', 'sys_datetime_settings');
define('TBL_SYS_GROUPS', 'sys_groups');
define('TBL_SYS_PRIVILEGES', 'sys_privileges');
define('TBL_USERS', 'users');
define('TBL_THEMES', 'themes');
define('TBL_LNG', 'languages');

if(is_readable(ABS_ROOT.DS.'project_tables.php')) include(ABS_ROOT.DS.'project_tables.php');

?>
