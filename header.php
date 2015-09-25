<?php require_once "lib/requires.php" ?>
<!DOCTYPE html>
<html lang="de">
<head>
  <title>The Legend of ROOT</title>
  <meta charset="utf-8">
  <meta name="author" content="Stefan Tanner &amp; Lukas Knöpfel">
  <link rel="shortcut icon" type="image/x-icon" href="static/img/favicon.ico">
  <link rel="stylesheet" href="static/css/style.css">
  <script src="static/js/jquery.js"></script>
</head>
<body>
  <header>
    <h1><a href="/">The Legend of ROOT</a></h1>

    <div id="login">
      <?php if (isset($_SESSION["user"])) { ?>
      <span>Loggid in as: <span><?=$_SESSION["user"]->name?></span><a href="logout.php">Logout</a></span>
      <?php } else { ?>
      <form action="login.php" method="POST">
        <input name="username" type="text" placeholder="Username" />
        <input name="password" type="password" placeholder="Password" />
        <input type="submit" value="Login" />
      </form>
      <a href="/register.php">Register</a>
      <?php } ?>
    </div>
  </header>
  <div id="content">
