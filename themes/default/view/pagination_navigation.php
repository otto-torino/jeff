<div>
<?php
if(count($pages)) {
	echo "<span>$prev</span> &#160; ";
	foreach($pages as $page) {
		$class = (isset($page['selected']) && $page['selected']) ? "selected" : "";
		if($page['number']=='GAP') echo "<span>...</span> &#160; ";
		else echo "<span class=\"$class\">".(isset($page['link']) ? anchor($page['link'], $page['number']) : $page['number'])."</span> &#160; ";
	}
	echo "<span>$next</span> &#160; ";
}
?>
</div>
