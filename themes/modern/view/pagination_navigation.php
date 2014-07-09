<?php
/**
 * @file /var/www/jeff.git/themes/modern/view/pagination_navigation.php
 * @ingroup modern_theme
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
 * @date 2014
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
?>
<ul class="pagination">
<?php
if(count($pages)) {
	echo "<li>$prev</li>";
	foreach($pages as $page) {
		$class = (isset($page['selected']) && $page['selected']) ? "selected" : "";
		if($page['number']=='GAP') echo "<li class=\"disabled\"><a>...</a></li>";
		else echo "<li class=\"".($class == 'selected' ? 'active' : $class)."\">".(isset($page['link']) ? anchor($page['link'], $page['number']) : anchor('', $page['number']))."</li>";
	}
    echo "<li>$next</li>";
    echo "<ul>";
}
?>
</div>
