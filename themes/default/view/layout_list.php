<h1 class="section_title"><?= $title ?></h1>
<p><?= $text ?></p>
<table class="generic" style="width:100%">
	<tr>
		<th style="width:130px"><?= __("Preview") ?></th><th><?= __("Name") ?></th><th><?= __("Description") ?></th><th class="noBorder noBkg"></th>
	</tr>
<? foreach($items as $item): ?>
	<tr>
		<td style="text-align:center">
			<a href="<?= $item['image'] ?>" title="<?= $item['name'] ?>" rel="lightbox"><img style="height:60px" src="<?= $item['image'] ?>" alt="screenshot"/></a>
		</td>
		<td><?= $item['name'] ?></td>
		<td><?= $item['description'] ?></td>
		<td class="noBorder"><?= $item['active'] ? __("ACTIVE") : "<input type='button' value='".__("activate")."' onclick=\"location.href='".$item['link_activate']."'\" />" ?></td>
	</tr>
<? endforeach ?>
</table>
<script>
var ll_cerabox = new CeraBox(); ll_cerabox.addItems($$('a[rel=lightbox]'), {group: false});
</script>
