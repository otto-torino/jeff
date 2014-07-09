<?php
/**
 * @file /var/www/jeff.git/modern/default/view/login.php
 * @ingroup default_theme modern_module
 * @brief Template containing the public login form, see @ref loginController::login
 *
 * Available variables:
 * - **form_action**: action attribute of the form element
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
?>
<section class="login box">
<h1><?= __("Login") ?></h1>
<form id="loginForm" class="login" action="<?= $form_action ?>" method="post">
	<!--<label for="user">Username</label> -->
	<p><input type="text" name="user" value="" placeholder="username" /></p>
	<!--<label for="password">Password</label> -->
    <p><input type="password" name="password" value="" placeholder="password" /></p>
    <input type="submit" name="submit_login" value="<?= __("login") ?>" />
</form> 
</section>
