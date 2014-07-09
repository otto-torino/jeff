<?php
/**
 * @file db.factory.php
 * @brief Contains the database factory implementation.
 *
 * The database instance is unique due to the use of the singleton pattern.\n       
 * The database instance returned depends on the DBMS configuration setting, so that it's easy to add support for other DBMS different from MySQL.\n
 * Look the main Database class to see which methods a specific DBMS class can override. Each specific Db class shoud extend the Database class.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup database Database management
 * Set of classes used to create a db client object. The existence of only one db client instance at runtime is granted by the **singleton** pattern.
 *
 * The **abstract factory** pattern is also used in the creation of the client db instance so that the db management module is **easily extensible**.
 *
 * The class used as db client is decided at runtime reading the constant **DBMS** set in the [configuration](configuration.php) file.\n        
 *
 */

/**
 * \ingroup database core
 * @brief Database factory, returns a database object instance depending on the DBMS configuration setting. 
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
abstract class db extends singleton {

    /**
     * @brief database host
     */
    private static $_db_host = DB_HOST;

    /**
     * @brief database user
     */
    private static $_db_user = DB_USER;

    /**
     * @brief database password
     */
    private static $_db_pass = DB_PASSWORD;

    /**
     * @brief database name
     */
    private static $_db_dbname = DB_DBNAME;

    /**
     * @brief database charset
     */
    private static $_db_charset = DB_CHARSET;

    /**
     * @brief database schema
     */
    private static $_db_schema = DB_SCHEMA;

    /**
     * @brief returns a singleton db instance 
     * 
     * @return 
     *   the singleton instance
     */
    public static function instance() {

        $class = get_class();

        // singleton, return always the same instance
        if(array_key_exists($class, self::$_instances) === false) {

            if(class_exists(DBMS)) {
                $dbclass = DBMS;
            }
            else{
                $dbclass = 'Database';
            }

            self::$_instances[$class] = new $dbclass(
                array(
                    "connect"=>true,
                    "dbms"=>DBMS,
                    "host"=>DB_HOST,
                    "user"=>DB_USER,
                    "password"=>DB_PASSWORD,
                    "db_name"=>DB_DBNAME,
                    "charset"=>DB_CHARSET
                )
            );
        }

        return self::$_instances[$class];

    }

}

?>
