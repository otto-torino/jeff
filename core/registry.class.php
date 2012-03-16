<?php
/**
 * @file registry.class.php
 * @brief Contains the registry class.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.98
 * @date 2011-2012
 * @copyright Otto srl MIT License \see http://www.opensource.org/licenses/mit-license.php
 */

/**
 * @ingroup core
 * @brief Global framework registry
 *
 * <p>The registry object acts through all the application like a singleton dictionary.</p>
 * <p>Basically it has a setter and a getter methods by which other objects my create new registry properties.<br />
 * Since the registry is a singleton instance, each of them has access to the properties setted by the others 
 * through the __get method.</p>
 * <p>Hence the registry is a <b>global dictionary</b> (actually an associative array, but I'll call it dictionary 
 * since it's what it represents) used to store public and sharable properties and objects.</p>
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.98
 * @date 2011-2012
 * @copyright Otto srl MIT License \see http://www.opensource.org/licenses/mit-license.php 
 */
class registry extends singleton {

	/*
	 * @brief array containing the registry variables
	 */
	private $vars = array();

	/**
	 * @brief Sets a registry variable 
	 * 	
	 * @param string $index variable name 
  	 * @param mixed $value variable value
  	 * @return void
   	 */
 	public function __set($index, $value) {
        	$this->vars[$index] = $value;
 	}

	
	/**
	 * @brief Adds css file paths to registry 
	 * 
	 * @param string $css relative path 
	 * @return void
	 */
	public function addCss($css) {
		$this->vars['css'][] = $css;	
	}
 
       
	/**
	 * @brief Adds js file paths to registry 
	 * 
	 * @param string $js relative path
	 * @return void
	 */
	public function addJs($js) {
		$this->vars['js'][] = $js;
	}

       
	/**
	 * @brief Adds meta tags to the registry 
	 * 
	 * @param array $meta 
	 *   associative array:
	 *   - <b>name</b>: name attribute 
	 *   - <b>property</b>: property attribute 
	 *   - <b>content</b>: content attribute 
	 * @return void
	 */
	public function addMeta($meta) {
		$this->vars['meta'][] = $meta;	
	}
 
       
	/**
	 * @brief Adds link tags to the registry 
	 * 
	 * @param array $link 
	 *   associative array:
	 *   - <b>rel</b>: rel attribute 
	 *   - <b>type</b>: type attribute 
	 *   - <b>title</b>: title attribute 
	 *   - <b>href</b>: href attribute 
	 * @return void
	 */
	public function addHeadLink($link) {	
		$this->vars['head_links'][] = $link;
	}

	/**
	 * @brief Gets a registry variable
	 * 
	 * @param string $index  variable name
	 * @return void
	 */
	public function __get($index) {
		return $this->vars[$index];
	}

}

?>
