<?php
/**
 * @file default.php
 * @brief Contains the default theme class
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup default_theme Default theme
 * @brief Jeff default theme
 * @ingroup themes
 *
 * Contains all the views, css, translations needed by the system. Every other theme has a fallback to the default when it lacks any resource 
 * 
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup default_theme
 * @brief Default theme class
 * 
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class defaultTheme extends theme implements Itheme {

	/**
	 * @brief Constructs a default theme instance 
	 * 
	 * @return default theme instance
	 */
	function __construct() {
		
		parent::__construct('default');

	}
	
	/**
	 * @brief Gets theme representative image 
	 * 
	 * @return string image relative path
	 */
	public function getImage() {
		return relativePath(dirname(__FILE__)).'/img/default.jpg';
	}
	
	/**
	 * @brief Gets theme name 
	 * 
	 * @return string theme name
	 */
	public function getName() {
		return __("DefaultTheme");
	}
	
	/**
	 * @brief Gets theme description 
	 * 
	 * @return string theme description
	 */
	public function getDescription() {
		return __("DefaultThemeDescription");
	}
}

?>
