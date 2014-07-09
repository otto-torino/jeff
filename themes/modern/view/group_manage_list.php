<?php
/**
 * @file /var/www/jeff.git/themes/modern/view/group_manage_list.php
 * @ingroup modern_theme group_module
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
 * @date 2014
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
?>
<section>
<h1 class="section_title"><?= $title ?></h1>
<div>
    <div class="admin-subheader">
        <p class="pull-left"><?= $text ?></p>
        <p class="pull-right"><?= $link_insert ?></p>
        <div class="clearfix"></div>
    </div>
</div>
<div>
<?= $table ?>
</div>
</section>
