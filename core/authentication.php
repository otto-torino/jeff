<?php

class authentication {

	public static function check($registry) {

		if(isset($_GET['login'])) {

		    	$redirect = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $registry->router->linkHref(null, null);

			if(($username=cleanInput('post', 'user', 'string')) && ($password=cleanInput('post', 'password', 'string'))) {
				$user = user::getFromAuth($registry, $username, $password);	
				if(self::checkUser($registry, $user)) {
					$_SESSION['userid'] = $user->id;
					header('Location: '.$redirect);
					exit;
				}
			}	
			
			Error::errorMessage(array("error"=>__("authError")), $redirect);
		}
		elseif(isset($_GET['logout'])) {
			unset($_SESSION);
			session_destroy();
			header('Location: '.$registry->router->linkHref(null, null));
			exit();
		}
		else {
			$registry->user = null;
			$registry->admin = false;

			if(isset($_SESSION['userid'])) {
				$registry->user = new user($registry, $_SESSION['userid']);
				if(access::check($registry, 'main', $registry->admin_privilege)) $registry->admin = true;
			}
			else {
				$registry->user = new StdClass();
				$registry->user->groups = 5;
				$registry->user->id = 0;
			}
		}


	}

	public static function checkUser($registry, $user) {

		if(!$user) return false;

		$registry->user = $user;
		if( ($user && $registry->site=='main') || 
		    ($registry->site=='admin' && access::check($registry, 'main', $registry->admin_view_privilege))) 
		    return true;

		return false;
	}

}

?>
