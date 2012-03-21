<?php
/**
 * @file white.php
 * @brief Contains the white theme class
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup white_theme White theme
 * @brief Jeff white theme
 * @ingroup themes
 *
 * All missing resources fallback on the default theme
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup white_theme
 * @brief White theme class
 * 
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class whiteTheme extends theme implements Itheme {
	
	/**
	 * @brief Constructs a white theme instance 
	 * 
	 * @return white theme instance
	 */
	function __construct() {
		
		parent::__construct('white');

	}
	
	/**
	 * @brief Gets theme representative image 
	 * 
	 * @return string image relative path
	 */
	public function getImage() {
		return relativePath(dirname(__FILE__)).'/img/white.jpg';
	}
	
	/**
	 * @brief Gets theme name 
	 * 
	 * @return string theme name
	 */
	public function getName() {
		return __("WhiteTheme");
	}
	
	/**
	 * @brief Gets theme description 
	 * 
	 * @return string theme description
	 */
	public function getDescription() {
		return __("WhiteThemeDescription");
	}
}

?>
