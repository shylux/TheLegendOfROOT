<?php

class Game implements JsonSerializable {
  const PASSABLE_TERRAIN_IDS = [0, 3, 4, 5, 6];

  public $id;
  public $username;
  public $stats = array(); // contains current health, position etc

  public function move($direction) {
    $newPos = $this->getNewCoords($direction);
    $this->checkCoords($newPos);
    $this->stats->x = $newPos["x"];
    $this->stats->y = $newPos["y"];
    $this->save();
    return array(
      "action" => "movePlayer",
      "x" => $newPos["x"],
      "y" => $newPos["y"]);
  }

  public function exitDungeon() {

  }

  public function getNewCoords($direction) {
    switch ($direction) {
      case 'up':
        return array("x"=>$this->stats->x, "y"=>$this->stats->y-1);
      case 'down':
        return array("x"=>$this->stats->x, "y"=>$this->stats->y+1);
      case 'right':
        return array("x"=>$this->stats->x+1, "y"=>$this->stats->y);
      case 'left':
        return array("x"=>$this->stats->x-1, "y"=>$this->stats->y);
      default:
        throw new Exception("Unknown move direction.");
    }
  }
  public function checkCoords($newPos) {
    if ($newPos["x"] >= $this->width || $newPos["x"] < 0 ||
        $newPos["y"] >= $this->height || $newPos["y"] < 0)
      throw new Exception("Cannot move outside of dungeon!");
    if (!$this->isPassable($newPos["x"], $newPos["y"]))
      throw new Exception("Terrain unpassable!");
  }
  public function getEntityByType($type) {
    foreach ($this->entities as $entity) {
      if ($entity->type == $type) return $entity;
    }
  }
  public function isPassable($x, $y) {
    return in_array($this->terrain[$y][$x], Game::PASSABLE_TERRAIN_IDS);
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