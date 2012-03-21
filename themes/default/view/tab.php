<?php
/**
 * @file /var/www/jeff.git/themes/default/view/tab.php
 * @ingroup default_theme
 * @brief Template used for displaying tabs
 *
 * Available variables:
 * - **id**: (optional) id attribute
 * - **title**: tab title 
 * - **links**: array of tab links 
 * - **link_selected**: active tab link 
 * - **content**: active tab content 
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
?>
<div class="tabContainer"<?= $id ? " id=\"box_$id\"":"" ?>>
<div class="tabTop">
<div class="left">
<div class="tabTitle tabImgLeft"><?= $title ?></div>
</div>
<div class="right">
<?php krsort($links) ?>
<?php foreach($links as $l): ?>
<div class="tabExt right<?= $link_selected==$l ? " extSelected": "" ?>">
<div class="tabInt left<?= $link_selected==$l ? " intSelected": ""?>"><?= $l ?></div>
</div>
<?php endforeach ?>
</div>
<div class="clear"></div>
</div>
<div class="tabContent"><?= $content ?></div>
</div>
