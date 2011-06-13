<?php

class document {

	private $_registry;
	private $_template;

	function __construct($registry) {

		$this->_registry = $registry;
		$this->_template = $this->getTemplate();

	}

	public function render() {
	
		/* init title, description, etc... */
		$this->initHeadVariables();
		
		$buffer = $this->_template->parse();

		echo $buffer;
	
	}

	private function initHeadVariables() {

		// may be modified later on
		$this->_registry->title = $this->_registry->site_settings->app_title;
		$this->_registry->description = $this->_registry->site_settings->app_description;	
		$this->_registry->language = APP_LANGUAGE;
		$this->_registry->keywords = $this->_registry->site_settings->app_keywords;
		$this->_registry->favicon = ROOT."/favicon.ico";
		$this->_registry->addCss(REL_CSS."/main.css");
		$this->_registry->addCss(REL_CSS."/datepicker_dashboard.css");
		$this->_registry->addCss(REL_CSS."/slimbox.css");
		foreach($this->_registry->theme->getCss() as $csspath) $this->_registry->addCss($csspath);
		foreach($this->_registry->theme->getJs() as $jspath) $this->_registry->addJs($jspath);
		$this->_registry->addJs(REL_JSLIB."/mootools-core-1.3.1-yc.js");
		$this->_registry->addJs(REL_JSLIB."/mootools-more-1.3.1.1-yc.js");
		$this->_registry->addJs(REL_JSLIB."/ajax.js");
		$this->_registry->addJs(REL_JSLIB."/abitools.js");
		$this->_registry->addJs(REL_JSLIB."/html5.js");
		$this->_registry->addJs(REL_JSLIB."/form.js");
		$this->_registry->addJs(REL_JSLIB."/datepicker.js");
		$this->_registry->addJs(REL_JSLIB."/tooltip.js");
		$this->_registry->addJs(REL_JSLIB."/slimbox.js");

	}

	public static function errorMessages() {

		$errorMsg = Error::getErrorMessage();
		return empty($errorMsg) ? '' : "<script>alert('".$errorMsg."');</script>";

	}

	private function getTemplate() {

		return templateFactory::create($this->_registry);
			
	}
}

?>
