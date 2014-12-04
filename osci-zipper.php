<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	//check of osci-install has laready been unzipped
	if (!file_exists("index.php")) {
		//unzip drupal into temp
		try {
			$zip = new ZipArchive;
			$path = getcwd();
			$path = str_replace("\\","/",$path);

			if ($zip->open('osci-install.zip') === TRUE) {
				$zip->extractTo($path);
				$zip->close();
			} else {
				$status = "Zip Error";
			}

			if (file_exists("index.php")) {
				$status = "1";
			}
		} catch (Exception $e) {
			$status = $e;
		}
	}

	//unzip has already occurred
	if (file_exists("index.php")) {
		$status = "1";
	}

	echo $status;

?>

