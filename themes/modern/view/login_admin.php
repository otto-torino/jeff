<?php
/**
 * @file /var/www/jeff.git/themes/default/view/login_admin.php
 * @ingroup default_theme login_module
 * @brief Template containing the login form of the administrative area, see @ref loginController::adminlogin
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
<form class="form-horizontal" action="<?= $form_action ?>" method="post">
    <div class="form-group">
        <label for="user">Username</label>
        <input type="text" name="user" value="" />
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" value="" /><br />
    </div>
    <div class="form-group">
        <label for="submit"></label>
        <input type="submit" name="submit_login" value="login" />
    </div>
</form> 
