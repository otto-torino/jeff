<?php

class Error {

	public static function codeMessages() {

		return	array(
			1=>__("compulsoryFieldsError"),
		);

	}

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

	public static function getErrorMessage() {

		$errorMsg = (isset($_SESSION['ERRORMSG']))?$_SESSION['ERRORMSG']:"";
		if(isset($_SESSION['ERRORMSG'])) unset($_SESSION['ERRORMSG']);

		return $errorMsg;

	}


}

?>
