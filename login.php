<?php

// TODO: Check db and set session

$redirect = "/";
if (isset($_SERVER["HTTP_REFERER"]))
  $redirect = $_SERVER["HTTP_REFERER"];

header("Location: ".$redirect);
