<?php
require_once "lib/requires.php";

if (require_params("username", "password")) {
  $user = User::login($_REQUEST["username"], $_REQUEST["password"]);
}

$redirect = "/";
if (isset($_SERVER["HTTP_REFERER"]))
  $redirect = $_SERVER["HTTP_REFERER"];

header("Location: ".$redirect);
