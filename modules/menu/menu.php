<?php

class menu extends model {

	public $voices;

	function __construct($registry, $id) {

		$this->_registry = $registry;
		$this->voices = array();
		if($id=='admin') $this->initAdminMenu();
		elseif($id=='main') $this->initMainMenu();
	}

	private function initAdminMenu() {
	
		$this->voices[__("Home")] = ROOT.'/';
		$this->voices[__("HomeAdmin")] = ROOT.'/admin/';

		// configuration
		if( 	
			access::check($this->_registry, 'siteSettings', 1) ||
			access::check($this->_registry, 'datetimeSettings', 1) ||
			access::check($this->_registry, 'language', 1)
		) {
			$v = array();
			if(access::check($this->_registry, 'siteSettings', 1)) $v[__("AppPref")] = $this->_registry->router->linkHref('siteSettings', 'manage');
			if(access::check($this->_registry, 'datetimeSettings', 1)) $v[__("DatetimePref")] = $this->_registry->router->linkHref('datetimeSettings', 'manage');
			if(access::check($this->_registry, 'language', 1)) $v[__("Languages")] = $this->_registry->router->linkHref('language', 'manage'); 
			$this->voices[__("Configuration")] = $v;
		}

		// users
		if( 	
			access::check($this->_registry, 'user', 1) ||
			access::check($this->_registry, 'group', 1) ||
			access::check($this->_registry, 'privileges', 1)
		) {
			$v = array();
			if(access::check($this->_registry, 'user', 1)) $v[__("AppPref")] = $this->_registry->router->linkHref('user', 'manage');
			if(access::check($this->_registry, 'group', 1)) $v[__("DatetimePref")] = $this->_registry->router->linkHref('group', 'manage');
			if(access::check($this->_registry, 'privileges', 1)) $v[__("Languages")] = $this->_registry->router->linkHref('privilege', 'manage'); 
			$this->voices[__("Users")] = $v;
		}

		// aspect
		if(access::check($this->_registry, 'layout', 1))
			$this->voices[__("Aspect")] = array(__("Layout") => $this->_registry->router->linkHref('layout', 'manage')); 
		$this->voices[__("Logout")] = $this->_registry->router->linkHref('logout', null);

	}
	
	private function initMainMenu() {
	
		$this->voices[__("Home")] = ROOT.'/';
		if(access::check($this->_registry, 'admin_view')) $this->voices[__("HomeAdmin")] = ROOT.'/admin/';
		if($this->_registry->user->id) $this->voices[__("Logout")] = $this->_registry->router->linkHref('logout', null);

	}

}

?>
