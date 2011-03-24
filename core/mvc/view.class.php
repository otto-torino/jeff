<?php

class view {

	protected $obj;
	protected $cpath;
	protected $_view;
	protected $_assets;
	protected $_view_folder;
	protected $_dft_view_folder;
	private $_registry;

	function __construct($registry) {

		$this->_data = new stdClass();
		$this->_registry = $registry;
		$this->_view_folder = $registry->theme->viewPath();
		$this->_css_folder = $registry->theme->cssPath();
		$this->_dft_view_folder = $registry->theme->dftViewPath();
		$this->_dft_css_folder = $registry->theme->dftCssPath();
	}

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

	// $assets -> array("css_path1"=>"css", "css_path2"=>"css", "js_path1"=>"js");
	public function setAssets($assets) {
		$this->_assets = $assets;
	}

	public function assign($name, $value) {
		$this->_data->$name = $value;
	}

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
