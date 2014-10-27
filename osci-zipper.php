<?php
	//check of osci-install has laready been unzipped
	if (!file_exists("index.php")) {
		//unzip drupal into temp
		$command = 'tar -zxvf osci-install.tgz';
		$osci = shell_exec($command);

		echo "1";
	} else {
		echo "1";
	}
?>

