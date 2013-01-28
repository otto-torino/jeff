<?php
/**
 * @file /var/www/jeff.git/themes/default/view/layout_list.php
 * @ingroup default_theme layout_module
 * @brief Template containing the backoffice list of the @ref layout_module, see @ref layoutController::manage
 *
 * Available variables:
 * - **title**: section title
 * - **text**: information text
 * - **items**: available themes, associative array with keys:
 *   - **active**: (bool) is the active theme?
 *   - **image**: relative path of the theme snapshot
 *   - **name**: theme name
 *   - **description**: theme description
 *   - **link_activate**: link to the activate theme action
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
?>
<section>
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
</section>
