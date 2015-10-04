<?php
require_once "lib/load.php";

if (!require_params("id", "action")) die();
$action = $_REQUEST["action"];
$game = Game::loadGame($_REQUEST["id"]);

switch ($action) {
  case 'up':
  case 'down':
  case 'left':
  case 'right':
    echo json_encode($game->move($action));
    break;

  default:
    throw new Exception("Unknown action.");
    break;
}
