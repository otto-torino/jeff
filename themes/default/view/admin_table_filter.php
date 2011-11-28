<div class="left" style="width:75%">
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
