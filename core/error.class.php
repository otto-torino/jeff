<?php
/**
 * @file error.class.php
 * @brief Contains the error class
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup errors Errors management
 *
 * <p>System errors and user errors management</p>
 */

/**
 * @ingroup core errors
 *
 * @brief Abstract class for the management of system errors, errors due to wrong actions of the user, warning messages  
 *
 * System errors are displayed as independent pages, and the information shown depends on the value of the DEBUG setting in the configuration file.
 *
 * Errors and warnings are stored in a session variable and shown after url redirecting.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
abstract class Error {

	/**
	 * @brief List of system default errors coded by int keys 
	 * 
	 * @static
	 * @return array dictionary of default errors
	 */
	public static function codeMessages() {

		return	array(
			1=>__("compulsoryFieldsError"),
		);

	}

	/**
	 * @brief Management of system errors
	 *
	 * If the DEBUG setting in the configuration.php is set to true displays the error with all the information, 
	 * otherwise displays a custom message. 
	 * 
	 * @param string $class the class which triggers the error
	 * @param string $function the function which triggers the error
	 * @param string $message the error message
	 * @param int $line the line number where the error occurs
	 * @param string $noDebugMsg the message to show if DEBUG mode is disabled
	 * @return prints the system error
	 */
	public static function syserrorMessage($class, $function, $message, $line, $noDebugMsg=null) {

		@ob_clean();
		ob_start();
		$buffer = "<html>\n";
		$buffer .= "<head>\n";
		$buffer .= "<style type=\"text/css\">";
		$buffer .= "@import url('".REL_CSS."/syserror.css');";
		$buffer .= "</style>\n";
		$buffer .= "</head>\n\n";
		$buffer .= "<body>\n";
		$buffer .= "<div>\n";
		$buffer .= "<div id=\"errorImg\">\n";
		$buffer .= "</div>\n";
		$buffer .= "<table border=\"1\">\n";
		$buffer .= "<tr>";
		if(DEBUG) {
			$buffer .= "<th>".__("Class/File")."</th><th>".__("Function")."</th><th>".__("Message")."</th>";
			if($line) $buffer .= "<th>".__("Line")."</th>";
			$buffer .= "</tr>\n";
			$buffer .= "<tr>";
			$buffer .= "<td>".$class."</td><td>".$function."</td><td>".$message."</td>";
			if($line) $buffer .= "<td>".$line."</td>";
		}
		else {
			if($noDebugMsg) $buffer .= "<td>".$noDebugMsg."</td>";
		}
		$buffer .= "</tr>\n";
		$buffer .= "</table>\n";
		$buffer .= "</div>\n";
		$buffer .= "</body>\n";
		$buffer .= "</html>";

		echo $buffer;
		ob_end_flush();
		exit();

	}
	
	/**
	 * @brief Saves the error in the active session and redirect to the given url (where the error will be shown) 
	 * 
	 * @param mixed $message 
	 *   the error message. Possible values are:
	 *   - integer: the message is taken from the default errors dictionary
	 *   - associative array in the form array('error'=>'error_message', 'hint'=>'error_hint')
	 * @param mixed $link the redirection url 
	 * @return redirects to the given url
	 */
	public static function errorMessage($message, $link) {

		ob_clean();
		$codeMessages = self::codeMessages();
		
		$msg = (is_int($message['error']))? $codeMessages[$message['error']]:$message['error'];

		$buffer = __("Error").": ";
		$buffer .= " ".jsVar($msg)."\\n";
		if(isset($message['hint'])) {
			$buffer .= __("Hints").":";
			$buffer .= " ".jsVar($message['hint']);
		}
		$_SESSION['ERRORMSG'] = $buffer;

		header("Location: $link");
		exit();

	}
	
	/**
	 * @brief Saves the warning in the active session and redirect to the given url (where the warning will be shown) 
	 * 
	 * @param mixed $message 
	 *   associative array in the form array('warning'=>'warning_message', 'hint'=>'warning_hint')
	 * @param mixed $link the redirection url 
	 * @return redirects to the given url
	 */
	public static function warningMessage($message, $link) {

		ob_clean();

		$msg = $message['warning'];

		$buffer = __("Attention!");
		$buffer .= " ".jsVar($msg)."\\n";
		if(isset($message['hint'])) {
			$buffer .= __("Hints").":";
			$buffer .= " ".jsVar($message['hint']);
		}
		$_SESSION['ERRORMSG'] = $buffer;

		header("Location: $link");
		exit();

	}

	/**
	 * @brief Gets the error message from the active session 
	 * 
	 * @return the error message
	 */
	public static function getErrorMessage() {

		$errorMsg = (isset($_SESSION['ERRORMSG']))?$_SESSION['ERRORMSG']:"";
		if(isset($_SESSION['ERRORMSG'])) unset($_SESSION['ERRORMSG']);

		return $errorMsg;

	}


}

?>
