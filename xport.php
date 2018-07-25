<?php

require_once("config.inc.php");
require_once("src/Xport.php");
$xport = new Xport($config);

$xport->backup('mysql1');
//$xport->import('mysql1', 'mysql2');

