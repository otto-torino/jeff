<h1 class="section_title"><?= $title ?></h1>
<p><?= $text ?></p>
<table class="generic wide">
	<tr>
		<th style="width:130px"><?= __("Preview") ?></th><th><?= __("Name") ?></th><th><?= __("Description") ?></th><th class="noBorder noBkg"></th>
	</tr>
<? foreach($items as $item): ?>
	<tr<?= $item['active'] ? " class=\"selected\"" : "" ?>>
		<td style="text-align:center">
			<a href="<?= $item['image'] ?>" title="<?= $item['name'] ?>" rel="lightbox"><img style="height:60px" src="<?= $item['image'] ?>" alt="screenshot"/></a>
		</td>
		<td><?= $item['name'] ?></td>
		<td><?= $item['description'] ?></td>
		<td class="noBorder noBkg" style="padding:23px 0 0 10px;"><?= $item['active'] ? "" : "<input type='button' value='".__("activate")."' onclick=\"location.href='".$item['link_activate']."'\" />" ?></td>
	</tr>
<? endforeach ?>
</table>
<script>
$$('a[rel=lightbox]').cerabox({group: false});
</script>
