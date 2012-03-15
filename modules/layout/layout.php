<?php

class layout extends model {

	function __construct($id) {
	
		$this->_tbl_data = TBL_THEMES;
		parent::__construct($id);

	}

	public static function getThemes($registry, $opts=null) {
	
		$objs = array();
		$rows = $registry->db->autoSelect("id", TBL_THEMES, '', 'active DESC,name');
		foreach($rows as $row) $objs[] = new layout($row['id']);

		return $objs;
	
	}

	public static function activateTheme($registry, $id) {

		foreach(self::getThemes($registry) as $theme) {
			if($theme->id == $id && !$theme->active) {
				$theme->active = 1;
				$theme->saveData();
			}
			if($theme->id != $id && $theme->active) {
				$theme->active = 0;
				$theme->saveData();
			}
		} 
	}

}

?>
