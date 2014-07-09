<?php
/**
 * @file language_choose.php
 * @ingroup modern_theme language_module
 * @brief Template containing the choose language box, see @ref languageController::choose
 *
 * Available variables:
 * - **lngs**: array of language links
 * - **selected**: index of the selected language
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2014
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
?>
<ul class="nav navbar-nav">
    <?php $i = 0; ?>
    <?php foreach($lngs as $lng): ?>
    <li<?= $selected == $i ? ' class="active"' : '' ?>><?= $lng ?></li>
    <?php $i++; ?>
    <?php endforeach ?>
</ul>
