<?php
/**
 * @file /var/www/jeff.git/themes/modern/view/menu_admin.php
 * @ingroup modern_theme menu_module
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

<?php
    $icons = array(
        1 => 'fa-home',
        2 => 'fa-wrench',
        4 => 'fa-gears',
        5 => 'fa-calendar',
        6 => 'fa-flag',
        8 => 'fa-user',
        9 => 'fa-users',
        10 => 'fa-key',
        12 => 'fa-th-large',
        13 => 'fa-bars',
        14 => 'fa-sign-out',
    );
?>
<ul>
<?php $continue = true; $parsed = $voices; $tree = array(); $last = null; $i = 0; ?>
<?php while ($continue === true): ?>
    <?php $i++; ?>
	<?php if (is_array(current($parsed))): ?>
		<?php $tree[] = $parsed; $parsed = current($parsed); end($parsed); $last = key($parsed); reset($parsed); ?>
	<?php elseif(current($parsed) !== false) : ?>
        <li>
            <a href="<?= current($parsed) ?>">
                <?php if(isset($icons[$i])): ?>
                    <span class="fa fa-3x <?php echo $icons[$i]; ?>"></span>
                <? endif ?>
                <?= key($parsed) ?>
            </a>
        </li>
		<?php if (key($parsed)==$last): ?>
			<?php $parsed = array_pop($tree); ?>
		<?php endif ?>
		<?php next($parsed); ?>
	<?php else: ?>
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
