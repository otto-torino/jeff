<?php

class language extends model {

	function __construct($id) {
	
		$this->_tbl_data = TBL_LNG;

		parent::__construct($id);

	}

	public static function get($registry, $opts=null) {
	
		$objs = array();
		$where = gOpt($opts, "where", ''); 
		$rows = $registry->db->autoSelect("id", TBL_LNG, $where, 'language');
		foreach($rows as $row) $objs[] = new language($row['id']);

		return $objs;
	
	}

	public static function getFromLabel($registry, $label) {

		$rows = $registry->db->autoSelect("id", TBL_LNG, "label='$label'", 'language');
		if(count($rows)) return new language($rows[0]['id']);

		return null;
	
	}

	public static function setLanguage($registry) {
	
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
