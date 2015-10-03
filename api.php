<?php
require_once "lib/load.php";

var_dump($_REQUEST);
if (!require_params("id", "action")) die();

echo "test";
