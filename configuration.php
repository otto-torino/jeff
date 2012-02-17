<?php
/**
* \file configuration.php
* Jeff configuration file.
*
* @version 0.98
* @copyright 2011 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
* @authors abidibo abidibo@gmail.com
*/

/**
 * session name
 */
define('SESSIONNAME', 'JEFF_SID');

/**
 *  application language (head meta tag)
 */
define('APP_LANGUAGE', 'it_IT');

/**
 * Database Management System
 */
define('DBMS', 'mysql');

/**
 * Database hostname 
 */
define('DB_HOST', 'localhost');

/**
 * Database name 
 */
define('DB_DBNAME', 'db_jeff');

/**
 * Database username 
 */
define('DB_USER', 'root');

/**
 * Database user password 
 */
define('DB_PASSWORD', '');

/**
 * Database schema
 */
define('DB_SCHEMA', '');

/**
 * Database charset 
 */
define('DB_CHARSET', 'utf8'); 

/**
 *  System passwords cryptation method
 */
define('PWD_HASH', 'md5'); // md5 | sha1 | "empty"(-> no cryptation)

/**
 * Debug mode.
 *
 * View more detailed information when error occurs 
 */
define('DEBUG', true);

?>
