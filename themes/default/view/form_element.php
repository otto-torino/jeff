<label class="<?= (isset($label_class) ? $label_class : '').($required ? " required" : ""); ?>" for="<?= $name ?>">
<span class="formlabel"><?= $label ?></span>
<?php
if($required) echo "<span class=\"form_star\">&#160;*</span>";
if($description) echo "<br/><span class=\"formlabel_exp\">".$description."</span>";
?>
</label>
<?= $formfield , $textadd ?>
<?php echo $more ? $more : ''; ?>
<br class="formRowBreak"/>
