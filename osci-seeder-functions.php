<?php

define('DRUPAL_ROOT', getcwd());

class osciInstaller
{
	public $db;

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
		if ($mysql_prefix) {
			$mysql_database = $mysql_prefix . "_" . $mysql_database;
		}

		$pdo = 'mysql:host=' . $mysql_host . ';port=' . $mysql_port . ';dbname=' . $mysql_database;
		$this->db = new PDO($pdo, $mysql_username, $mysql_password);
		$this->settings = $settings;
	}

	public function seedTables($dbFile) {

		// Temporary variable, used to store current query
		$templine = '';
		// Read in entire file
		$lines = file($dbFile);
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

		require_once DRUPAL_ROOT . '/includes/install.inc';
		require_once DRUPAL_ROOT . '/includes/bootstrap.inc';

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

	function updatePassword($name = NULL, $password = NULL, $email = NULL, $uid = 1) {

		require_once DRUPAL_ROOT . '/includes/password.inc';

		if ($name == NULL || $password == NULL || $email == NULL) {
			$name = $this->settings["name"];
			$password = $this->settings['password'];
			$email = $this->settings['mail'];
		}

		$hashword = user_hash_password($password);

		$sql = "UPDATE
				users
			SET
				name='" . $name . "',
				pass='$hashword',
				mail='" . $email . "' WHERE uid='" . $uid . "'
			";

		try {
			$this->db->query($sql);
		} catch (Exception $e) {
			throw Exception('Error performing query ' . $sql . ': ' . mysql_error());
		}

		return 1;
	}

	function moveSampleAssets($sample) {
		$sample_path = 'osci-assets/samples/' . $sample;

		$image_assets_files = scandir($sample_path . '/image_assets');
		$image_styles_files = scandir($sample_path . '/image_styles');

		$image_assets_path = 'sites/default/files/image_assets/';
		$image_styles_path = 'sites/default/files/styles/medium/public/image_assets/';

		mkdir($image_assets_path, 755, true);
		mkdir($image_styles_path, 755, true);

		foreach ($image_assets_files as $file) {
			if (in_array($file, array(".",".."))) continue;
			copy($sample_path . '/image_assets/' . $file, $image_assets_path . $file);
		}

		foreach ($image_styles_files as $file) {
			if (in_array($file, array(".",".."))) continue;
			copy($sample_path . '/image_styles/' . $file, $image_styles_path . $file);
		}

		return 1;
	}

}
