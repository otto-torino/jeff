<?php

class template {

	protected $_registry, $_path, $_mdl_url_content, $_modules;

	function __construct($registry, $tpl_path) {
	
		$this->_registry = $registry;
		$this->_path = $tpl_path;

		return null;

	}

	public function getPath() {

		return $this->_path;

	}
	
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
		$regexp = "/\{([A-Z]+)\}/";
		$buffer = preg_replace_callback($regexp, array($this, 'parseVariables'), $buffer);

		/* insert modules methods contents */	
		$regexp = "/\{module:(\w+) method:(\w+)(\sparams:(\w+))?\}/";
		$buffer = preg_replace_callback($regexp, array($this, 'parseModules'), $buffer);

		return $buffer;
	
	}

	protected function parseVariables($matches) {
		
		$m = $matches[1];

		if($m == 'TITLE') return $this->_registry->title;
		elseif($m == 'DESCRIPTION') return $this->_registry->description;
		elseif($m == 'LANGUAGE') return $this->_registry->language;
		elseif($m == 'KEYWORDS') return $this->_registry->keywords;
		elseif($m == 'FAVICON') return $this->_registry->favicon;
		elseif($m == 'CSS') {
			$r = '';
			foreach(array_unique($this->_registry->css) as $css) 
				$r .= "<link rel=\"stylesheet\" href=\"$css\" type=\"text/css\" />\n";
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

	protected function parseModules($matches) {
		
		return ($matches[1]=='url_module' && $matches[2]=='url_method')
			? $this->_mdl_url_content
			: $this->_modules[$matches[1]."-".$matches[2]."-".(isset($matches[4]) ? $matches[4] : '')];


	}

	protected function chargeModules($matches) {
		
		$params = isset($matches[4]) ? $matches[4] : null;
		if(!($matches[1]=='url_module' && $matches[2]=='url_method')) 
			$this->_modules[$matches[1]."-".$matches[2]."-".$params] = $this->_registry->router->loader(array("module"=>$matches[1], "method"=>$matches[2], "params"=>$params));
		return $matches[0];

	}

}

?>
