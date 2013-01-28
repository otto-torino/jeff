<?php
/**
 * @file layout.php
 * @brief Contains the model of the layout module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup layout_module
 * @brief Layout model class
 *
 * Model fields:
 * - **id** int(8): primary key
 * - **name** varchar(128):theme name
 * - **active** int(1): is active?
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class layout extends model {
	
	/**
	 * @brief Constructs a layout instance
	 * 
	 * @param mixed $id the object id (primary key value of the record)
	 * @return layout instance
	 */
	function __construct($id) {
	
		$this->_tbl_data = TBL_THEMES;
		parent::__construct($id);

	}
	
	/**
	 * @brief Gets all available themes 
	 * 
	 * @param array $opts: associative array of options (no one by now)
	 * @return array layout objects
	 */
	public static function getThemes($opts=null) {
	
		$registry = registry::instance();
		$objs = array();
		$rows = $registry->db->autoSelect("id", TBL_THEMES, '', 'active DESC,name');
		foreach($rows as $row) $objs[] = new layout($row['id']);

		return $objs;
	
	}

	/**
	 * @brief Sets theme as active from given id
	 * 
	 * @param int $id the theme id (primary key value) 
	 * @return void
	 */
	public static function activateTheme($id) {

		$registry = registry::instance();

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
