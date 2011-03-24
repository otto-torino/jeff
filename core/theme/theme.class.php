<?php

require_once('interface.theme.php');

class theme {

	protected $_registry, $_tpl_name, $_tpl, $_dft_theme;

	function __construct($registry, $theme_name) {
		$this->_dft_theme = 'default';
		$this->_registry = $registry;
		$this->_name = $theme_name;
	}
	
	public function name() {
		
		return $this->_name;

	}

	public function path() {
		
		return ABS_THEMES.DS.$this->_name;

	}

	public function dftPath() {
		
		return ABS_THEMES.DS.$this->_dft_theme;

	}

	public function viewPath() {
		
		return ABS_THEMES.DS.$this->_name.DS."view";

	}

	public function dftViewPath() {
		
		return ABS_THEMES.DS.$this->_dft_theme.DS."view";

	}

	public function cssPath() {
		
		return ABS_THEMES.DS.$this->_name.DS."css";

	}

	public function dftCssPath() {
		
		return ABS_THEMES.DS.$this->_dft_theme.DS."css";

	}
	public function getTemplate() {

		return $this->_tpl;
	
	}

	public function setTpl($tpl) {

		$this->_tpl_name = $tpl;

		if(is_readable($this->path().DS.$tpl.".tpl"))
			$tpl_path = $this->path().DS.$tpl.".tpl";
		elseif(is_readable($this->dftPath().DS.$tpl.".tpl"))
			$tpl_path = $this->dftPath().DS.$tpl.".tpl";
		else $tpl_path = null;

		$this->_tpl = $tpl_path ? new template($this->_registry, $tpl_path) : null;

	}

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
