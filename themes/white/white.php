<?php

class whiteTheme extends theme implements Itheme {

	function __construct($registry) {
		
		parent::__construct($registry, dirname(__FILE__));

	}
	
	public function getImage() {
		return relativePath(dirname(__FILE__)).'/img/white.jpg';
	}
	
	public function getName() {
		return __("WhiteTheme");
	}
	
	public function getDescription() {
		return __("WhiteThemeDescription");
	}
}

?>
