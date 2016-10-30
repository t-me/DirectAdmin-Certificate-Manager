<?php

$filename = __DIR__ . "/config/config.ini";
if (!file_exists($filename)) {
    echo "The file $filename does not exist";
	echo "<br>";
	echo "Please rename 'config.example.ini' to 'config.ini' and reload the page";
	die();
}


require_once __DIR__ . "/lib/config.php";
$config = new Config_Lite(__DIR__ . "/config/config.ini");
$install = $config->getBool('root','install');

if($_GET['action'] && $_GET['action'] == "install"){

	require_once __DIR__ . "/lib/functions.php";
	$func = new cm_function();

	if ($install == true){
		
		//check directadmin host adres en remove stuff
		$dahost = $_POST['dahost'];
		$forbiddenurl = array("ssl://https://", ":2222", ":2223", ":2222/", ":2223/");
		$replaceurl= array("ssl://", "", "", "", "");
		$dahost = str_replace($forbiddenurl, $replaceurl, $dahost);
			
		//check for Let's Encrypt options if there is a "/" add the end or beginning
		$ledomain = $func->LE_Dir_Trailing_Slash($_POST['ledomain']);	
		$lefolder = $func->LE_Dir_Leading_Slash($_POST['lefolder']);
		$lepublic = $func->LE_Dir_Leading_Slash($_POST['lepublic']);
		
		$key = $_POST['privatekey'];
		$url = $_POST['baseurl'];
		$luser = $func->encrypt($_POST['username'],$key);
		$lpass = $func->encrypt($_POST['password'],$key);
		
		$dpass = $func->encrypt($_POST['dapass'],$key);
		
		$configArr = array(
						'root' => array(
							'install' => false,
							'privatekey' => $key,
							'base_url' => $url,
							'lastcheck' => date_format(new DateTime(), 'd-m-Y'),
							'cversion' => '0.2',
							'lversion' => $func->GetVersion()
						),
						'dashboard' => array(
							'USERNAME' => $luser,
							'PASSWORD' => $lpass,
							'MAX_TRIES' => 3,
							'UNLIMITED_TRIES' => false
						),
						'directadmin' => array(
							'HOST' => $dahost,
							'USERNAME' => $_POST['dauser'],
							'PASSWORD' => $dpass
						),
						'letsencrypt' => array(
							'DOMAINFOLDER' => $ledomain,
							'LETSFOLDER' => $lefolder,
							'PUBLICFOLDER' => $lepublic
						));
		$config->write(__DIR__ . "/config/config.ini", $configArr);					
							
		
		$complete = true;
	}
	
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Install | Certificate Manager</title>
    <!-- Favicon-->

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="assets/plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="assets/plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="assets/css/style.min.css" rel="stylesheet">
</head>

<body class="install-page">
    <div class="install-box">
        <div class="logo">
            <a href="javascript:void(0);">Install <b>CM</b></a>
            <small>Install Certificate Manager</small>
        </div>
        <div class="card">
            <div class="body">
			<?php if($complete == true) { ?>
				<div class="row m-t-15 m-b--20">
					<div class="col-xs-12">
						<div class="alert bg-green align-center">
                            Install is complete, you can now login. <a href="index.php" class="alert-link">HERE<a>
                        </div>
					</div>
				</div>
				<div class="row m-b--20">
					<div class="col-xs-12">
						<div class="alert bg-blue align-center">
							For security reasons <br>Please remove this file from your server.
						</div>
					</div>
				</div>
			<?php } elseif($install == true){ ?>
			<div class="row m-b--20">
				<div class="col-md-6">
					<form id="sign_in" action="install.php?action=install" method="POST">
						<div class="msg">Base options</div>
						<div class="input-group">
							<span class="input-group-addon">
								<i class="material-icons">vpn_key</i>
							</span>
							<div class="form-line">
								<input type="text" class="form-control" name="privatekey" placeholder="Private Key" required autofocus>
							</div>
							<div class="help-info">Choose your private key to secure your DirectAdmin and login credentials</div>
						</div>
						<div class="input-group">
							<span class="input-group-addon">
								<i class="material-icons">public</i>
							</span>
							<div class="form-line">
								<input type="text" class="form-control" name="baseurl" placeholder="Base URL" required>
							</div>
							<div class="help-info">Where you have this script installed "example: mysecurewebsite.tld/subfolder"</div>
						</div>
				</div>
				<div class="col-md-6">						
						<div class="msg">Login options </div>
						<div class="input-group">
							<span class="input-group-addon">
								<i class="material-icons">person</i>
							</span>
							<div class="form-line">
								<input type="text" class="form-control" name="username" placeholder="Username" required>
							</div>
							<div class="help-info">Username for Certificate Manager</div>
						</div>
						<div class="input-group">
							<span class="input-group-addon">
								<i class="material-icons">lock</i>
							</span>
							<div class="form-line">
								<input type="password" class="form-control" name="password" placeholder="Password" required>
							</div>
						<div class="help-info">Password for Certificate Manager</div>
						</div>
				</div>
			</div>
			<div class="row m-b--20">
				<div class="col-md-6">
					<div class="msg">DirectAdmin options </div>
					<div class="input-group">
						<span class="input-group-addon">
							<i class="material-icons">public</i>
						</span>
						<div class="form-line">
							<input type="text" class="form-control" name="dahost" placeholder="DirectAdmin Host">
						</div>
						<div class="help-info">Connection to DirectAdmin server "example: directadminhost.tld" <br>for ssl connecction start with : ssl://</div>
					</div>
					<div class="input-group">
						<span class="input-group-addon">
							<i class="material-icons">person</i>
						</span>
						<div class="form-line">
							<input type="text" class="form-control" name="dauser" placeholder="DirectAdmin Username">
						</div>
						<div class="help-info">DirectAdmin Username</div>
					</div>
					<div class="input-group">
						<span class="input-group-addon">
							<i class="material-icons">lock</i>
						</span>
						<div class="form-line">
							<input type="password" class="form-control" name="dapass"  placeholder="DirectAdmin Password">
						</div>
						<div class="help-info">DirectAdmin Password</div>
					</div>
				</div>
				<div class="col-md-6">
				<div class="msg">Let's Encrypt options </div>
					<div class="input-group">
						<span class="input-group-addon">
							<i class="material-icons">folder</i>
						</span>
						<div class="form-line">
							<input type="text" class="form-control" name="ledomain" placeholder="Domains Folder" value="<?php echo strstr(__DIR__, "domains", true)."domains/"; ?>">
						</div>
						<div class="help-info">Folder where the domains are stored "example: /home/dausername/domains".</div>
					</div>
					<div class="input-group">
						<span class="input-group-addon">
							<i class="material-icons">folder</i>
						</span>
						<div class="form-line">
							<input type="text" class="form-control" name="lefolder" placeholder="Let's Encrypt Folder" value="<?php echo "/letsencrypt"; ?>">
						</div>
						<div class="help-info">Folder where the certificates are stored "example: /letsencrypt".</div>
					</div>
					<div class="input-group">
						<span class="input-group-addon">
							<i class="material-icons">folder</i>
						</span>
						<div class="form-line">
							<input type="text" class="form-control" name="lepublic"  placeholder="Public HTML Folder" value="<?php echo "/public_html"; ?>">
						</div>
						<div class="help-info">Folder where the public html is set "example: /public_html"</div>
					</div>
				</div>
				
			</div>
			<div class="row">
				<div class="col-xs-12">
					<button class="btn btn-block bg-pink waves-effect" type="submit">Install</button>
				</div>
			</div>
					</form>
			
			<?php } else { ?>
				<div class="row m-t-15 m-b--20">
					<div class="col-xs-12">
						<div class="alert bg-red">
                            Install is not allowed, set <pre> [root] <br> install = true </pre>in config file
                        </div>
					</div>
				</div>
			<?php } ?>
				<div class="row m-t-15 m-b--20">
					<div class="col-xs-12 align-right">
						<small>Made By <a target="_BLANK" href="https://www.tieme-alberts.nl">Tieme Alberts</a></small>
					</div>
				</div>
			
            </div>
        </div>
    </div>

    <!-- Jquery Core Js -->
    <script src="assets/plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="assets/plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="assets/plugins/node-waves/waves.js"></script>

    <!-- Custom Js -->
    <script src="assets/js/admin.js"></script>
</body>

</html>