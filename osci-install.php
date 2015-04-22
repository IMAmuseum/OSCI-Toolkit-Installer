<?php

// Exit early if running an incompatible PHP version to avoid fatal errors.
if (version_compare(PHP_VERSION, '5.2.4') < 0) {
  print 'Your PHP installation is too old. Drupal requires at least PHP 5.2.4. See the <a href="http://drupal.org/requirements">system requirements</a> page for more information.';
  exit;
}

//Get site url
$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

//remove osci-install from url
$site_url = str_replace(array("index.php", "osci-install.php"), "", $url);

// Replace last slash in site_url
$site_url = rtrim($site_url, '/');

// STEP 1 - WAIT
// unzip osci-install

// STEP 2 - FORM
// get database name
// database username
// database password
// hostname
// SUBMIT
// write to drupal settings.php
// create database tables from sql

// STEP 3 - COMPLETE
// move osci-install
// remove osci-install zip?
// show default username / password
// redirect to drupal login

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>OSCI Toolkit Installer by IMAmuseum</title>
		<link rel="stylesheet" href="osci-assets/bootstrap.min.css">
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-md-2">
					<h1><img src="osci-assets/osci-logo.png"></h1>
				</div>
				<?php
				    if (is_writable('.htaccess')) {
			            $errors['htaccess'] = '';
			        } else {
			            $errors['htaccess'] = 'error';
			        }
        		?>
				<div class="col-md-10">
					<div class="zipped">
						<h3 class="col-sm-offset-2">Installing OSCI Toolkit Please Wait...<h3>
						<div class="col-sm-offset-2">
							<img src="osci-assets/spinner.gif">
						</div>
					</div>
					<form action="osci-seeder.php" class="form-horizontal hidden" method="POST" id="seeder-form">
							<div class="error"></div>
							<input type="text" name="mysql[driver]" value="mysql" class="hidden">
							<div class="form-group">
								<h3 class="col-sm-offset-3">Database Settings</h3>
							</div>
							<div class="form-group">
								<label for="db_name" class="col-sm-3 control-label">Database Name *</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="db_name" name="mysql[database]" required>
								</div>
							</div>
							<div class="form-group">
								<label for="db_username" class="col-sm-3 control-label">Database Username *</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="db_username" name="mysql[username]" required>
								</div>
							</div>
							<div class="form-group">
								<label for="db_password" class="col-sm-3 control-label">Database Password *</label>
								<div class="col-sm-9">
									<input type="password" class="form-control" id="db_password" name="mysql[password]" required>
								</div>
							</div>
							<div class="form-group">
								<h3 class="col-sm-offset-3">Advanced Database Options</h3>
							</div>
							<div class="form-group">
								<label for="db_host" class="col-sm-3 control-label">Database Host *</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="db_host" name="mysql[host]" value="localhost" required>
								</div>
							</div>
							<div class="form-group" class="hidden">
								<label for="db_port" class="col-sm-3 control-label">Database Port</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="db_port" name="mysql[port]">
								</div>
							</div>
							<div class="form-group" class="hidden">
								<label for="db_prefix" class="col-sm-3 control-label">Table Prefix</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="db_prefix" name="mysql[prefix]">
								</div>
							</div>

							<div class="form-group">
								<h3 class="col-sm-offset-3">Site Maintenance Account</h3>
							</div>
							<div class="form-group">
								<label for="name" class="col-sm-3 control-label">Username *</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="name" name="name" value="admin" required>
								</div>
							</div>
							<div class="form-group">
								<label for="mail" class="col-sm-3 control-label">E-mail address *</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="mail" name="mail" required>
								</div>
							</div>
							<div class="form-group">
								<label for="password" class="col-sm-3 control-label">Password *</label>
								<div class="col-sm-9">
									<input type="password" class="form-control" id="password" name="password">
								</div>
							</div>
							<div class="form-group">
								<label for="password_again" class="col-sm-3 control-label">Confirm Password *</label>
								<div class="col-sm-9">
									<input type="password" class="form-control" id="password_again" name="password_again">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">Sample Publication</label>
								<div class="radio">
									<label>
										<input type="radio" name="samples" id="sample0" value="none" checked>
										No Sample
									</label>
								</div>
								<div class="col-sm-offset-3 radio">
									<label>
										<input type="radio" name="samples" id="sample1" value="alice">
										Alice's Adventures in Wonderland
									</label>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-offset-3 col-sm-9">
									<input type="submit" id="profiler" class="btn btn-default" name="submit" value="Submit">
								</div>
							</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>

<script src="osci-assets/jquery-1.11.1.min.js"></script>
<script src="osci-assets/jquery.validate.min.js"></script>

<script>
$( document ).ready(function() {
	$.post('<?php echo "$site_url/osci-zipper.php"; ?>', function( data ) {
		if ( data == 1 ) {
			$( ".zipped" ).html( '' );
			$( "#seeder-form").removeClass('hidden');
		} else {
			$( ".zipped" ).html(data);
		}
	});

	$('#seeder-form').validate({
		rules: {
			password: "required",
		    password_again: {
		      equalTo: "#password"
		    }
		}
	});
});
</script>