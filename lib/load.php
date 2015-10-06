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

// i18n
$language = 'de_CH';
putenv("LANG=$language");
putenv("LC_ALL=$language");
setlocale(LC_ALL, $language);

$domain = 'messages';
$locales_dir = $_SERVER["DOCUMENT_ROOT"]."/locale";
bindtextdomain($domain, $locales_dir);
textdomain($domain);
bind_textdomain_codeset($domain, 'UTF-8');

User::refresh(); // refresh user data in session
