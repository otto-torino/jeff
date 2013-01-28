<?php
/**
 * @file /var/www/jeff.git/themes/default/view/pagination_navigation.php
 * @ingroup default_theme
 * @brief Template containing the page navigation links, see @ref pagination::navigation
 *
 * Available variables:
 * - **pages**: array containing the first page, the current page with optional number of next pages, the last page and the GAPs in the form of an associative array, the keys are:
 *   - **selected**: is the current page?  
 *   - **number**: the page number or the string 'GAP'
 *   - **link**: the page url link
 * - **prev**: previous page anchor link 
 * - **next**: next page anchor link 
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
?>
<div>
<?php
if(count($pages)) {
	echo "<span>$prev</span> &#160; ";
	foreach($pages as $page) {
		$class = (isset($page['selected']) && $page['selected']) ? "selected" : "";
		if($page['number']=='GAP') echo "<span>...</span> &#160; ";
		else echo "<span class=\"$class\">".(isset($page['link']) ? anchor($page['link'], $page['number']) : $page['number'])."</span> &#160; ";
	}
	echo "<span>$next</span> &#160; ";
}
?>
</div>
