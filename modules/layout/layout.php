<?php

class layout extends model {

	function __construct($registry, $id) {
	
		$this->_registry = $registry;
		$this->_tbl_data = TBL_THEMES;
		parent::__construct($this->initP($id));

	}

	private function initP($id) {

		return $this->initDbProp($id);

	}

	public static function getThemes($registry, $opts=null) {
	
		$objs = array();
		$rows = $registry->db->autoSelect("id", TBL_THEMES, '', 'active DESC,name');
		foreach($rows as $row) $objs[] = new layout($registry, $row['id']);

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
