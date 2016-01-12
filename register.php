<?php

if (require_params("username", "email", "password") && Captcha::validate($_POST)) {

  $user = User::create($_REQUEST["username"], $_REQUEST["email"], $_REQUEST["password"]);

  if ($user) {
    $_SESSION["user"] = $user;
    ?><script type="text/javascript">window.location="/game";</script><?php
  }
}

$captcha = new Captcha();
$captcha->generateCaptcha();

?>

<h2>Register</h2>

<form id="register" method="POST">
  <table>
  <tr>
	<td><label for="username">Username</label></td>
	<td><input id="username" name="username" type="text" placeholder="Username" /></td>
  </tr>
  <tr>
	<td><label for="email">Email</label></td>
	<td><input id="email" name="email" type="email" placeholder="Email" /></td>
  </tr>
  <tr>
	<td><label for="password">Password</label></td>
	<td><input id="password" name="password" type="password" placeholder="Password" /></td>
  </tr>
  <tr>
	<td colspan="2"><?php echo $captcha->getFormInfo(); ?></td>
  </tr>
  <tr>
	<td><?php echo $captcha->getImage(); ?></td>
	<td><?php echo $captcha->getForm(); ?></td>
   </tr>
  <tr>
	<td colspan="2"><input type="submit" value="Register" /></td>
  </tr>
  </table>
</form>
