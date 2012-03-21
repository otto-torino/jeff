<?php
/**
 * @file noaccess.php
 * @brief No access page
 *
 * Page shown when the user cannot access the requested content.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
*/

/**
 * @brief absolute path to the ROOT directory 
 */
define('ABS_ROOT', realpath(dirname(__FILE__)));

/**
 * @brief relative path to the ROOT directory
 */
define('ROOT', preg_replace("#".$_SERVER['DOCUMENT_ROOT']."#", "", ABS_ROOT));

/**
 * @brief operating system directory separator 
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
