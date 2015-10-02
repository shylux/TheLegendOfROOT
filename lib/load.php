<?php

include_once("lib/requires.php");

$GLOBALS['db'] 			= new Sqlite("root.db");
$GLOBALS['paginatior'] 	= new Paginator();

User::refresh(); // refresh user data in session

// TO DO : Do some session stuff, setting other GLOBALS, etc.
