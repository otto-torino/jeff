<?php
/**
 * @file menu.php
 * @brief Contains the menu class
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.98
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

require_once("menuVoice.php");

/**
 * @ingroup menu_module
 * @brief Menu class, a wrapper for main site and administrative area menus
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.98
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class menu {

	/**
	 * @brief array of menu voices 
	 */
	public $voices;

	/**
	 * @brief Constructs a menu instance 
	 * 
	 * @param string $id menu type ('main', 'admin')
	 * @return menu instance
	 */
	function __construct($id) {

		$this->_registry = registry::instance();
		$this->voices = array();
		if($id=='admin') $this->initAdminMenu();
		elseif($id=='main') $this->initMainMenu();
	}

	/**
	 * @brief Administrative area menu voices initialization 
	 * 
	 * @return void
	 */
	private function initAdminMenu() {
	
		$this->voices[__("Home")] = ROOT."/";
		$this->voices[__("HomeAdmin")] = ROOT.'/admin/';

		// configuration
		if( 	
			access::check('siteSettings', 1) ||
			access::check('datetimeSettings', 1) ||
			access::check('language', 1)
		) {
			$v = array();
			if(access::check('siteSettings', 1)) $v[__("AppPref")] = $this->_registry->router->linkHref('siteSettings', 'manage');
			if(access::check('datetimeSettings', 1)) $v[__("DatetimePref")] = $this->_registry->router->linkHref('datetimeSettings', 'manage');
			if(access::check('language', 1)) $v[__("Languages")] = $this->_registry->router->linkHref('language', 'manage'); 
			$this->voices[__("Configuration")] = $v;
		}

		// users
		if( 	
			access::check('user', 1) ||
			access::check('group', 1) ||
			access::check('privileges', 1)
		) {
			$v = array();
			if(access::check('user', 1)) $v[__("Users")] = $this->_registry->router->linkHref('user', 'manage');
			if(access::check('group', 1)) $v[__("Groups")] = $this->_registry->router->linkHref('group', 'manage');
			if(access::check('privileges', 1)) $v[__("Permissions")] = $this->_registry->router->linkHref('privilege', 'manage'); 
			$this->voices[__("Users")] = $v;
		}

		// aspect
		if(access::check('layout', 1))
			$this->voices[__("Aspect")] = array(__("Layout") => $this->_registry->router->linkHref('layout', 'manage')); 
		
		// menu
		if(access::check('menu', 1))
			$this->voices[__("Menu")] = $this->_registry->router->linkHref('menu', 'manage');

		$this->voices[__("Logout")] = $this->_registry->router->linkHref('logout', null);
				
	}
	
	/**
	 * @brief Main site menu voices initialization 
	 * 
	 * @return void
	 */
	private function initMainMenu() {
	
		$where = "parent='0'";
		$voices = menuVoice::get(array("where"=>"parent='0'", "order"=>"position"));

		foreach($voices as $v) {

			if(!$v->groups || access::hasGroup(explode(",", $v->groups))) {
				$this->voices[] = array(
					"target"=>$v->target,
					"href"=>preg_match("#^http://#", $v->url) ? $v->url : ROOT.$v->url,
					"label"=>htmlVar($v->label),
					"sub"=>$this->subVoices($v->id)	
				);
			}
		}

	}
	
	/**
	 * @brief Main site menu subvoices 
	 * 
	 * @param int $id parent voice id
	 * @return array parent subvoices
	 */
	private function subVoices($id) {

		$sv = array();

		$svoices = menuVoice::get(array("where"=>"parent='$id'", "order"=>"position"));
		foreach($svoices as $v) {
			$sv[] = array(
				"target"=>$v->target,
				"href"=>preg_match("#^http://#", $v->url) ? $v->url : ROOT.$v->url,
				"label"=>htmlVar($v->label),
				"sub"=>$this->subVoices($v->id)	
			);
		}

		return $sv;
	}

}

?>
