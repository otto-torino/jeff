<?php
/**
 * @file document.class.php
 * @brief Contains the document class
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup core
 * @brief Class which renders the html document
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class document {

	/**
	 * @brief the registry singleton instance 
	 */
	private $_registry;
	
	/**
	 * @brief the active template instance 
	 */
	private $_template;
	
	/**
	 * @brief Constructs the document instance 
	 * 
	 * Retrieves the template instance to use. The template object is created by a factory class which returns the proper
	 * template instance depending on the requested url.
	 *
	 * The template instance represents a global template, the one which contains 
	 * the whole html code and the modules views which may be considered as local templates. 
	 * 
	 * @return document instance
	 */
	function __construct() {

		$this->_registry = registry::instance();
		$this->_template = $this->getTemplate();

	}

	/**
	 * @brief Rendering of the whole document 
	 * 
	 * @return prints the whole document
	 */
	public function render() {
	
		/* init title, description, etc... */
		$this->initHeadVariables();
		
		$buffer = $this->_template->parse();

		echo $buffer;
	
	}

	/**
	 * @brief Initialization of some registry properties used in the head tag    
	 * 
	 * @return void
	 */
	private function initHeadVariables() {

		// may be modified later on
		$this->_registry->title = $this->_registry->site_settings->app_title;
		$this->_registry->description = $this->_registry->site_settings->app_description;	
		$this->_registry->language = APP_LANGUAGE;
		$this->_registry->keywords = $this->_registry->site_settings->app_keywords;
		$this->_registry->favicon = ROOT."/favicon.ico";
		$this->_registry->addCss(REL_CSS."/main.css");
		$this->_registry->addCss(REL_CSS."/datepicker_dashboard.css");
		$this->_registry->addCss(REL_CSS."/cerabox.css");
		foreach($this->_registry->theme->getCss() as $csspath) $this->_registry->addCss($csspath);
		foreach($this->_registry->theme->getJs() as $jspath) $this->_registry->addJs($jspath);
		$this->_registry->addJs(REL_JSLIB."/mootools-core-yc.js");
		$this->_registry->addJs(REL_JSLIB."/mootools-more-yc.js");
		$this->_registry->addJs(REL_JSLIB."/modernizr.js");
		$this->_registry->addJs(REL_JSLIB."/ajax.js");
		$this->_registry->addJs(REL_JSLIB."/abitools.js");
		$this->_registry->addJs(REL_JSLIB."/form.js");
		$this->_registry->addJs(REL_JSLIB."/datepicker.js");
		$this->_registry->addJs(REL_JSLIB."/tooltip.js");
		$this->_registry->addJs(REL_JSLIB."/cerabox.js");

	}

	/**
	 * @brief Alert system errors if present 
	 * @ingroup errors
	 * 
	 * @return the javascript code which alerts the error
	 */
	public static function errorMessages() {

		$errorMsg = Error::getErrorMessage();
		return empty($errorMsg) ? '' : "<script>alert('".$errorMsg."');</script>";

	}

	/**
	 * @brief Retrieves the template object from the template factory 
	 * 
	 * @return the @ref template instance
	 */
	private function getTemplate() {

		return templateFactory::create();
			
	}

}

?>
