<?php
/**
 * @file /var/www/jeff.git/themes/default/view/group_manage_list.php
 * @ingroup default_theme group_module
 * @brief Template containing the backoffice list of the @ref group_module, see @ref groupController::manage
 *
 * Available variables:
 * - **title**: section title
 * - **text**: information text
 * - **link_insert**: anchor link for new groups insertion
 * - **table**: table containing registered groups
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
?>
<section>
<h1 class="section_title"><?= $title ?></h1>
<div>
<?= $text ?>
</div>
<p><?= $link_insert ?></p>
<div>
<?= $table ?>
</div>
</section>
