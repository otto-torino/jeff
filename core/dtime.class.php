<?php
/**
 * @file dtime.class.php
 * @brief Contains the datetime class
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup datetime Date and time
 *
 * Set of classes used to deal with date, time and datetime data types
 */

/**
 * @ingroup core datetime
 * @brief Class for the management of date and time strings
 *
 * This class allows you to convert date and time in different formats and centralizes the management of the date and time formats.
 *
 * The formats that can be used are those stored on the database through the datetimeSettings module, or customized formats, 
 * similar to what happens to the class datetime of php.
 *
 * Here are the format characters supported:
 * - \%Y: 4 digits year 
 * - \%m: 2 digits month
 * - \%d: 2 digits day
 * - \%H: 2 digits hour
 * - \%i: 2 digits minute
 * - \%s: 2 digits second
 * - \%F: full month name (January, ..., December)
 * - \%M: 3 digits month name (Jan, ..., Dec)
 * - \%D: 3 digits day name (Mon, ..., Sun)
 * - \%y: 2 digits year
 * - \%U: Unix Timestamp
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class dtime {

	/**
	 * @brief datetimeSettings instance
	 *
	 * Model tied to the database table which stores the date and time settings
	 */
	private $_settings;

	/**
	 * @brief php datetime instance
	 */
	private $_datetime;

	/**
	 * @brief Constructs a dtime instance 
	 * 
	 * @return dtime instance
	 */
	function __construct($registry) {
	
		$this->_settings = new datetimeSettings();

	}

	/**
	 * @brief Returns the current time 
	 * 
	 * @param string $format the format of the outputted date string 
	 * @return the current time in the requested format
	 */
	public function now($format=null) {

		$this->_datetime = new Datetime();

		return $this->parseFormat($format);

	}
	
	/**
	 * 
	 */
	
	/**
	 * @brief Returns the given time in a custom format
	 * 
	 * @param mixed $date date to convert to the given format
	 * @param mixed $format  the format of the outputted date string
	 * @return the time string requested format
	 */
	public function view($date, $format=null) {
	
		if(!$date) return '';

		$this->_datetime = new datetime($date);

		return $this->parseFormat($format);

	}

	/**
	 * @brief Parses the format given by replacing special characters to date values and time  
	 * 
	 * @param mixed $format the format of the outputted date string
	 * @return the time string requested format
	 */
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

	/**
	 * @brief Returns the date or time value corresponding to the special character  
	 * 
	 * @param mixed $matches The special character matching 
	 * @return the date or time value
	 */
	private function applyFormat($matches) {
		
		return $this->_datetime->format($matches[1]);

	}

}

?>
