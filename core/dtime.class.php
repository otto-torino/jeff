<?php

class dtime {

	private $_settings;
	private $_datetime;

	function __construct($registry) {
	
		$this->_settings = new datetimeSettings($registry);

	}

	public function now($format=null) {

		$this->_datetime = new Datetime();

		return $this->parseFormat($format);

	}

	public function view($date, $format=null) {
	
		$this->_datetime = new datetime($date);

		return $this->parseFormat($format);

	}

	private function parseFormat($format) {
		
		if($format=='date') $string = $this->_settings->date_format;
		if($format=='time') $string = $this->_settings->time_format;
		else $string = $this->_settings->datetime_format;

		$chars = array("#%(Y)#", "#%(m)#", "#%(d)#", "#%(H)#", "#%(i)#", "#%(s)#");

		$result = preg_replace_callback($chars, array($this, 'applyFormat'), $string);

		return $result;
	}

	private function applyFormat($matches) {
		
		return $this->_datetime->format($matches[1]);

	}

}

?>