<?php
/**
 * @file template.class.php
 * @brief Contains the template class
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup templates core
 * @brief Class used to manage and parse templates
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class template {

	/**
	 * @brief the @ref registry singleton instance 
	 */
	protected $_registry;
	
	/**
	 * @brief template file path 
	 */
	protected $_path;
	
	/**
	 * @brief content outputted by the method called through url 
	 */
	protected $_mdl_url_content;
	
	/**
	 * @brief array containing contents outputted by the modules called in the template 
	 */
	protected $_modules;

	/**
	 * @brief Constructs a template instance 
	 * 
	 * @param string $tpl_path template file path
	 * @return void
	 */
	function __construct($tpl_path) {
	
		$this->_registry = registry::instance();
		$this->_path = $tpl_path;
	}

	/**
	 * @brief Template file getter 
	 * 
	 * @return string template file path
	 */
	public function getPath() {

		return $this->_path;

	}
	
	/**
	 * @brief Template parser 
	 *
	 * Parses the tempate file and replaces variables and module's outputs
	 * 
	 * @return void
	 */
	public function parse() {
		
		if(!is_readable($this->getPath()))
			Error::syserrorMessage('template', 'parse', sprintf(__("TplNotFound"), $this->getTplPath()), __LINE__);

		$tplContent = file_get_contents($this->getPath());

		/* charge/exec url module-method content */
		if(preg_match("#\{module:url_module method:url_method\}#", $tplContent))
			$this->_mdl_url_content = $this->_registry->router->loader(null);
		
		/* precharge modules methods so that registry properties are setted*/
		$regexp = "/\{module:(\w+) method:(\w+)(\sparams:(\w+))?\}/";
		$buffer = preg_replace_callback($regexp, array($this, 'chargeModules'), $tplContent);

		/* parse template sobstituting variables charged in the registry */
		$regexp = "/\{([A-Z_]+)\}/";
		$buffer = preg_replace_callback($regexp, array($this, 'parseVariables'), $buffer);

		/* insert modules methods contents */	
		$regexp = "/\{module:(\w+) method:(\w+)(\sparams:(\w+))?\}/";
		$buffer = preg_replace_callback($regexp, array($this, 'parseModules'), $buffer);

		return $buffer;
	
	}

	/**
	 * @brief Variables parser 
	 * 
	 * @param array $matches regexp matches
	 * @return void
	 */
	protected function parseVariables($matches) {
		
		$m = $matches[1];

		if($m == 'TITLE') return $this->_registry->title;
		elseif($m == 'DESCRIPTION') return $this->_registry->description;
		elseif($m == 'LANGUAGE') return $this->_registry->language;
		elseif($m == 'KEYWORDS') return $this->_registry->keywords;
		elseif($m == 'FAVICON') return $this->_registry->favicon;
		elseif($m == 'META') {
			$r = '';
			foreach($this->_registry->meta as $meta) { 
				$r .= "<meta"
					.(isset($meta['name']) ? " name=\"".$meta['name']."\"" : '')
					.(isset($meta['property']) ? " property=\"".$meta['property']."\"" : '')
					." content=\"".$meta['content']."\" />\n";
			}
			return $r;
		}
		elseif($m == 'CSS') {
			$r = '';
			foreach(array_unique($this->_registry->css) as $css) 
				$r .= "<link rel=\"stylesheet\" href=\"$css\" type=\"text/css\" />\n";
			return $r;
		}
		elseif($m == 'HEAD_LINKS') {
			$r = '';
			foreach($this->_registry->head_links as $hlink) { 
				$r .= "<link"
					.(isset($hlink['rel']) ? " rel=\"".$hlink['rel']."\"" : '')
					.(isset($hlink['type']) ? " type=\"".$hlink['type']."\"" : '')
					.(isset($hlink['title']) ? " title=\"".$hlink['title']."\"" : '')
					." href=\"".$hlink['href']."\" />\n";
			}
			return $r;
		}
		elseif($m == 'JAVASCRIPT') {
			$r = '';
			foreach(array_unique($this->_registry->js) as $js) 
				$r .= "<script type=\"text/javascript\" src=\"$js\"></script>\n";
			return $r;
		}

		elseif($m == 'ERRORS') return document::errorMessages();

	}

	/**
	 * @brief modules parser 
	 * 
	 * @param array $matches regexp matches 
	 * @return void
	 */
	protected function parseModules($matches) {
		
		return ($matches[1]=='url_module' && $matches[2]=='url_method')
			? $this->_mdl_url_content
			: $this->_modules[$matches[1]."-".$matches[2]."-".(isset($matches[4]) ? $matches[4] : '')];


	}

	/**
	 * @brief Charges modules outputs
	 * 
	 * @param array $matches regexp matches
	 * @return void
	 */
	protected function chargeModules($matches) {
		
		$params = isset($matches[4]) ? $matches[4] : null;
		if(!($matches[1]=='url_module' && $matches[2]=='url_method')) 
			$this->_modules[$matches[1]."-".$matches[2]."-".$params] = $this->_registry->router->loader(array("module"=>$matches[1], "method"=>$matches[2], "params"=>$params));
		return $matches[0];

	}

}

?>
