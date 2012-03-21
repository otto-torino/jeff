<?php
/**
 * @file /var/www/jeff.git/themes/default/view/form_element.php
 * @ingroup default_theme forms
 * @brief Template for the single form element, see @ref form::prepareView
 *
 * Available variables:
 * - **name**: element name
 * - **label**: label string
 * - **label_class**: (optional) css class of the label tag
 * - **label_form**: (optional) form attribute of the label tag
 * - **required**: (bool) is the field required?
 * - **description**: label description
 * - **form_field**: form input/select/... element
 * - **text_add**: (optional) additional content after field element
 * - **more**: (optional) additional content
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
?>
<label class="<?= (isset($label_class) ? $label_class : '').($required ? " required" : ""); ?>" for="<?= $name ?>"<?= isset($label_form) ? " form=\"".$label_form."\"" : '' ?>>
<?= $label ?>
<?php
if($description) echo "<br/><span class=\"formlabel_exp\">".$description."</span>";
?>
</label>
<?= $formfield , $textadd ?>
<?php echo $more ? $more : ''; ?>
<br class="formRowBreak"/>
