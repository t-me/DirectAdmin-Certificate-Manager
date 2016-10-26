<?php
error_reporting(-1);
ini_set('display_errors', 'On');

require_once "../lib/login/login.php";
$authentication = new quickprotect();
$authentication->checkLoginAndDirect();

require_once '../lib/functions.php';
$func = new cm_function();

require_once '../lib/config.php';
$config = new Config_Lite('../config/config.ini');

include 'elements/header.php';
include 'elements/sidebar.php';
?>

	<section class="content">
		<div class="container-fluid">
		<?php
			$page = '';
			if ($_GET){
				$page = $_GET['page'];
				if($page == 'password'){
					include 'pages/password.php';
				}elseif($page == 'domaininfo'){
					include 'pages/domaininfo.php';
				}elseif($page == 'settings'){
					include 'pages/settings.php';
				}elseif($page == 'encrypt'){
					include 'pages/encrypt.php';
				}else{
					include 'pages/dashboard.php';
				}
			}else{
				include 'pages/dashboard.php';
			}
		?>
		</div>
	</section>

<?php
include 'elements/footer.php';
?>
