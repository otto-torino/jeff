<?php

function anchor($link, $text, $opts=null) {

	$buffer = "<a href=\"$link\"";
	$buffer .= gOpt($opts, 'over') ? " onmouseover=\"".gOpt($opts, 'over')."\"":"";
	$buffer .= gOpt($opts, 'out') ? " onmouseout=\"".gOpt($opts, 'out')."\"":"";
	$buffer .= gOpt($opts, 'class') ? " class=\"".gOpt($opts, 'class')."\"":"";
	$buffer .= gOpt($opts, 'title') ? " title=\"".gOpt($opts, 'title')."\"":"";
	$buffer .= gOpt($opts, 'target') ? " target=\"".gOpt($opts, 'target')."\"":"";
	$buffer .= ">";
	$buffer .= $text;
	$buffer .= "</a>";

	return $buffer;
}  

function layerWindow($title, $url, $text, $opts=null) {

	return "<span class=\"link\" onclick=\"".layerWindowCall($title, $url, $opts)."\">$text</span>";
}

function layerWindowCall($title, $url, $opts=null) {

	$width = gOpt($opts, 'width', 800);
	$bodyId = gOpt($opts, 'bodyId', 'bid');

	$onclick = "window.myWin = new layerWindow({'title':'$title', 'url':'$url', 'bodyId':'$bodyId', 'width':$width, 'destroyOnClose':true, closeButtonUrl: '".ROOT."/img/icons/ico_close.gif', 'overlay':true});window.myWin.display();";

	return $onclick;

}

function tooltip($label, $title, $text, $opts=null) {
	$class = gOpt($opts, 'class', 'string');
	return "<span class=\"$class tooltip\" title=\"$title::$text\">$label</span>";
}

function clearFloat() {
	return "<div class=\"clear\"></div>";
}

function relativePath($abspath) {

	$path = ROOT.preg_replace("#".preg_quote(ABS_ROOT)."#", "", $abspath);

	if(OS=='win') return preg_replace("#".preg_quote("\\")."#", "/", $path);

	return $path;

}

function gOpt($opts, $name, $dft=null) {

	return isset($opts[$name]) ? $opts[$name] : $dft;

}

function floatcomp($a,$comp,$b,$decimals=2) {
	$res = bccomp($a,$b,$decimals); // php function for comparing floating point numbers with a specified level of precision
	switch ($comp) {
		case ">":
			return ($res==1);
		case ">=":
			return ($res==1 || $res==0);
		case "<":
			return ($res==-1);
		case "<=":
			return ($res==-1 || $res==0);
		default:
		case "==":
			return ($res==0);
	}
}

?>
