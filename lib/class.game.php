<?php

class Game implements JsonSerializable {
  public $id;
  public $username;
  public $stats = array(); // contains current health, position etc

  public function getEntityByType($type) {
    foreach ($this->entities as $entity) {
      if ($entity->type == $type) return $entity;
    }
  }

  public static function newGame($dungeon_name) {
    $filename = check_filename("./dungeons/", $dungeon_name);
    $game = new Game();
    apply_arr(json_decode(file_get_contents($filename)), $game);

    $game->username = $_SESSION["user"]->name;
    $entrance = $game->getEntityByType("entrance");
    $game->stats["x"] = $entrance->x;
    $game->stats["y"] = $entrance->y;

    $game->json_data = json_encode($game);
    $id = $GLOBALS["db"]->insert("games", $game);
    $game->id = $id;
    $game->save();
  }

  public static function loadGame($id) {
    $game_array = $GLOBALS["db"]->selectAll("games", array("id" => $id))[0];
    $game_unpacked = json_decode($game_array["json_data"]);
    $game = new Game();
    apply_arr($game_unpacked, $game);
    return $game;
  }
  public static function deleteGame($id) {
    $GLOBALS["db"]->remove("games", array("id" => $id));
  }
  public static function listGames($username = false) {
    if (!$username) $username = $_SESSION["user"]->name;
    return $GLOBALS["db"]->select("games", array("id", "name", "recommendedLevel"), array("username" => $username));
  }

  public function save() {
    $this->json_data = json_encode($this);
    $GLOBALS["db"]->update("games", $this, array("id" => $this->id));
  }

  // Prevent recursive serialization of json_data
  function jsonSerialize() {
    $arr = (array) $this;
    unset($arr["json_data"]);
    return $arr;
  }
}
