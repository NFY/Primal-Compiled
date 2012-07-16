<?php

use \DB\User;
use \Primal\Visitor;

$request = new Primal\Request();
$response = new Primal\Response();

$ERROR = array();

if ($_POST['login']) {
	switch (Visitor::LoginWithEmail($request['login']['email'], $request['login']['password'], $request['login']['remember'])) {
		case Visitor::ERR_LOGIN_OK:
			$bounce = Visitor::GetLoginRedirect();
			if ($bounce) {
				Visitor::ClearLoginBounce();
				$response->redirect($bounce);
			} else $response->redirect('/');
			break;
		case Visitor::ERR_LOGIN_BADUSER:
			$ERROR['BAD_USERNAME'] = "Unknown Email.";
			break;
		case Visitor::ERR_LOGIN_BADPASS:
			$ERROR['BAD_PASSWORD'] = "Incorrect Password.";
			break;
	}
}


$message = Visitor::GetLoginMessage();
$login = $_POST['login']?:array('remember'=>1);


?><!DOCTYPE HTML>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Primal Placeholder Title</title>
</head>
<body>
	<form method="post" accept-charset="utf-8" id="login_form">
		<?php if ($message) { ?><div class="message"><?php echo $message ?></div><?php } ?>
		<?php if (!empty($ERROR)) { ?><div class="error"><?php foreach ($ERROR as $err) if (is_string($err)) { ?><p><?php echo $err ?></p><?php } ?></div><?php } ?>

		<div class="email">
			<label>Email:</label>
			<input type="text" name="login[email]" value="<?php echo htmlentities($login['email']) ?>">
		</div>
		<div class="password">
			<label>Password:</label>
			<input type="password" name="login[password]" value="<?php echo htmlentities($login['password']) ?>">
		</div>
		<div class="remember">
			<label><input type="checkbox" name="login[remember]" value="1" <?php if ($login['remember']) echo 'checked' ?>> Remember Me</label>
		</div>
		<div class="submit">
			<input type="submit" class="" value="Login">
		</div>
	</form>
</body>
</html>