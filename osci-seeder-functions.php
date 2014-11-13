<?php

define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/install.inc';
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
require_once DRUPAL_ROOT . '/includes/password.inc';

class osciInstaller
{
	public $db;
	public $dbFile = 'osci-toolkit.sql';

	public function __construct($settings)
	{
		// Database name
		$mysql_database = $settings['mysql']['database'];
		// MySQL username
		$mysql_username = $settings['mysql']['username'];
		// MySQL password
		$mysql_password = $settings['mysql']['password'];

		// Advanced Options
		// MySQL host
		$mysql_host = $settings['mysql']['host'];
		// MySQL port
		$mysql_port = $settings['mysql']['port'];
		// MySQL prefix
		$mysql_prefix = $settings['mysql']['prefix'];
		// if port is set add it to host
		if ($mysql_port) {
			$mysql_host = $mysql_host . ":" . $mysql_port;
		}
		// // if prefix is set add it to database
		// if ($mysql_prefix) {
		// 	$mysql_database = $mysql_prefix . "_" . $mysql_database;
		// }

		$pdo = 'mysql:host=' . $mysql_host . ';port=' . $mysql_port . ';dbname=' . $mysql_database;
		
		$this->db = new PDO($pdo, $mysql_username, $mysql_password);
		$this->settings = $settings;
	}

	public function seedTables() {

		// Temporary variable, used to store current query
		$templine = '';
		// Read in entire file
		$lines = file($this->dbFile);
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
				try {
					$this->db->query($templine);
				} catch(Exception $e) {
					throw Exception('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
				}
				// Reset temp variable to empty
				$templine = '';
			}
		}

		return 1;
	}

	function rewriteSettings() {
		$settings['databases'] = array(
			'value'    => array('default' => array('default' => $this->settings['mysql'])),
			'required' => TRUE,
		);
		$settings['drupal_hash_salt'] = array(
			'value'    => drupal_random_key(),
			'required' => TRUE,
		);

		drupal_rewrite_settings($settings);

		return 1;
	}


	function updatePassword() {

		$hashword = user_hash_password($this->settings['password']);

		$sql = "UPDATE 
				users 
			SET 
				name='" . $this->settings["name"] . "', 
				pass='$hashword', 
				mail='" . $this->settings['mail'] . "' WHERE uid=1
			";

		try {
			$this->db->query($sql);
		} catch (Exception $e) {
			throw Exception('Error performing query ' . $sql . ': ' . mysql_error());
		}

		return 1;
	}
}
