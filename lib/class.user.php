<?php
require_once "class.sqlite.php";

class User {
  public $name;
  public $email;
  public $pass_hash;

  public static function create($username, $email, $password) {
    $GLOBALS["db"]->insert("users",
                array("name" => $username,
                      "email" => $email,
                      "pass_hash" => salt_hash($password)));
    return User::load($username);
  }

  public static function load($username) {
    $user_array = $GLOBALS["db"]->select("users", array("name", "email", "pass_hash"), array("name" => $username))[0];
    $user = new User();
    $user->name = $user_array["name"];
    $user->email = $user_array["email"];
    $user->pass_hash = $user_array["pass_hash"];
    return $user;
  }

}
