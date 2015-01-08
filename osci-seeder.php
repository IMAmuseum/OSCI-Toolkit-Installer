<?php

if(isset($_POST['submit'])) {

	require_once 'osci-seeder-functions.php';
	require_once 'osci-cleaner.php';

	$settings = array(
		'mysql'		=> $_POST['mysql'],
		'name'		=> $_POST['name'],
		'mail'		=> $_POST['mail'],
		'password'	=> $_POST['password'],
	);

	$installer 	= new osciInstaller($settings);
	$seeder 	= $installer->seedTables('osci-toolkit.sql');
	$setter 	= $installer->rewriteSettings();
	$profiler 	= $installer->updatePassword();

	if($seeder && $setter && $profiler) {
		cleanupInstaller();
		header('Location: /');
	}

} else {
	header('Location: /osci-install.php');
}
