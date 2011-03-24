<div class="form_multicheck">
<table class="<?= $class ?>">
	<tbody>
		<?php
			foreach($rows as $row) {
				echo "<tr>\n";
				foreach($row as $cell) {
					$cell_tag = (is_array($cell) && isset($cell['header']) && $cell['header']) ? "th" : "td";
					$cell_colspan = (is_array($cell) && isset($cell['colspan']) && $cell['colspan']) ? " colspan=\"".$cell['colspan']."\"" : "";
					$title = (is_array($cell) && isset($cell['title'])) ? " title=\"".$cell['title']."\"" : '';
					$text = (is_array($cell) && isset($cell['text'])) ? $cell['text'] : $cell;
					if(is_array($cell) && isset($cell['class'])) echo "<$cell_tag$cell_colspan$title class=\"".$cell['class']."\">".$text."</$cell_tag>\n";
					else echo "<$cell_tag>$text</$cell_tag>\n";
				}
				echo "</tr>\n";
			}
		?>
	</tbody>
</table>
</div>
<div class="clear"></div>
