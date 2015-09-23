<?php

// Checks if every paramter is sent and non-empty
function require_params(...$list_of_params) {
  foreach ($list_of_params as $param)
    if (!array_key_exists($param, $_REQUEST) || strlen($_REQUEST[$param]) == 0) return false;
  return true;
}
