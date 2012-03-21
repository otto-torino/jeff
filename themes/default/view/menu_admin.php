<?php
/**
 * @file /var/www/jeff.git/themes/default/view/menu_admin.php
 * @ingroup default_theme menu_module
 * @brief Template containing the menu of the administrative area, see @ref menuController::adminMenu
 *
 * Available variables:
 * - **voices**: associative array of menu voices in the form array('link'=>SUB), where SUB may be another array structure or a label 
 *                  (in other words voices contains the tree structure of the menu)
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
?>
<ul id="nav">
<?php $continue = true; $parsed = $voices; $tree = array(); $last = null; ?>
<?php while ($continue === true): ?>
	<?php if (is_array(current($parsed))): ?>
		<li><a href="#"><?= key($parsed) ?></a>
		<ul>
		<?php $tree[] = $parsed; $parsed = current($parsed); end($parsed); $last = key($parsed); reset($parsed); ?>
	<?php elseif(current($parsed) !== false) : ?>
		<li><a href="<?= current($parsed) ?>"><?= key($parsed) ?></a></li>
		<?php if (key($parsed)==$last): ?>
			</ul></li>
			<?php $parsed = array_pop($tree); ?>
		<?php endif ?>
		<?php next($parsed); ?>
	<?php else: ?>
		</ul></li>
		<?php $parsed = array_pop($tree);next($parsed); ?>
	<?php endif ?>
	<?php if (count($tree)==0 && current($parsed)==false): ?>
		<?php $continue = false; ?>
	<?php endif ?>
<?php endwhile ?>
</ul>
<script>
window.addEvent('load', function() { var myMenu = new MenuMatic('nav'); });
</script>
