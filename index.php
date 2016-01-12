<?php require_once("lib/load.php"); ?>
<!DOCTYPE html>
<html>
<head>
  <title>The Legend of ROOT</title>
  <meta charset="utf-8">
  <meta name="author" content="Stefan Tanner &amp; Lukas Knöpfel">
  <link rel="shortcut icon" type="image/x-icon" href="static/img/favicon.ico">
  <link rel="stylesheet" href="static/css/style.css">
  <link rel="stylesheet" href="static/css/chat.css">
  <script src="static/js/jquery.js"></script>
  <script src="static/js/js.cookie.js"></script>
  <script src="static/js/helpers.js"></script>
  <script src="static/js/json2.js"></script>
  <script src="static/js/game.js"></script>

  <?php if ( False && isLoggedIn() && strpos($_SERVER['REQUEST_URI'], 'game') > 0 ): ?>
	<script src="static/js/jquery-ui/jquery-ui.js"></script>
	<script src="static/js/chat.js"></script>
  <?php endif; ?>

</head>
<body>
 <?php if ( False && isLoggedIn() && strpos($_SERVER['REQUEST_URI'], 'game') > 0 ): ?>
		<?php include("chat/chatstructure.php"); ?>
 <?php endif; ?>
  <header>
    <h1><a href="/">The Legend of ROOT</a></h1>
    <nav>
		<?php if ( isLoggedIn() ): ?>
      <a href="/game">Dungeons</a>
		<?php endif; ?>
    </nav>

    <div id="login">
      <?php if ( isLoggedIn() ) { ?>
      <span>Loggid in as: <a href="/character"><?=$_SESSION["user"]->name?></a><a href="/logout">Logout</a></span>
      <?php } else { ?>
      <a href="/register">Register</a>
      <form action="login.php" method="POST">
        <input name="username" type="text" placeholder="Username" />
        <input name="password" type="password" placeholder="Password" />
        <input type="submit" value="Login" />
      </form>
      <?php } ?>
    </div>


  </header>
	<div id="content">
	<?php include_once( ( !isset($_GET['include']) ) ? 'register.php' : "{$_GET['include']}" ); ?>
	</div>
	<footer>
		<div id="authors">
		  created by: <a href="http://github.com/tanns2" rel="author">Stefan Ägidius Tanner</a> &amp; <a href="http://shylux.ch" rel="author">Lukas "Knöp the Möb" Knöpfel</a>
		</div>
	</footer>
</body>
</html>
