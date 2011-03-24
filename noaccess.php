<?php
define('ABS_ROOT', realpath(dirname(__FILE__)));
define('ROOT', preg_replace("#".$_SERVER['DOCUMENT_ROOT']."#", "", ABS_ROOT));
define( 'DS', DIRECTORY_SEPARATOR );
?>
<html>
<head>
<link type="text/css" rel="stylesheet" href="<?= ROOT ?>/css/main.css" />
</head>
<body>
<div style="text-align:center;padding-top:40px;">
	<div class="noaccess"></div>
	<p>Non sei autorizzato a visitare la pagina richiesta</p>
</div>
</body>
</html>
