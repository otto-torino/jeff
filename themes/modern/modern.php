<?php
/**
 * @file modern.php
 * @brief Contains the modern theme class
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2014
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup modern_theme Modern theme
 * @brief Jeff modern theme
 * @ingroup themes
 *
 * All missing resources fallback on the default theme
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2014
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup modern_theme
 * @brief White theme class
 * 
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2014
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class modernTheme extends theme implements Itheme {
	
	/**
	 * @brief Constructs a white theme instance 
	 * 
	 * @return white theme instance
	 */
	function __construct() {
		
		parent::__construct('modern');

	}
	
	/**
	 * @brief Gets theme representative image 
	 * 
	 * @return string image relative path
	 */
	public function getImage() {
		return relativePath(dirname(__FILE__)).'/img/modern.jpg';
	}
	
	/**
	 * @brief Gets theme name 
	 * 
	 * @return string theme name
	 */
	public function getName() {
		return __("ModernTheme");
	}
	
	/**
	 * @brief Gets theme description 
	 * 
	 * @return string theme description
	 */
	public function getDescription() {
		return __("ModernThemeDescription");
	}

    /**
	 * @brief Gets theme custom js
	 * 
	 * @return theme's javascripts
	 */
    public function getJs() {
        return array(
            relativePath(dirname(__FILE__).DS."js".DS."bootstrap.js")
        );
    }
}

?>
