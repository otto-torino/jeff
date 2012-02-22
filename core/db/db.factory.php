<?php

require_once('interface.db.php');

/*
 * Factory Class which creates concrete db objects
 */
abstract class db extends singleton {
	
	/* DB Configuration Paramethers */
	private static $_db_host = DB_HOST;
	private static $_db_user = DB_USER;
	private static $_db_pass = DB_PASSWORD;
	private static $_db_dbname = DB_DBNAME;
	private static $_db_charset = DB_CHARSET;
	private static $_db_schema = DB_SCHEMA;

	public static function instance() {
		
		$class = get_class();

		// singleton, return always the same instance
		if(array_key_exists($class, self::$_instances) === false) {

			if(DBMS=='mysql') { 
				self::$_instances[$class] = new mysql(
					array(
						"connect"=>true,
						"host"=>self::$_db_host,
						"user"=>self::$_db_user,
						"password"=>self::$_db_pass,
						"db_name"=>self::$_db_dbname,
						"charset"=>self::$_db_charset
					)
				);
			}
		}

		return self::$_instances[$class];

	}

}

?>
