<?php

// remove php installer functions
// remove .tgz
// remove .sql

function cleanupInstaller() {
	shell_exec('rm osci-install.php');
	shell_exec('rm -rf osci-assets');
	shell_exec('rm osci-install.tgz');
	shell_exec('rm osci-toolkit.sql');
	shell_exec('rm osci-zipper.php');
	shell_exec('rm osci-seeder-functions.php');
	shell_exec('rm osci-seeder.php');
}