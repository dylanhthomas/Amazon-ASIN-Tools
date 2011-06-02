<?php

// Configure for your database
$db_server		= 'insert_your_database_server';
$db_username	= 'insert_your_database_username';
$db_password	= 'insert_your_database_password';
$db_name		= 'insert_your_database_name';
$table 			= 'insert_your_database_table';
// ***************************************

mysql_connect($db_server, $db_username, $db_password) or die("Could not connect: " . mysql_error());
mysql_select_db($db_name);

?>
