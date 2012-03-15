<?php

/*
 * Factory Class which creates concrete templates objects
 */
abstract class templateFactory {
	
	public static function create($registry) {

		if($registry->site == 'admin') {
			$tpl = access::check('main', $registry->admin_view_privilege) ? "admin_private" : "admin_public";
		}
		elseif($registry->site == 'main') {
			if($registry->user->id) $tpl = $registry->isHome ? "home_private" : "page_private";
			elseif($registry->isHome) $tpl = 'home_public'; 
			else $tpl = 'page_public';
		}

		$registry->theme->setTpl($tpl);

		$tplObj = $registry->theme->getTemplate();

		if($tplObj) return $tplObj;
		else
			Error::syserrorMessage('templateFactory', 'create', sprintf(__("CantChargeTplError"), $tpl), __LINE__);

	}

}

?>
