<?php

interface Itheme {

	public function name();
	public function path();
	public function dftPath();
	public function viewPath();
	public function dftViewPath();
	public function getTemplate();
	public function setTpl($tpl);
	public function getCss();
	public function getJs();

}

?>
