<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting( E_ALL | E_STRICT );

$config  = [];

$config['servers']['mysql1'] = [
	'host'		=> 'host1',
	'user'		=> 'root',
	'password'	=> '',
];

$config['servers']['mysql2'] = [
	'host'		=> 'host2',
	'user'		=> 'root',
	'password'	=> '',
];