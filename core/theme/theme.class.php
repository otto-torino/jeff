<?php
/**
 * \file theme.class.php
 * \brief Contains the theme primitive class.
 *
 * @version 0.98
 * @copyright 2011 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors abidibo abidibo@gmail.com
 */

require_once('interface.theme.php');

/**
 * \ingroup themes core
 * |brief the primitive theme class
 * 
 * A Jeff theme is a module composed by views, css, img, locales, js, template files and a class file.<br />
 * The class which takes its name from the theme name extends this theme class (which then acts like a super class) and implements the theme interface.<br />
 * Jeff has a default and complete theme. It's the base theme that all others theme extends (not at class level). 
 * That is every template file, css, localized string, js, img which is not founded in the used theme module is taken from the default one, 
 * so that it's not necessary to overwrite every single aspect of the default theme to create a new custom one, 
 * but you may only overwrite that features that you want to change. 
 * 
 * @version 0.98
 * @copyright 2011 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author abidibo abidibo@gmail.com 
 */
class theme {

	/**
	 * \brief The registry singleton instance 
	 */
	protected $_registry;
	
	/**
	 * \brief The theme name 
	 */
	protected $_name;

	/**
	 * \brief The document template name 
	 */
	protected $_tpl_name;
	
	/**
	 * \brief The template object 
	 */
	protected $_tpl;

	/**
	 * \brief The name of the default theme 
	 */
	protected $_dft_theme = 'default';

	/**
	 * Constructs a theme object 
	 * 
	 * @param string $theme_name the theme name
	 * @return void
	 */
	function __construct($theme_name) {
		$this->_registry = registry::instance();
		$this->_name = $theme_name;
	}
	
	/**
	 * Returns the theme name 
	 * 
	 * @return the theme name
	 */
	public function name() {
		
		return $this->_name;

	}
	
	/**
	 * Returns the theme absolute path 
	 * 
	 * @return the theme absolute path
	 */
	public function path() {
		
		return ABS_THEMES.DS.$this->_name;

	}
	
	/**
	 * Returns the default theme absolute path 
	 * 
	 * @return the default theme absolute path
	 */
	public function dftPath() {
		
		return ABS_THEMES.DS.$this->_dft_theme;

	}
	
	/**
	 * Returns the absolute path of the theme view folder
	 * 
	 * @return the absolute path of the theme view folder
	 */
	public function viewPath() {
		
		return ABS_THEMES.DS.$this->_name.DS."view";

	}
	
	/**
	 * Returns the absolute path of the default theme view folder
	 * 
	 * @return the absolute path of the default theme view folder
	 */
	public function dftViewPath() {
		
		return ABS_THEMES.DS.$this->_dft_theme.DS."view";

	}
	
	/**
	 * Returns the absolute path of the theme css folder
	 * 
	 * @return the absolute path of the theme css folder
	 */
	public function cssPath() {
		
		return ABS_THEMES.DS.$this->_name.DS."css";

	}
	
	/**
	 * Returns the absolute path of the default theme css folder
	 * 
	 * @return the absolute path of the default theme css folder
	 */
	public function dftCssPath() {
		
		return ABS_THEMES.DS.$this->_dft_theme.DS."css";

	}
	
	/**
	 * Getter method for the $_tpl member
	 * 
	 * @return the template object property
	 */
	public function getTemplate() {

		return $this->_tpl;
	
	}

	/**
	 * Sets the document template to render 
	 * 
	 * @param string $tpl the template name
	 * @return the template instance if the template file exists, null otherwise.
	 */
	public function setTpl($tpl) {

		$this->_tpl_name = $tpl;

		if(is_readable($this->path().DS.$tpl.".tpl"))
			$tpl_path = $this->path().DS.$tpl.".tpl";
		elseif(is_readable($this->dftPath().DS.$tpl.".tpl"))
			$tpl_path = $this->dftPath().DS.$tpl.".tpl";
		else $tpl_path = null;

		$this->_tpl = $tpl_path ? new template($this->_registry, $tpl_path) : null;

	}

	/**
	 * Returns the list of css to be included in the document 
	 * 
	 * @return the array containing the theme css to include in the document
	 */
	public function getCss() {

		$css = array();

		// theme css
		if(is_readable($this->path().DS.'css'.DS."stylesheet.css"))
			$css[] = relativePath($this->path())."/css/stylesheet.css";

		// template specific css
		if(is_readable($this->path().DS.'css'.DS.$this->_tpl_name.".css"))
			$css[] = relativePath($this->path())."/css/".$this->_tpl_name.".css";
		elseif(is_readable($this->dftPath().DS.'css'.DS.$this->_tpl_name.".css"))
			$css[] = relativePath($this->dftPath())."/css/".$this->_tpl_name.".css";

		return $css;	

	}
	
	/**
	 * Returns the list of js to be included in the document 
	 * 
	 * @return the array containing the theme js to include in the document
	 */
	public function getJs() {

		$js = array();

		// theme js
		if(is_readable($this->path().DS.'js'.DS."themejs.js"))
			$js[] = relativePath($this->path())."/js/themejs.js";

		// template specific js
		if(is_readable($this->path().DS.'js'.DS.$this->_tpl_name.".js"))
			$js[] = relativePath($this->path())."/js/".$this->_tpl_name.".js";
		elseif(is_readable($this->dftPath().DS.'js'.DS.$this->_tpl_name.".js"))
			$js[] = relativePath($this->dftPath())."/js/".$this->_tpl_name.".js";

		return $js;

	}

}

?>
