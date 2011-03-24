<h1 class="section_title"><?= $title ?></h1>
<p style="text-align:right"><a href="#bottom" name="top">bottom</a></p>
<? foreach($blocks as $block): ?>
<div class="block" onclick="<?= $block['link_edit'] ?>">
	<table style="width:100%">
		<tr>
			<td class="blockid"><?= $block['id'] ?></td>
			<td class="block_txt"><?= $block['txt'] ?></td>
			<td class="next"><?= $block['next'] ?></td>
		</tr>
	</table>
</div>
<? endforeach ?>
<? if($link_new_block): ?>
<div class="block_empty" onclick="<?= $link_new_block ?>"></div>
<? endif ?>
<p style="text-align:right"><a href="#top" name="bottom">top</a></p>
