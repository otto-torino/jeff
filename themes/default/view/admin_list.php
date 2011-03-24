<h1 class="section_title">
	<div class="left"><?= $title ?></div>
	<div class="right link"><?= $link_new ?></div>
	<div class="clear"></div>
</h1>

<?php if (count($items)): ?>
<form method="post" action="<?= $formaction ?>">
	<table>
	<?php foreach($items as $item): ?>
		<tr>
			<td><input type="checkbox" name="<?= $check_name ?>" value="<?= $item['id'] ?>"/></td>
			<td><?= $item['link_edit'] ?></td>
		</tr>
	<?php endforeach; ?>
	</table>
	<p><input type="submit" value="<?= $button_label ?>" /></p>
</form>
<?php else: ?>
<p class="no_results"><?= $no_results ?></p> 
<?php endif; ?>

