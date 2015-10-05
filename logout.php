<?php
require_once "lib/requires.php";

unset($_SESSION["user"]);
session_destroy($_SESSION["user"]);

$redirect = "/";
if (isset($_SERVER["HTTP_REFERER"]))
  $redirect = $_SERVER["HTTP_REFERER"];

header("Location: ".$redirect);
