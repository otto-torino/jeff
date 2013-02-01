<?php
/**
 * @file /var/www/jeff.git/themes/default/view/menu_public.php
 * @ingroup default_theme menu_module
 * @brief Template containing the main site menu, see @ref menuController::mainMenu
 *
 * Available variables:
 * - **selected_url**: current url 
 * - **voices**: associative array of menu voices in the form array('link'=>SUB), where SUB may be another array structure or a label 
 *                  (in other words voices contains the tree structure of the menu)
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
?>
<?php
function printVoice($v, $selected_url, $i) {

	$class = $selected_url==$v['href'] ? " class=\"selected\"" : "";
	if(!count($v['sub'])) return "<li".$class."><a href=\"".$v['href']."\" target=\"".$v['target']."\"$class>".$v['label']."</a></li>\n";
	else {
		$buffer = "<li".$class."><a href=\"".$v['href']."\" target=\"".$v['target']."\"$class>".$v['label']."</a><ul>\n";
		foreach($v['sub'] as $sv) $buffer .= printVoice($sv, null, null);
		$buffer .= "</ul></li>\n"; 

		return $buffer;
	}
}
?>
<nav class="main_menu">
<h1 class="hidden">Main menu</h1>
<ul id="nav">
<?php
	$i = 0;
	foreach($voices as $v) {
		echo printVoice($v, $selected_url, $i);		
		$i++;
	}
?>
</ul>
<script>
window.addEvent('load', function() { var myMenu = new MenuMatic(); });
</script>
</nav>

