<?php

if(isset($_POST['submit'])) {

	require_once 'osci-seeder-functions.php';
	require_once 'osci-cleaner.php';

	$settings = array(
		'mysql'		=> $_POST['mysql'],
		'name'		=> $_POST['name'],
		'mail'		=> $_POST['mail'],
		'password'	=> $_POST['password'],
		'sample'	=> $_POST['samples'],
	);

	$installer 	= new osciInstaller($settings);
	// check if user wants to use a sample publication
	if ($settings['sample'] == 'none') {
		$seeder = $installer->seedTables('osci-toolkit.sql');
		$assets = 1;
	} else {
		$seeder = $installer->seedTables('osci-assets/samples/' . $settings['sample'] . '/osci-sample.sql');
		$assets = $installer->moveSampleAssets($settings['sample']);
	}
	$setter 	= $installer->rewriteSettings();
	$profiler 	= $installer->updatePassword();

	if($seeder && $setter && $profiler) {
		cleanupInstaller();
		header('Location: /');
	}

} else {
	header('Location: /osci-install.php');
}
