<?php
/**
 * @file /var/www/jeff.git/themes/default/view/login.php
 * @ingroup default_theme login_module
 * @brief Template containing the public login form, see @ref loginController::login
 *
 * Available variables:
 * - **form_action**: action attribute of the form element
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.98
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
?>
<section class="login box">
<h1><?= __("Login") ?></h1>
<form id="loginForm" class="noBkg noBorder login" action="<?= $form_action ?>" method="post">
	<!--<label for="user">Username</label> -->
	<input type="text" name="user" value="" placeholder="username" />
	<br class="formRowBreak"/>
	<!--<label for="password">Password</label> -->
	<input type="password" name="password" value="" placeholder="password" /> &#160; <input type="submit" name="submit_login" value="<?= __("login") ?>" />
	<br class="formRowBreak"/>
</form> 
</section>
