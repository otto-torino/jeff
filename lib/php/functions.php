<?php
/**
 * @file functions.php
 * @ingroup php_lib core
 * @brief PHP functions used a bit everywhere 
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup php_lib PHP functions library
 * Set of php functions used by many classes and of general interest.
 */

/**
 * @ingroup localization
 * @brief String localization 
 * 
 * @param string $id string identifier
 * @return the localized string
 */
function __($id) {
	
	$language = isset($_SESSION['lng']) ? $_SESSION['lng'] : 'english';

	if(is_readable(ABS_THEMES.DS.'default'.DS.'languages'.DS.$language.'.php'))
		$lng = include(ABS_THEMES.DS.'default'.DS.'languages'.DS.$language.'.php');
	else $lng = array();

	// registry singleton
	$registry = registry::instance();
	if(isset($registry->theme)) {
		$theme = $registry->theme;
		if(get_class($theme)!= 'defaultTheme')
			if(is_readable($theme->path().DS.'languages'.DS.$language.'.php'))
				$lng = array_merge($lng, include($theme->path().DS.'languages'.DS.$language.'.php'));
	}

	return isset($lng[$id]) ? $lng[$id] : $id;

};

/**
 * @brief Anchor link 
 * 
 * @param string $link link's url
 * @param string $text link's text
 * @param array $opts 
 *   associative array of options:
 *   - **over**: onmouseover attribute
 *   - **out**: onmouseout function
 *   - **class**: css class
 *   - **title**: title attribute
 *   - **target**: target attribute
 * @return the anchor element
 */
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

/**
 * @brief Layer window link 
 *
 * Returns a link which opens a js layer window
 * 
 * @param string $title window title
 * @param string $url content's url 
 * @param string $text link's text
 * @param array $opts 
 *   associative array of options, see @ref layerWindowCall
 * @return the link element
 */
function layerWindow($title, $url, $text, $opts=null) {

	return "<span class=\"link\" onclick=\"".layerWindowCall($title, $url, $opts)."\">$text</span>";
}

/**
 * @brief Javascript code to open a layer window 
 * 
 * @param string $title window title
 * @param string $url content's url
 * @param array $opts 
 *   associative array of options:
 *   - **width**: int default 800. The window width
 *   - **height**: int default null. The window height. If null the window height depends on its contents.
 *   - **bodyId**: string default 'bid'. Id attribute of the window body.
 *   - **reloadZindex**: bool default false. Whether to check the maximum z-index in the document or not. If not the maximum z-index is considered equal to 1.
 * @return the javascript code
 */
function layerWindowCall($title, $url, $opts=null) {

	$width = gOpt($opts, 'width', 800);
	$height = gOpt($opts, 'height', null);
	$bodyId = gOpt($opts, 'bodyId', 'bid');
	$reloadZindex = gOpt($opts, 'reloadZindex', false) ? "true" : "false";

	$height_opt = $height ? " 'height':$height," : '';

	$onclick = "window.myWin = new layerWindow({'title':'$title', 'url':'$url', 'bodyId':'$bodyId', 'width':$width,$height_opt 'destroyOnClose':true, reloadZindex: $reloadZindex, closeButtonUrl: '".ROOT."/img/icons/ico_close.gif', 'overlay':true});window.myWin.display();";

	return $onclick;

}

/**
 * @brief Span element with tooltip 
 * 
 * @param string $label element text
 * @param string $title tooltip title
 * @param string $text tooltip text
 * @param array $opts 
 *   associative array of options:
 *   - **class**: element css class
 * @return the tooltip element
 */
function tooltip($label, $title, $text, $opts=null) {
	$class = gOpt($opts, 'class', '');
	return "<span class=\"$class tooltip\" title=\"$title::$text\">$label</span>";
}

/**
 * @brief Element used to clear css float 
 * 
 * @return the clear float element
 */
function clearFloat() {
	return "<div class=\"clear\"></div>";
}

/**
 * @brief Relative path from absolute path 
 * 
 * @param array $abspath absolute path
 * @return relative path
 */
function relativePath($abspath) {

	$path = ROOT.preg_replace("#".preg_quote(ABS_ROOT)."#", "", $abspath);

	if(OS=='win') return preg_replace("#".preg_quote("\\")."#", "/", $path);

	return $path;

}

/**
 * @brief Retrieve value from associative array by key. 
 *
 * It's also possible to set a default value if the requested key is not set 
 * 
 * @param array $opts associative array
 * @param string $name key name
 * @param mixed $dft default value 
 * @return array value or default
 */
function gOpt($opts, $name, $dft=null) {

	return isset($opts[$name]) ? $opts[$name] : $dft;

}

/**
 * @brief Comparation of floating numbers 
 * 
 * @param float $a first number
 * @param string $comp comparation type
 * @param float $b second number
 * @param int $decimals number of decimals
 * @return comparation result
 */
function floatcomp($a, $comp, $b, $decimals=2) {
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

/**
 * @brief Charge dojo editor 
 *
 * Charges the dojo editor in the elements selected through the given css selector
 * 
 * @param string $selector css selector
 * @return javascript code which charges the editor
 */
function chargeEditor($selector) {

	$buffer = "<script>";
	$buffer .= prepareJsEditor($selector);
	$buffer .= "</script>";

	return $buffer;

}

/**
 * @brief Charges js and css and constructs the function call 
 * 
 * @param string css selector 
 * @return call to the function which charges the editor
 */
function prepareJsEditor($selector) {
	
	$registry = registry::instance();

	$stylesheets = '';
	foreach($registry->css as $css) 
		$stylesheets .= $css.";";
	$stylesheets .= ROOT."/css/dojo_frame.css";

	$registry->addJs("https://ajax.googleapis.com/ajax/libs/dojo/1.6.0/dojo/dojo.xd.js");
	$registry->addJs(ROOT."/lib/js/dojo.js");
	$registry->addCss(ROOT."/css/dojo.css");


	return "chargeEditor('$selector', '$stylesheets')";

}

/**
 * @brief Social sharing 
 * 
 * @param mixed $social 
 *   "all" or an array of social site names. Supported ones are:
 *   - **facebook**
 *   - **twitter**
 *   - **linkedin**
 *   - **digg**
 *   - **googleplus**
 * @param string $url the url to share
 * @param string $title title to share
 * @param string $description description to share
 * @return share links
 */
function share($social, $url, $title=null, $description=null) {

	$registry = registry::instance();

	$ss = new siteSettings($registry);
	$source = $ss->app_title;

	if($social==="all") $social = array("facebook", "twitter", "linkedin", "digg", "googleplus");

	$items = array();
	foreach($social as $s) {
		if($s=='facebook') {
			$items[] = "<a name=\"fb_share\" type=\"button_count\" share_url=\"$url\" href=\"http://www.facebook.com/sharer.php\">Share</a><script src=\"http://static.ak.fbcdn.net/connect.php/js/FB.Share\" type=\"text/javascript\"></script>";	
		}
		elseif($s=='twitter') {
			$items[] = "<a href=\"http://twitter.com/home?status=Currentlyreading ".urlencode($url)."\" title=\""._("condividi su Twitter")."\"><img src=\"".ROOT."/img/share_twitter.jpg\" alt=\"Share on Twitter\"></a>";
		}
		elseif($s=='linkedin') {
			$items[] = "<a href=\"http://www.linkedin.com/shareArticle?mini=true&url=".urlencode($url)."&title=".urlencode($title)."&source=".urlencode($source)."\"><img src=\"".ROOT."/img/share_linkedin.jpg\" alt=\"Share on LinkedIn\"></a>";
		}
		elseif($s=='googleplus') {
			$items[] = "<g:plusone size=\"small\" width=\"90\"></g:plusone><script type=\"text/javascript\">(function() { var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true; po.src = 'https://apis.google.com/js/plusone.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s); })();</script>";
		}
		elseif($s=='digg') {
			$items[] = "<a href=\"http://digg.com/submit?phase=2&amp;url=".$url."&amp;title=".$title."\"><img src=\"".ROOT."/img/share_digg.png\" alt=\"Share on LinkedIn\"></a>";
		}
	}

	$buffer = implode(" ", $items);

	return "<div class=\"share\">".$buffer."</div>";
}

/**
 * @brief Cut html strings without truncating tags 
 * 
 * @param string $html html string
 * @param int $length cut string length
 * @param string $ending cut string ending characters 
 * @param bool $strip_tags whether to remove all html tags or not
 * @param bool $cut_words whether to allow words cut or not
 * @param bool $cut_images whether to remove image tags or not
 * @param array $opts 
 *   associative array of options:
 *   - **endingPosition** "in" or "out". Whether to add the ending characters inside or outside the outer tag
 * @return void
 */
function cutHtmlText($html, $length, $ending, $strip_tags, $cut_words, $cut_images, $opts = null) {
	
	/*
		regular expressions to intercept tags
	*/
	$opened_tag = "<\w+\s*([^>]*[^\/>]){0,1}>";  // i.e. <p> <b> ...
	$closed_tag = "<\/\w+\s*[^>]*>";				// i.e. </p> </b> ...
	$openended_tag = "<\w+\s*[^>]*\/>";			// i.e. <br/> <img /> ...	
	$cutten_tag = "<\w+\s*[^>]*$";				// i.e. <img src="" 
	$reg_expr_img = "/<img\s*[^>]*\/>/is";      
	/* 
		Check: if text is shorter than length (tags excluded) return $html
		with or without tags
	*/
	$reg_expr = "/$opened_tag|$closed_tag|$openended_tag/is";
	$text = preg_replace($reg_expr, '', $html);
	if (strlen($text) <= $length) {
		if(!$strip_tags) {
			if($cut_images) {
				$html = preg_replace($reg_expr_img, "", $html);
			}
			return $html;
		}
		else return $text;
	}
	
	/*
		else if $strip_tags is false...
	*/
	if(!$strip_tags) {
	
		// splits all html-tags to scanable lines
		$reg_expr = "/(<\/?\w+\s*[^>]*\/?>)?([^<>]*)/is";
 		preg_match_all($reg_expr, $html, $lines, PREG_SET_ORDER);
 		/*
 			now 
 			- in $lines[$i] are listed all the matches with the regular expression:
 			  $lines[0]: first match
 			  $lines[1]: second match ...
 			  
 			- $lines[$i][0] contains the wide matching string
 			- $lines[$i][1] contains the matching with (<\/?\w+\s*[^>]*\/?>), that is opened or    
 			  closed ore openclosed tags
 			- $lines[$i][2]contains the matching with ([^<>]*) that is the text inside the tag
 			  or between a tag and another
 		*/
 		$total_length = 0;
 		$tags_opened = array();
  		$partial_html = '';
 		
 		foreach ($lines as $line_matchings) {
    		/*
    			$line_matchings[1] contains tags
    			$line_matchings[2] contains text contained in tags
    			
    			Check: what kind of tag is? open, close, openclose?
    		*/
   			if (!empty($line_matchings[1])) {
   				$strip_this_tag = 0;
   				$reg_expr_oc = "/".$openended_tag."$/is";
   				$reg_expr_o = "/<(\w+)\s*([^>]*[^\/>]){0,1}>$/is";
   				$reg_expr_c = "/<\/(\w+)>$/is";
   				// search img tags
   				if(preg_match($reg_expr_img, $line_matchings[1]) && $cut_images) {
                	$strip_this_tag = 1;
                }
                // search openended tags
                elseif (preg_match($reg_expr_oc, $line_matchings[1])) {
                	// nothing: doesn't encrease the count of characters
                	// and doesn't need a closure
                }
                // search opened tags
                elseif(preg_match($reg_expr_o, $line_matchings[1], $tag_matchings)) {
                	// open tag
                	// add tag to the beginning of $open_tags list
 					array_unshift($tags_opened, strtolower($tag_matchings[1]));
                }
                // search closed tags
                elseif(preg_match($reg_expr_c, $line_matchings[1], $tag_matchings)) {
                	// close tag
                	// delete tag from $open_tags list (as it has been already closed)
                	$pos = array_search($tag_matchings[1], $tags_opened);
  					if ($pos !== false) {
  						unset($tags_opened[$pos]);
  					}
                }
                // add html-tag to $truncate'd text
				if(!$strip_this_tag) $partial_html .= $line_matchings[1];
   				
   			}
   			/*
   				Calculate the lenght of the text inside tags and replace considering html entities one size characters
   			*/
   			$reg_exp_entities = '/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i';
   			$content_length = strlen(preg_replace($reg_exp_entities, ' ', $line_matchings[2]));
   			
   			if ($total_length+$content_length> $length) {
   			
   				$left = $length - $total_length;
   				$entities_length = 0;
   				
   				// search for html entities (l'entities conta come un carattere, ma nell'html ne uccupa di più, quindi dobbiamo fare in modo di includere completament l'entities, cioè il suo codice e contarlo interamente come un singolo carattere: scaliamo uno da $left ed aggiungiamo $entities_length all alunghezza della substring)
				if(preg_match_all($reg_exp_entities, $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
					// calculate the real length of all entities in the legal range
					foreach ($entities[0] as $entity) {
						if ($entity[1]+1-$entities_length <= $left) {
							$left--;
							$entities_length += strlen($entity[0]);
						}
						else {
							// no more characters left
							break;
						}
					}
				}
				
				$partial_html .= substr($line_matchings[2], 0, $left+$entities_length);
				// maximum lenght is reached, so get off the loop
  				break;
				  			
   			}
   			else {
				$partial_html .= $line_matchings[2];
  				$total_length += $content_length;
			}
   			
   			// if the maximum length is reached, get off the loop
			if($total_length>= $length) break;

		}
	}
	else {
		// onlly pure text
     		$partial_html = substr($text, 0, $length);
	}
	
	// if the words shouldn't be cut in the middle...
    	if (!$cut_words) {
       		//search the last occurance of a space or an end tag
       		$spacepos = strrpos($partial_html, ' ');
       		$endtagpos = strrpos($partial_html, '>');
       		if(isset($spacepos) || isset($endtagpos)) {
       			//cut the text in this position
       			$cutpos = ($spacepos<$endtagpos)? ($endtagpos+1) : $spacepos;
       			$partial_html = substr($partial_html, 0, $cutpos);
       		}
    	}
	
	if(gOpt($opts, 'endingPosition') == 'in')
		$partial_html .= $ending;

	/*
		close all opened tags (if strip tags is false)
	*/
	if(!$strip_tags) 
    		// close all unclosed html tags
    		foreach ($tags_opened as $tag) 
    			$partial_html .= '</' . $tag . '>';
	
	// add the ending characters to the partial text
	if(gOpt($opts, 'endingPosition') != 'in')
		$partial_html .= $ending;
   
    	return $partial_html;	

}

/**
 * @brief Sanitizes strings for url and filenames 
 * 
 * @param string $string the string to sanitize
 * @param bool $is_filename whether the string has to be used as a filename or not
 * @return the sanitized string
 */
function sanitize($string = '', $is_filename = false) {
 	
	// Replace all weird characters with dashes
	 $string = preg_replace('/[^\w\-'. ($is_filename ? '~_\.' : ''). ']+/u', '-', $string);

	 // Only allow one dash separator at a time (and make string lowercase)
	 return mb_strtolower(preg_replace('/--+/u', '-', $string), 'UTF-8');

}


?>
