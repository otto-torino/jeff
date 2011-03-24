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
