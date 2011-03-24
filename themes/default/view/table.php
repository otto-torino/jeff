<table class="<?= $class ?>">
<? if(isset($caption)): ?>
<caption><?= $caption ?></caption>
<? endif ?>
	<thead>
		<tr>
		<?php
			if(isset($heads)) foreach($heads as $h) {
				$class = (is_array($h) && isset($h['class'])) ? " class=\"".$h['class']."\"" : "";
				$text = (is_array($h) && isset($h['text'])) ? $h['text'] : $h;
				echo "<th".$class.">$text</th>";
			}
		?>
		</tr>
	</thead>
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
	<tfoot>
		<tr>
		<?php
			if(isset($foots) && is_array($foots)) foreach($foots as $f) echo "<td>$f</td>";
			elseif(isset($foots)) echo "<td colspan=\"".count($rows[0])."\">$foots</td>";
		?>
		</tr>
	</tfoot>
</table>
