<?php
error_reporting(-1);
ini_set('display_errors', 'On');

require_once __DIR__ . "/../lib/login/login.php";
$authentication = new quickprotect();
$authentication->checkLoginAndDirect();

require_once __DIR__ . "/../lib/functions.php";
$func = new cm_function();

require_once __DIR__ . "/../lib/config.php";
$config = new Config_Lite(__DIR__ . "/../config/config.ini");

include __DIR__ . "/elements/header.php";
include __DIR__ . "/elements/sidebar.php";
?>

	<section class="content">
		<div class="container-fluid">
		<?php
			$page = '';
			if ($_GET){
				$page = $_GET['page'];
				if($page == 'password'){
					include __DIR__ . "/pages/password.php";
				}elseif($page == 'domaininfo'){
					include __DIR__ . "/pages/domaininfo.php";
				}elseif($page == 'settings'){
					include __DIR__ . "/pages/settings.php";
				}elseif($page == 'encrypt'){
					include __DIR__ . "/pages/encrypt.php";
				}else{
					include __DIR__ . "/pages/dashboard.php";
				}
			}else{
				include __DIR__ . "/pages/dashboard.php";
			}
		?>
		</div>
	</section>

<?php
include __DIR__ . "/elements/footer.php";
?>
