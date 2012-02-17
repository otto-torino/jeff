<?php
/**
* \file noaccess.php
* Page shown when the user cannot access the requested content.
*
* @version 0.98
* @copyright 2011 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
* @authors abidibo abidibo@gmail.com
*/

/**
 * absolute path to the ROOT directory 
 */
define('ABS_ROOT', realpath(dirname(__FILE__)));

/**
 * relative path to the ROOT directory
 */
define('ROOT', preg_replace("#".$_SERVER['DOCUMENT_ROOT']."#", "", ABS_ROOT));

/**
 * operating system directory separator 
 */
define( 'DS', DIRECTORY_SEPARATOR );
?>
<html>
<head>
<link type="text/css" rel="stylesheet" href="<?= ROOT ?>/css/main.css" />
</head>
<body>
<div style="text-align:center;padding-top:40px;">
	<div class="noaccess"></div>
	<p>You're not authorized to see the requested content</p>
	<p>Non sei autorizzato a visitare la pagina richiesta</p>
</div>
</body>
</html>
