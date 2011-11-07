<?php

function __($id) {
	
	$language = isset($_SESSION['lng']) ? $_SESSION['lng'] : 'english';

	if(is_readable(ABS_THEMES.DS.'default'.DS.'languages'.DS.$language.'.php'))
		$lng = include(ABS_THEMES.DS.'default'.DS.'languages'.DS.$language.'.php');
	else $lng = array();

	if(isset($_SESSION['theme'])) {
		$theme = $_SESSION['theme'];
		if(get_class($theme)!= 'defaultTheme')
			if(is_readable($theme->path().DS.'languages'.DS.$language.'.php'))
				$lng = array_merge($lng, include($theme->path().DS.'languages'.DS.$language.'.php'));
	}

	return isset($lng[$id]) ? $lng[$id] : $id;

};

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
	$height = gOpt($opts, 'height', null);
	$bodyId = gOpt($opts, 'bodyId', 'bid');
	$reloadZindex = gOpt($opts, 'reloadZindex', false) ? "true" : "false";

	$height_opt = $height ? " 'height':$height," : '';

	$onclick = "window.myWin = new layerWindow({'title':'$title', 'url':'$url', 'bodyId':'$bodyId', 'width':$width,$height_opt 'destroyOnClose':true, reloadZindex: $reloadZindex, closeButtonUrl: '".ROOT."/img/icons/ico_close.gif', 'overlay':true});window.myWin.display();";

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

function chargeEditor($registry, $selector) {

	$stylesheets = '';
	foreach($registry->css as $css) 
		$stylesheets .= $css.";";
	$stylesheets .= ROOT."/css/dojo_frame.css";

	$registry->addJs("https://ajax.googleapis.com/ajax/libs/dojo/1.6.0/dojo/dojo.xd.js");
	$registry->addJs(ROOT."/lib/js/dojo.js");
	$registry->addCss(ROOT."/css/dojo.css");


	$buffer = "<script>";
	$buffer .= "var dojo_textareas = [];";
	$buffer .= "dojo.ready(function(){
		      	var textareas = dojo.query(\"$selector\");
  			if(textareas && textareas.length){
    				dojo.addClass(dojo.body(), \"claro\");
				for(var i=0; i<textareas.length; i++) {
					var textarea = textareas[i];
					var key = $(textarea).getParents('form')[0].get('name')+'_'+$(textarea).get('id');
					dojo_textareas[key] = new dijit.Editor({
      					styleSheets: \"$stylesheets\",
      					plugins: [
        					\"collapsibletoolbar\",
        					\"fullscreen\", \"viewsource\", \"|\",
        					\"undo\", \"redo\", \"|\",
        					\"cut\", \"copy\", \"paste\", \"|\",
        					\"bold\", \"italic\", \"underline\", \"strikethrough\", \"|\",
        					\"insertOrderedList\", \"insertUnorderedList\", \"indent\", \"outdent\", \"||\",
        					\"formatBlock\", \"fontName\", \"fontSize\", \"||\",
        					\"findreplace\", \"insertEntity\", \"blockquote\", \"|\",
        					\"createLink\", \"insertImage\", \"insertanchor\", \"|\",
       					 	\"foreColor\", \"hiliteColor\", \"|\",
       	 					\"showblocknodes\", \"pastefromword\",
        					// headless plugins
        					\"normalizeindentoutdent\", \"prettyprint\",
        					\"autourllink\", \"dijit._editor.plugins.EnterKeyHandling\"
      					]
    					}, textareas[i]);
				}
  			}
		});";
	$buffer .= "</script>";

	return $buffer;

}

?>
