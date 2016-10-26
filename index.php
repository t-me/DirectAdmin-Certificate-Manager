<?php
$filename = 'config/config.ini';
if (!file_exists($filename)) {
		header ("Location: install.php");
	die();
}


require_once "lib/login/login.php";
$authentication = new quickprotect();

if ($_GET['do']=="logout") {
    $authentication->logout();
}

if($authentication->is_logged_in() === TRUE){
	header("Location: dashboard/");
	die();
}

if (isset($_SESSION['goAfterLogin'])){
    $goto = $_SESSION['goAfterLogin'];
    unset($_SESSION['goAfterLogin']);
}
else $goto = 'dashboard/';

if (isset($_POST['username'])) {
    if($authentication->login($_POST['username'], $_POST['password'])) header ("Location: $goto");
}

include('login.php');