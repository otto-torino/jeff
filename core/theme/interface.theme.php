<?php
/**
 * @file interface.theme.php
 * @brief Contains the theme interface.
 *
 * Defines a common interface for all theme classes
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup themes Theme management
 * Set of classes used to manage themes
 *
 * Thanks to its modularity Jeff may support different themes, and the user may set which one to use, setting it as active. \n
 * All theme classes extends the theme class and implements the theme interface.
 *
 * Every new installed theme "extends" the default one, in the sense that the new template may only override some features and all other stuffs (img, css, views, locales, js)
 * falbacks to the default template, this way is quite fast to change for example only the template graphics
 * because it's enough to create some images and rewrite the css stylesheet.
 *
 * What follow is the list of features a theme might have
 * - **css** (under themes/theme_name/css/)
 *       - stylesheet.css: the main theme css
 *       - <template_name>.css: the specific global template css
 *       modules css: specific modules css
 * - **js** (under themes/theme_name/js)
 *       - themejs.js: the main theme javascript file (not present in the default theme)
 *       - <active_template>.js: the specific template js
 * - **images** (themes/theme_name/img) 
 *       - the theme images
 * - **locale** (themes/theme_name/languages) 
 *       - the files which contains the associative array used to translate strings
 * - **view** (themes/theme_name/view) 
 *       - all the module's views, that is the module templates
 * - **template files** (themes/theme_name/) es public_home.tpl
 *       - all the global template files loaded and parsed by the template engine
 * - **<template_name>.php** (themes/theme_name) the specific theme class
 *
 */

/**
 * @ingroup themes core
 * @brief The common interface for all theme classes
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
interface Itheme {

	/**
	 * @brief Returns the theme name 
	 * 
	 * @return the theme name
	 */
	public function name();

	/**
	 * @brief Returns the theme absolute path 
	 * 
	 * @return the theme absolute path
	 */
	public function path();

	/**
	 * @brief Returns the default theme absolute path 
	 * 
	 * @return the default theme absolute path
	 */
	public function dftPath();

	/**
	 * @brief Returns the absolute path of the theme view folder
	 * 
	 * @return the absolute path of the theme view folder
	 */
	public function viewPath();

	/**
	 * @brief Returns the absolute path of the default theme view folder
	 * 
	 * @return the absolute path of the default theme view folder
	 */
	public function dftViewPath();

	/**
	 * @brief Getter method for the $_tpl member
	 * 
	 * @return the template object property
	 */
	public function getTemplate();

	/**
	 * @brief Sets the document template to render 
	 * 
	 * @param string $tpl the template name
	 * @return the template instance if the template file exists, null otherwise.
	 */
	public function setTpl($tpl);

	/**
	 * @brief Returns the list of css to be included in the document 
	 * 
	 * @return the array containing the theme css to include in the document
	 */
	public function getCss();

	/**
	 * @brief Returns the list of js to be included in the document 
	 * 
	 * @return the array containing the theme js to include in the document
	 */
	public function getJs();

	/**
	 * @brief Returns the theme name 
	 * 
	 * @return the theme name
	 */
	public function getName();
	
	/**
	 * @brief Returns the theme description 
	 * 
	 * @return the theme description
	 */
	public function getDescription();

	/**
	 * @brief Returns the theme snapshot 
	 * 
	 * @return the theme snapshot
	 */
	public function getImage();

}

?>
