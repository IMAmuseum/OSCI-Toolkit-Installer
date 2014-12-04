<?php
	//check of osci-install has laready been unzipped
	if (!file_exists("index.php")) {
		//unzip drupal into temp
		try {
			$zip = new ZipArchive;
			$zip->open('osci-install.zip');
			$path = getcwd() . "/";
        	$path = str_replace("\\","/",$path);
			$zip->extractTo($path);
			$zip->close();
			echo "1";
		} catch (Exception $e) {
    		echo $e;
		}
	}

	//unzip has already occurred
	if (file_exists("index.php")) {
		echo "1";
	}

?>

