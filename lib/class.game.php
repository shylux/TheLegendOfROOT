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

    $action_log = array();

    $action_log[] = array(
      "action" => "movePlayer",
      "x" => $newPos["x"],
      "y" => $newPos["y"]);

    foreach ($this->getEntities($this->stats->x, $this->stats->y) as $entity) {
      $this->executeEntity($entity, $action_log);
    }

    if (end($action_log)["action"] !== "exit")
      $this->save();
    return $action_log;
  }

  public function executeEntity($entity, &$action_log) {
    switch ($entity->type) {
      case "message":
        if ($this->checkReadMessages($entity))
          break;
        $action_log[] = array(
          "action" => "message",
          "message" => $entity->message);
        break;
      case "movePlayer":
        $action_log = array_merge($action_log, $this->move($entity->direction));
        break;
      case "exit":
        $this->exitDungeon($entity);
        $action_log[] = array("action" => "refreshBrowser");
        break;
      case "entrance":
        return;
      default:
      $action_log[] = array(
        "action" => "message",
        "message" => "Type: ".$entity->type
      );
    }

  }
  public function exitDungeon($entity) {
    $this->delete();
    if (isset($entity->number))
      Game::newGame($entity->toDungeon, $entity->number);
    else {
      Game::newGame($entity->toDungeon);
    }
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
  public function getEntities($x, $y) {
    $entities = array();
    foreach ($this->entities as $entity) {
      if (!all_set($entity, "x", "y")) continue;
      if ($entity->x == $x && $entity->y == $y)
        $entities[] = $entity;
    }
    return $entities;
  }
  public function getEntityId($entity) {
    return hash("md5", $this->name . json_encode($entity));
  }
  /* Checks if message has already been read by user. If so returns true.
     Otherwise adds message to read messages and returns false. */
  public function checkReadMessages($message) {
    $user = $_SESSION["user"];
    if (!isset($user->json_data["read_messages"]))
      $user->json_data["read_messages"] = array();
    $ret = in_array($this->getEntityId($message), $user->json_data["read_messages"]);
    if (!$ret) {
      $user->json_data["read_messages"][] = $this->getEntityId($message);
      $user->save();
    }
    return $ret;
  }
  public function getEntrance($entrance_nr) {
    $default_entrance = null;
    $entrance = null;
    foreach ($this->entities as $entity) {
      if ($entity->type == "entrance") {
        if ($default_entrance === null) $default_entrance = $entity;
        if ($entrance_nr !== null && isset($entity->number) && $entrance_nr == $entity->number)
          $entrance = $entity;
      }
    }
    if ($entrance !== null) return $entrance;
    return $default_entrance;
  }
  public function isPassable($x, $y) {
    return in_array($this->terrain[$y][$x], Game::PASSABLE_TERRAIN_IDS);
  }

  public static function newGame($dungeon_name, $entrance_nr = null) {
    $filename = check_filename("./dungeons/", $dungeon_name.".json");
    $game = new Game();
    apply_arr(json_decode(file_get_contents($filename)), $game);

    $game->username = $_SESSION["user"]->name;
    $entrance = $game->getEntrance($entrance_nr);
    $game->stats["x"] = $entrance->x;
    $game->stats["y"] = $entrance->y;

    $game->json_data = json_encode($game);
    $id = $GLOBALS["db"]->insert("games", $game);
    $game->id = $id;
    $game->save();
  }

  // load game of logged in user
  public static function load() {
    $username = $_SESSION["user"]->name;
    if (!$GLOBALS["db"]->exists("games", array("username"), array("username" => $username)))
      Game::newGame("origin");
    $game_array = $GLOBALS["db"]->selectAll("games", array("username" => $username))[0];
    $game_unpacked = json_decode($game_array["json_data"]);
    $game = new Game();
    apply_arr($game_unpacked, $game);
    return $game;
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
  public function delete() {
    $GLOBALS["db"]->remove("games", array("id" => $this->id));
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
