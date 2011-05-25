<?php

class defaultTheme extends theme implements Itheme {

	function __construct($registry) {
		
		parent::__construct($registry, 'default');

	}
	
	public function getImage() {
		return relativePath(dirname(__FILE__)).'/img/default.jpg';
	}
	
	public function getName() {
		return __("DefaultTheme");
	}
	
	public function getDescription() {
		return __("DefaultThemeDescription");
	}
}

?>
