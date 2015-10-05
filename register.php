<?php

if (require_params("username", "email", "password", "class")) {
  $user = User::create($_REQUEST["username"], $_REQUEST["email"], $_REQUEST["password"], $_REQUEST["class"]);

  if ($user) {
    $_SESSION["user"] = $user;
  }
}
?>

<h2>Register</h2>

<form id="register" method="POST">
  <label for="username">Username</label>
  <input id="username" name="username" type="text" placeholder="Username" />
  <label for="class">Class</label>
  <div>
    <label for="dev">Developer</label><input id="dev" type="radio" name="class" value="0">
    <label for="sup">Supporter</label><input id="sup" type="radio" name="class" value="1">
    <label for="adm">Admin</label><input id="adm" type="radio" name="class" value="2">
  </div>
  <label for="email">Email</label>
  <input id="email" name="email" type="email" placeholder="Email" />
  <label for="password">Password</label>
  <input id="password" name="password" type="password" placeholder="Password" />
  <input type="submit" value="Register" />
</form>