<?php

if(isset($_POST['submit'])) {

	require_once 'osci-seeder-functions.php';
	require_once 'osci-cleaner.php';

	$database = $_POST['mysql'];
	$name = $_POST['name'];
	$mail = $_POST['mail'];
	$password = $_POST['password'];

	$seeder = seedTables($database);

	$setter = rewriteSettings($database);

	$profiler = updatePassword($database, $name, $mail, $password);

	if($seeder && $setter && $profiler) {
		cleanupInstaller();
		header('Location: /');
	}

} else {
	header('Location: /osci-install.php');
}
