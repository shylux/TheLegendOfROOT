<?php
include("../lib/class.sqlite.php");
 
if ($db = new SQLITE('../root.db.sqlite')) {   
	$db->selectAll('users');
	var_dump($db->selectAll('users'));
	$db->insert('users', array(
		'name'	=> 'TESTUSER',
		'email'	=> 'tesetemail@mail.com',
		'class'	=> 1,
		'pass_hash'	=> 'aergSDFASDFASDFASDF',
		'json_data'	=> 'asdfasdjkfaklsjdfalsdfasdf',
		'xp'	=> 0,
		'att'	=> 0,
		'def'	=> 0,
		'agi'	=> 0
		
	));
} else {
    die($sqliteerror);
}