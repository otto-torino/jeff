<?php
/**
 * @file configuration.php
 * @brief Jeff configuration file.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.98
 * @date 2011-2012
 * @copyright Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 */

/**
 * @brief session name
 */
define('SESSIONNAME', 'JEFF_SID');

/**
 * @brief application language (head meta tag)
 */
define('APP_LANGUAGE', 'it_IT');

/**
 * @brief Database Management System
 */
define('DBMS', 'mysql');

/**
 * @brief Database hostname 
 */
define('DB_HOST', 'localhost');

/**
 * @brief Database name 
 */
define('DB_DBNAME', 'db_jeff');

/**
 * @brief Database username 
 */
define('DB_USER', 'root');

/**
 * @brief Database user password 
 */
define('DB_PASSWORD', '');

/**
 * @brief Database schema
 */
define('DB_SCHEMA', '');

/**
 * @brief Database charset 
 */
define('DB_CHARSET', 'utf8'); 

/**
 * @brief System passwords cryptation method
 */
define('PWD_HASH', 'md5'); // md5 | sha1 | "empty"(-> no cryptation)

/**
 * @brief Debug mode.
 *
 * View detailed information when system error occurs 
 */
define('DEBUG', true);

?>
