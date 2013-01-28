<?php
/**
 * @file /var/www/jeff.git/themes/default/view/admin_table_filter.php
 * @ingroup default_theme backoffice
 * @brief Template of the admin list view with filters (list of model items in back office), see @ref adminTable::view
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
 * - **form_filters**: filters form
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
?>
<div class="left" style="width:75%">
<?= $backoffice_text ?>
<p><?= $link_insert ?></p>
<?= $formstart ?>
<?= $input_where_query ?>
<?= $table ?>
<?= $input_edit?> <?= $input_delete ?> <?= $input_export_selected ?> <?= $input_export_all ?>
<?= $formend ?>
<div class="left"><?= $psummary ?></div><div class="right"><?= $pnavigation ?></div>
<div class="clear"></div>
</div>
<div class="right" style="width:23%">
<h2><?= $form_filters_title ?> <span style="cursor:help" title="<?= __('FiltersTooltip') ?>" class="tooltip">?</span></h2>
	<div class="at_filters"><?= $form_filters ?></div>
</div>
<div class="clear"></div>
