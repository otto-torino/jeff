<table class="print_button"><tr><td><input type="button" value="stampa" onclick="window.fc.printChart()"/></td></tr></table>
<div id="flowchart"></div>
<script>
	window.fc = new Flowchart('<?= $chart ?>');
	window.fc.start();
</script>

