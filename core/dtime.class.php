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
	
		if(!$date) return '';

		$this->_datetime = new datetime($date);

		return $this->parseFormat($format);

	}

	private function parseFormat($format) {
		
		if($format=='date') $string = $this->_settings->date_format;
		elseif($format=='time') $string = $this->_settings->time_format;
		elseif($format=='datetime') $string = $this->_settings->datetime_format;
		elseif($format) $string = $format;
		else $string = $this->_settings->datetime_format;

		$chars = array(
			"#%(Y)#", // 4 digits year 
			"#%(m)#", // 2 digits month
			"#%(d)#", // 2 digits day
			"#%(H)#", // 2 digits hour
			"#%(i)#", // 2 digits minute
			"#%(s)#", // 2 digits second
			"#%(F)#", // full month name (January, ..., December)
			"#%(M)#", // 3 digits month name (Jan, ..., Dec)
			"#%(D)#", // 3 digits day name (Mon, ..., Sun)
			"#%(y)#", // 2 digits year
			"#%(U)#"  // Unix Timestamp
		);

		$result = preg_replace_callback($chars, array($this, 'applyFormat'), $string);

		return $result;
	}

	private function applyFormat($matches) {
		
		return $this->_datetime->format($matches[1]);

	}

}

?>
