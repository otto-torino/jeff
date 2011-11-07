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
window.addEvent('domready', function() { var myMenu = new MenuMatic(); });
</script>
