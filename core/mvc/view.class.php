<?php
/**
 * \file view.class.php
 * \brief Contains the view primitive class.
 *
 * Defines the mvc view class
 *
 * @version 0.98
 * @copyright 2011 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors abidibo abidibo@gmail.com
 */

/**
 * \ingroup mvc core
 * \brief View class of the MVC pattern, is the class used to manage the module's views. 
 * 
 * This is the general view class used by module controllers.<br />
 *  It acts like an template engine at module level. the module templates are evaluated using the context variables assigned by the controller.<br />
 *
 * @version 0.98
 * @copyright 2011 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author abidibo abidibo@gmail.com 
 */
class view {
	
	/**
	 * \brief The standard class objects which contains the view context 
	 */
	protected $_data;
	
	/**
	 * \brief The registry singleton instance 
	 */
	protected $_registry;

	/**
	 * \brief Associative array containing the js and css to include asynchronously in the document
	 */
	protected $_assets;

	/**
	 * \brief The path to the folder containing the view of the active theme
	 */
	protected $_view_folder;

	/**
	 * \brief The path to the folder containing the view of the default theme (fallback purposes)
	 */
	protected $_dft_view_folder;

	/**
	 * \brief The path to the folder containing the css of the active theme
	 */
	protected $_css_folder;

	/**
	 * \brief The path to the folder containing the css of the default theme (fallback purposes)
	 */
	protected $_dft_css_folder;

	/**
	 * Constructs a view instance
	 *
	 * Initializes some class members 
	 * 
	 * @return void
	 */
	function __construct() {

		$this->_data = new stdClass();
		$this->_registry = registry::instance();
		$this->_view_folder = $this->_registry->theme->viewPath();
		$this->_css_folder = $this->_registry->theme->cssPath();
		$this->_dft_view_folder = $this->_registry->theme->dftViewPath();
		$this->_dft_css_folder = $this->_registry->theme->dftCssPath();
	}

	/**
	 * Sets the view template
	 * 
	 * Searches for the given template in the active theme view folder, than in the deafult theme view folder. If can't find it returns an error.<br />
	 * Adds a stylesheet if passed through the opts parameter. 
	 * 
	 * @param string $tpl the template name
	 * @param array $opts 
	 *   an associative array of options
	 *   - 'css': the stylesheet to charge with the template
	 * @return void
	 */
	public function setTpl($tpl, $opts=null) {

		if(is_readable($tpl.".php")) $this->_tpl = $tpl.".php";
		elseif(is_readable($this->_view_folder.DS.$tpl.".php")) $this->_tpl = $this->_view_folder.DS.$tpl.".php";
		elseif(is_readable($this->_dft_view_folder.DS.$tpl.".php")) $this->_tpl = $this->_dft_view_folder.DS.$tpl.".php";
		else Error::syserrorMessage('view', 'setTpl', sprintf(__("CantChargeTemplateError"), $tpl.".php"), __LINE__);

		if(gOpt($opts, 'css') && is_readable($this->_css_folder.DS.gOpt($opts, 'css').".css"))
			$this->_registry->addCss(relativePath($this->_css_folder).'/'.gOpt($opts, 'css').'.css');
		elseif(gOpt($opts, 'css') && is_readable($this->_dft_css_folder.DS.gOpt($opts, 'css').".css"))
			$this->_registry->addCss(relativePath($this->_dft_css_folder).'/'.gOpt($opts, 'css').'.css');

	}

	/**
	 * Setter method for the $_asset property 
	 * 
	 * @param mixed $assets array in the form array('asset_path'=>'asset_type'). The 'asset_type' may be 'css' or 'js'. 
	 * @return void
	 */
	public function setAssets($assets) {
		$this->_assets = $assets;
	}

	/**
	 * Defines template variables with their values. 
	 * 
	 * Prepares the context to use in the template.
	 * 
	 * @param string $name the name of the template variable 
	 * @param mixed $value the valueof the template variable
	 * @return void
	 */
	public function assign($name, $value) {
		$this->_data->$name = $value;
	}

	/**
	 * Generates the html output of the templates parsing all the template variables. Adds the asset calls if present. 
	 * 
	 * @return the template output (html)
	 */
	public function render() {

		$buffer = '';
		if(count($this->_assets)) 
			foreach($this->_assets as $path=>$type) 
				$buffer = $this->asset($path, $type);

		foreach($this->_data as $k=>$v) $$k=$v;

		ob_start();
		include($this->_tpl);
		$buffer .= ob_get_contents();
		ob_clean();

		return $buffer;

	}

	/**
	 * Returns the javascript code needed to asynchronously load the given css or js 
	 * 
	 * @param string $path the relative file path 
	 * @param string $type the file type (css | js)
	 * @return the asset javascript code
	 */
	protected function asset($path, $type) {
	
		$tag = $type=='css' ? "link" : "script";
		$method = $type=='css' ? "css" : "javascript";
		$id = md5($path);
		
		$buffer = '';

		if(is_readable($path)) {
			$buffer = "<script type=\"text/javascript\">\n";
			$buffer .= "if(typeof $$('".$tag."[id=$id]')[0] == undefined || $$('".$tag."[id=$id]')[0] == null) new Asset.".$method."('".relativePath($path)."', {id: '".$id."'});";
			$buffer .= "</script>";
		}

		return $buffer;
	
	}
}

?>
