<?php
require_once "lib/load.php";

$redirect = "/";

if (isset($_SERVER["HTTP_REFERER"]))
  $redirect = $_SERVER["HTTP_REFERER"];

if (require_params("username", "password")) {
  $user = User::login($_REQUEST["username"], $_REQUEST["password"]);
  $redirect = "/game";
}

header("Location: ".$redirect);
