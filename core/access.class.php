<?php

class access {

	public static function check($registry, $class=null, $pids=null, $opts=null) {
		
		$user = $registry->user;

		if($class == 'public_view') {
			$class = 'main';
			$pids = $registry->public_view_privilege;
		}
		elseif($class == 'private_view') {
			$class = 'main';
			$pids = $registry->private_view_privilege;
		}
		elseif($class == 'admin_view') {
			$class = 'main';
			$pids = $registry->admin_view_privilege;
		}
		elseif($class == 'admin') {
			$class = 'main';
			$pids = $registry->admin_privilege;
		}

		$user_privileges = array();
		foreach(explode(",", $user->groups) as $ugid) {
			$ug = new group($registry, $ugid);
			if($ug->privileges) {
				foreach(explode(",", $ug->privileges) as $gpid) {
					$p = new privilege($registry, $gpid);
					$user_privileges[$p->class][] = $p->class_id;
				}
			}
		}
		if(!is_array($pids)) $pids = array($pids);

		$access = false;
		foreach($pids as $pid) {
			if((isset($user_privileges[$class]) && in_array($pid, $user_privileges[$class])) || 
				(isset($user_privileges['main']) && in_array($registry->admin_privilege, $user_privileges['main']))) {
				$access = true;
				break;
			}
		}

		if(!$access && gOpt($opts, 'exitOnFailure')) {
		    header("Location: ".$registry->router->linkHref('noaccess', null));
		    exit();
		}
		return $access;

	}

	public static function hasGroup($registry, $group_ids) {
	
		$user = $registry->user;

		foreach($group_ids as $group_id) {
			if(preg_match("#\b$group_id\b#", $user->groups)) return true;
		}

		return false;

	}


}

?>
