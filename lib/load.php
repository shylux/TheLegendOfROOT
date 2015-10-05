<?php

require_once "helpers.php";
require_once "class.configuration.php";
require_once "class.sqlite.php";
require_once "class.pagination.php";
require_once "class.user.php";
require_once "class.game.php";

session_start();

$config = new Configuration("config/config");  

// TO DO : Do some session stuff, setting other GLOBALS, etc.
// Language stuff.. etc.

$GLOBALS['db'] 			= new Sqlite($config->getConfiguration("db")); 
$GLOBALS['paginatior'] 	= new Paginator();

User::refresh(); // refresh user data in session 