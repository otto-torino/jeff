<?php
/**
 * @file siteSettings.php
 * @brief Contains the model of the siteSettings module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup sitesettings_module
 * @brief Site settings model class
 *
 * Model fields:
 * - **id** int(1): primary key
 * - **app_title** varchar(64): application title (title tag in the html head element)
 * - **app_description** text: application description (meta tag in the html head element)
 * - **app_keywords** varchar(255): application keywords (meta tag in the html head element)
 * - **session_timeout** int(8): session timeout managed by the application (no management if empty or 0)
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class siteSettings extends model {
	
	/**
	 * @brief Constructs a siteSettings instance
	 * 
	 * @return siteSettings instance
	 */
	function __construct() {
	
		$id = 1;
		$this->_tbl_data = TBL_SYS_SETTINGS;

		parent::__construct($id);

	}

}

?>
