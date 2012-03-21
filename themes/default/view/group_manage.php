<?php
/**
 * @file /var/www/jeff.git/themes/default/view/group_manage.php
 * @ingroup default_theme group_module
 * @brief Template containing the backoffice forms of the @ref group_module, see @ref groupController::manageGroup
 *
 * Available variables:
 * - **title**: section title
 * - **form**: insertion or modification form
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
<?= $form ?>
</div>
</section>
