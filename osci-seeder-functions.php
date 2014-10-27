<?php

define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/install.inc';
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
require_once DRUPAL_ROOT . '/includes/password.inc';


function seedTables($database) {
	$filename = 'osci-toolkit.sql';
	// Database name
	$mysql_database = $database['database'];
	// MySQL username
	$mysql_username = $database['username'];
	// MySQL password
	$mysql_password = $database['password'];

	// Advanced Options
	// MySQL host
	$mysql_host = $database['host'];
	// MySQL port
	$mysql_port = $database['port'];
	// MySQL prefix
	$mysql_prefix = $database['prefix'];
	// if port is set add it to host
	if ($mysql_port) {
		$mysql_host = $mysql_host . ":" . $mysql_port;
	}
	// if prefix is set add it to database
	if ($mysql_prefix) {
		$mysql_database = $mysql_prefix . "_" . $mysql_database;
	}

	// Connect to MySQL server
	mysql_connect($mysql_host, $mysql_username, $mysql_password) or die('Error connecting to MySQL server: ' . mysql_error());
	// Select database
	mysql_select_db($mysql_database) or die('Error selecting MySQL database: ' . mysql_error());

	// Temporary variable, used to store current query
	$templine = '';
	// Read in entire file
	$lines = file($filename);
	// Loop through each line
	foreach ($lines as $line)
	{
		// Skip it if it's a comment
		if (substr($line, 0, 2) == '--' || $line == '')
			continue;

		// Add this line to the current segment
		$templine .= $line;
		// If it has a semicolon at the end, it's the end of the query
		if (substr(trim($line), -1, 1) == ';')
		{
			// Perform the query
			mysql_query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
			// Reset temp variable to empty
			$templine = '';
		}
	}

	return 1;
}

function rewriteSettings($database) {
	$settings['databases'] = array(
	'value'    => array('default' => array('default' => $database)),
	'required' => TRUE,
	);
	$settings['drupal_hash_salt'] = array(
	'value'    => drupal_random_key(),
	'required' => TRUE,
	);

	drupal_rewrite_settings($settings);

	return 1;
}


function updatePassword($database, $name, $mail, $password) {

	$hashword = user_hash_password($password);

	// Database name
	$mysql_database = $database['database'];
	// MySQL username
	$mysql_username = $database['username'];
	// MySQL password
	$mysql_password = $database['password'];

	// Advanced Options
	// MySQL host
	$mysql_host = $database['host'];
	// MySQL port
	$mysql_port = $database['port'];
	// MySQL prefix
	$mysql_prefix = $database['prefix'];
	// if port is set add it to host
	if ($mysql_port) {
		$mysql_host = $mysql_host . ":" . $mysql_port;
	}
	// if prefix is set add it to database
	if ($mysql_prefix) {
		$mysql_database = $mysql_prefix . "_" . $mysql_database;
	}

	// Connect to MySQL server
	mysql_connect($mysql_host, $mysql_username, $mysql_password) or die('Error connecting to MySQL server: ' . mysql_error());
	// Select database
	mysql_select_db($mysql_database) or die('Error selecting MySQL database: ' . mysql_error());

	$sql = "UPDATE users SET name='$name', pass='$hashword', mail='$mail' WHERE uid=1";

	mysql_query($sql) or print('Error performing query \'<strong>' . $sql . '\': ' . mysql_error() . '<br /><br />');

	return 1;
}
