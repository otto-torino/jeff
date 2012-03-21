<?php
/**
 * @file language.php
 * @brief Contains the model of the language module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup language_module
 * @brief language model class
 *
 * <p>Model fields:</p>
 * - **id** int(8): primary key
 * - **label** varchar(10): short label, i.e. 'I', 'GB'
 * - **language** varchar(50): language full name
 * - **code** varchar(5): language code, i.e. 'en_EN'
 * - **main** int(1): is the main language?
 * - **active** int(1): is active?
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class language extends model {
	
	/**
	 * @brief Constructs a language model instance
	 * 
	 * @param mixed $id the object id (primary key value of the record)
	 * @return language instance
	 */
	function __construct($id) {
	
		$this->_tbl_data = TBL_LNG;

		parent::__construct($id);

	}

	/**
	 * @brief Get language objects 
	 * 
	 * @param array $opts associative array of options:
	 * - **where**: where clause for the select statement
	 * @return array language objects
	 */
	public static function get($registry, $opts=null) {
	
		$objs = array();
		$where = gOpt($opts, "where", ''); 
		$rows = $registry->db->autoSelect("id", TBL_LNG, $where, 'language');
		foreach($rows as $row) $objs[] = new language($row['id']);

		return $objs;
	
	}
	
	/**
	 * @brief Get language object from label 
	 * 
	 * @param string $label language label
	 * @return mixed language object or null if not found
	 */
	public static function getFromLabel($label) {

		$registry = registry::instance();

		$rows = $registry->db->autoSelect("id", TBL_LNG, "label='$label'", 'language');
		if(count($rows)) return new language($rows[0]['id']);

		return null;
	
	}

	/**
	 * @brief Set the active language 
	 *
	 * Looks for $_GET 'lng' parameter, existing session value or sets the default language
	 * 
	 * @return string language name
	 */
	public static function setLanguage() {

		$registry = registry::instance();
	
		$language = null;
		if($code = cleanInput('get', 'lng', 'string')) {
			// charge language and put it in session
			$rows = $registry->db->autoSelect(array("id", "language"), TBL_LNG, "code='$code'", 'language');
			$language = $rows[0]['language'];
			$_SESSION['lng'] = $language;
			header("Location: ".preg_replace("#\?.*$#", "", $_SERVER['REQUEST_URI']));
		}
		elseif(isset($_SESSION['lng'])) {
			// use session language
			$language = $_SESSION['lng'];
		}

		if(!$language) {
			// default language
			$rows = $registry->db->autoSelect(array("id", "language"), TBL_LNG, "main='1'", 'language');
			$language = $rows[0]['language'];
			$_SESSION['lng'] = $language;
		}
	
		return $language;
	}

}

?>
