<label class="<?= (isset($label_class) ? $label_class : '').($required ? " required" : ""); ?>" for="<?= $name ?>"<?= isset($label_form) ? " form=\"".$label_form."\"" : '' ?>>
<?= $label ?>
<?php
if($description) echo "<br/><span class=\"formlabel_exp\">".$description."</span>";
?>
</label>
<?= $formfield , $textadd ?>
<?php echo $more ? $more : ''; ?>
<br class="formRowBreak"/>
