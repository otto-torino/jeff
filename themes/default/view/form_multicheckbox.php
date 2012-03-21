<?php
/**
 * @file /var/www/jeff.git/themes/default/view/form_multicheckbox.php
 * @ingroup default_theme forms
 * @brief Template for the multicheckbox form element, see form::multiplecheckbox
 *
 * Available variables:
 * - **class**: css class of the table element
 * - **rows**: table rows, each row contains 2 cells, which may be a string or an associative array, in this case, the available keys are:
 *    - **header** (bool) is the cell an header (th)? 
 *    - **colspan** (int) colspan attribute of the cell 
 *    - **title** title attribute of the cell 
 *    - **text** cell content (label or input checkbox) 
 *    - **class** css class of the cell 
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
?>
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
