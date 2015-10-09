<?php

User::requireLoggedIn();

if (require_params("new")) {
  Game::newGame($_REQUEST["new"]);
}
if (require_params("reset")) {
  $game = Game::load();
  $game->delete();
}

if (true):
  $game = Game::load();
?>
<h2><?= $game->name ?></h2><a href="?reset=true">Reset Dungeons</a>
<div id="game-container" class="game-tile"></div>

<script type="text/javascript">
TLOR.setup($('#game-container'), <?=json_encode($game)?>, {});
TLOR.play();
</script>


<?php else: ?>

<h2>Enter new Dungeon</h2>

<div id="abailable-dungeons">
<?php foreach (scandir("dungeons") as $file) {
  if (is_dir("dungeons/" . $file)) continue; ?>
  <a href="?new=<?= $file ?>"><?= $file ?></a>
<?php } ?>
</div>

<h2>Active Games</h2>
<div id="active-games">
<?php foreach (Game::listGames() as $game): ?>
  <div>
    <a href="?id=<?= $game["id"] ?>"><?= $game["name"] ?></a>
    <a href="?id=<?= $game["id"] ?>&delete=true">Delete</a>
  </div>
<?php endforeach; ?>
</div>

<?php endif; ?>
