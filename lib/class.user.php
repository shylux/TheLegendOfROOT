<?php

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
  public $hp = 0;
  public $currHp = 0;
  public $json_data = array();

  public function maxHealth() {
    return $this->hp * 3 + 10;
  }
  public function defence() {
    return $this->def;
  }
  public function attack() {
    return $this->att + 3;
  }

  private function baseLevel() {
    return 5;
  }

  public function level() {
    return $this->baseLevel() + $this->xp;
  }

  // invert of level()
  public function totXpForLv($level) {
    return $level - $this->baseLevel();
  }

  public function xpForNextLevel() {
    return $this->totXpForLv($this->level() + 1);
  }

  public function totSkillpoints() {
    return $this->level() - $this->baseLevel();
  }
  public function availableSkillpoints() {
    return $this->totSkillpoints() - ($this->att + $this->def + $this->hp);
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
      case "hp":
        $this->hp += 1;
        break;
    }
    $this->save();
  }

  public static function create($username, $email, $password, $class) {
    if ($GLOBALS["db"]->exists("users", array("name"), array("name" => $username)))
      return false;
    $GLOBALS["db"]->insert("users",
                array("name" => $username,
                      "email" => $email,
                      "pass_hash" => salt_hash($password),
                      "class" => $class,
                      "json_data" => json_encode(array())));
    return User::load($username);
  }

  public static function load($username) {
    $user_array = $GLOBALS["db"]->selectAll("users", array("name" => $username))[0];
    $user_array['json_data'] = json_decode($user_array['json_data'], true);
    $user = new User();
    apply_arr($user_array, $user);
    return $user;
  }
  public function save() {
    $this->json_data = json_encode($this->json_data);
    $GLOBALS["db"]->update("users", $this, array("name" => $this->name));
    $this->json_data = json_decode($this->json_data, true);
  }

  public static function login($username, $password) {
    $user = User::load($username);
    if ($user->pass_hash !== salt_hash($password))
      return false;
    $_SESSION["user"] = $user;
    return $user;
  }

  public static function refresh() {
    if (array_key_exists("user", $_SESSION))
      $_SESSION["user"] = User::load($_SESSION["user"]->name);
  }
  // checks if user is logged in and if not redirect to index
  public static function requireLoggedIn() {
    if (!array_key_exists("user", $_SESSION)) {
      header("Location: /");
      die();
    }
  }
}
