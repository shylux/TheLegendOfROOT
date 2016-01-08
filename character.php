<h1>Character</h1>

<?php
if (!isset($_SESSION["user"])) die();

$user = $_SESSION["user"];

if (isset($_REQUEST["up"]))
  $user->upgrade($_REQUEST["up"]);
?>
<h2><?= $user->name ?></h2>

<dl>
  <dt>Level</dt>
  <dd><?= $user->level() ?></dd>
  <dt>XP</dt>
  <dd><?= $user->xp ?></dd>
  <dt>XP for next Level</dt>
  <dd><?= $user->xpForNextLevel() ?></dd>
  <dt>Available Skillpoints</dt>
  <dd><?= $user->availableSkillpoints() ?></dd>
  <dt>Attack</dt>
  <dd><?= $user->attack() ?></dd>
  <dt>Defence</dt>
  <dd><?= $user->defence() ?></dd>
  <dt>Health</dt>
  <dd><?= $user->maxHealth() ?></dd>
</dl>
<?php if ($user->availableSkillpoints() > 0): ?>
<a href="?up=att">Upgrade Attack</a>
<a href="?up=def">Upgrade Defence</a>
<a href="?up=hp">Upgrade Health</a>
<?php endif; ?>
