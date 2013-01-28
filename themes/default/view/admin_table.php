<?php
/**
 * @file /var/www/jeff.git/themes/default/view/admin_table.php
 * @ingroup default_theme backoffice
 * @brief Template of the admin list view (list of model items in back office), see @ref adminTable::view
 *
 * Available variables:
 * - **backoffice_text**: Introduction text
 * - **link_insert**: anchor element redirecting to the insertion form
 * - **formstart**: form beginning tag
 * - **formend**: form ending tag
 * - **input_edit**: edit selected button
 * - **input_delete**: delete selected button
 * - **input_where_query**: hidden field for exportation purposes
 * - **input_export_selected**: export selected button
 * - **input_export_all**: export all button
 * - **psummary**: pagination summary
 * - **pnavigation**: pagination navigation links
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
?>
<div>
<?= isset($backoffice_text) ? $backoffice_text : '' ?>
<p><?= $link_insert ?></p>
<?= $formstart ?>
<?= $input_where_query ?>
<?= $table ?>
<?= $input_edit?> <?= $input_delete ?> <?= $input_export_selected ?> <?= $input_export_all ?>
<?= $formend ?>
<div class="left"><?= $psummary ?></div><div class="right"><?= $pnavigation ?></div>
<div class="clear"></div>
</div>
