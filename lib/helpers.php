<?php

// Checks if every paramter is set and non-empty
function all_set($obj, ...$list_of_params) {
  foreach ($list_of_params as $param)
    if (!isset($obj->$param)) return false;
  return true;
}
// checks if all are present in $_REQUEST
function require_params(...$list_of_params) {
  foreach ($list_of_params as $param)
    if (!array_key_exists($param, $_REQUEST) || strlen($_REQUEST[$param]) == 0) return false;
  return true;
}

function salt_hash($input) {
  return hash("md5", "TheLegendOfRoot".$input);
}

function apply_arr($arr, &$dest) {
  foreach ($arr as $key => $value) {
    $dest->{$key} = $value;
  }
}

function isLoggedIn() {
	return (isset($_SESSION["user"]));
}

// Combines parent dir with filename sent from client.
// Throws exception if a directory traversal is detected.
// Based on: http://stackoverflow.com/questions/4205141/preventing-directory-traversal-in-php-but-allowing-paths
function check_filename($parentDir, $filename) {
  $realBase = realpath($parentDir);
  $realUserPath = realpath($parentDir . $filename);
  if ($realUserPath === false || strpos($realUserPath, $realBase) !== 0) {
    throw new Exception("Directory traversal attack detected!");
  } else {
    return $realUserPath;
  }
}
