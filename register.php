<?php
require "header.php";

if (require_params("username", "email", "password")) {
  #TODO save
  ?><h1>YAAAAAY</h1><?php
}
?>

<h2>Register</h2>

<form id="register" method="POST">
  <label for="username">Username</label>
  <input id="username" name="username" type="text" placeholder="Username" />
  <label for="email">Email</label>
  <input id="email" name="email" type="email" placeholder="Email" />
  <label for="password">Password</label>
  <input id="password" name="password" type="password" placeholder="Password" />
  <input type="submit" value="Register" />
</form>

<?php require "footer.php" ?>
