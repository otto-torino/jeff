<?php
/**
 * @file /var/www/jeff.git/themes/default/view/form_fieldset.php
 * @ingroup default_theme forms
 * @brief Template for the fieldset element used in forms, see @ref form::fieldset
 *
 * Available variables:
 * - **id**: (optional) id attribute
 * - **legend**: legend content
 * - **content**: fieldset content
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
?>
<fieldset<?= $id ? " id=\"$id\"" : null?>>
<legend><?= $legend ?></legend>
<?= $content ?>
</fieldset>
