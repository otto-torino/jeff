<?php
/**
 * @file tables.php
 * @ingroup core
 * @brief Contains system database tables definition
 *
 * Tables used by new modules added to the web application should be defined in the file **ROOT/project_tables.php**.
 * Create it if it doesn't exists, it will be automatically included here.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @brief database table which stores system settings 
 */
define('TBL_SYS_SETTINGS', 'sys_site_settings');

/**
 * @brief database table which stores date and time settings 
 */
define('TBL_SYS_DATETIME_SETTINGS', 'sys_datetime_settings');

/**
 * @brief database table which stores system user groups 
 */
define('TBL_SYS_GROUPS', 'sys_groups');

/**
 * @brief database table which stores system access privileges 
 */
define('TBL_SYS_PRIVILEGES', 'sys_privileges');

/**
 * @brief database table which stores system users 
 */
define('TBL_USERS', 'users');

/**
 * @brief database table which stores available themes 
 */
define('TBL_THEMES', 'themes');

/**
 * @brief database table which stores application languages 
 */
define('TBL_LNG', 'languages');

/**
 * @brief database table which stores menu voices 
 */
define('TBL_MENU', 'menu');

if(is_readable(ABS_ROOT.DS.'project_tables.php')) {
	include(ABS_ROOT.DS.'project_tables.php');
}

?>
