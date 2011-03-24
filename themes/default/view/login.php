<h1><?= __("Login") ?></h1>
<form id="loginForm" class="noBkg noBorder login" action="<?= $form_action ?>" method="post">
	<!--<label for="user">Username</label> -->
	<input type="text" name="user" value="" placeholder="username" />
	<br class="formRowBreak"/>
	<!--<label for="password">Password</label> -->
	<input type="password" name="password" value="" placeholder="password" /> &#160; <input type="submit" name="submit_login" value="<?= __("login") ?>" />
	<br class="formRowBreak"/>
</form> 
