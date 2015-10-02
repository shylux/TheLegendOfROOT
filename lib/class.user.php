<?php
require_once "class.sqlite.php";

abstract class Klass {
  const Developer = 0;
  const Supporter = 1;
  const Admin = 2;
}

class User {
  public $name;
  public $email;
  public $pass_hash;
  public $class;
  public $xp = 0;
  public $att = 0;
  public $def = 0;
  public $agi = 0;

  public function level() {
    return floor(sqrt($this->xp));
  }

  // invert of level()
  public function totXpForLv($level) {
    return $level**2;
  }

  public function xpForNextLevel() {
    return $this->totXpForLv($this->level() + 1);
  }

  public function totSkillpoints() {
    return $this->level() * 2;
  }
  public function availableSkillpoints() {
    return $this->totSkillpoints() - ($this->att + $this->def + $this->agi);
  }

  public function upgrade($skill) {
    if ($this->availableSkillpoints() < 1) return;
    switch ($skill) {
      case "att":
        $this->att += 1;
        break;
      case "def":
        $this->def += 1;
        break;
      case "agi":
        $this->agi += 1;
        break;
    }
  }

  public static function create($username, $email, $password, $class) {
    if ($GLOBALS["db"]->exists("users", array("name"), array("name" => $username)))
      return false;
    $GLOBALS["db"]->insert("users",
                array("name" => $username,
                      "email" => $email,
                      "pass_hash" => salt_hash($password),
                      "class" => $class));
    return User::load($username);
  }

  public static function load($username) {
    $user_array = $GLOBALS["db"]->selectAll("users", array("name" => $username))[0];
    $user = new User();
    apply_arr($user_array, $user);
    return $user;
  }

  public static function login($username, $password) {
    $user = User::load($username);
    if ($user->pass_hash !== salt_hash($password))
      return false;
    $_SESSION["user"] = $user;
    return $user;
  }
}
