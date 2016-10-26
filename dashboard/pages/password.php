<?php

if($_GET['action'] && $_GET['action'] == "changepw"){

	if($_POST['newpassword'] === $_POST['rnewpassword']){
		
		$key = $config->get('root','privatekey');
		$curconfig = $config->getSection('dashboard');
		
		if($func->encrypt($_POST['curpassword'],$key) == $curconfig['PASSWORD']){

			$pass = $func->encrypt($_POST['curpassword'],$key);
			
			$dashboard = $config->setSection('dashboard', array(
									'USERNAME' => $curconfig['USERNAME'],
									'PASSWORD' => $pass,
									'MAX_TRIES' => $curconfig['MAX_TRIES'],
									'UNLIMITED_TRIES' => $curconfig['UNLIMITED_TRIES']
								));
								
			$config->write('../config/config.ini', $dashboard);
		
			$mess = array('1','New password is set.');
			
		} else {
			$mess = array('2','Current password does not match.');
		}

	} else {
		$mess = array('3','New password does not match.');
	}
	
}
?>


<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="card">
			<div class="header">
				<h2>
					Change Password
				</h2>
			</div>
			<div class="body">
				<?php 
				if($mess[0] == 1){
					echo '<div class="alert alert-success"><strong>'.$mess[1].'</strong></div>';
				} elseif($mess[0] == 2){
					echo '<div class="alert alert-warning"><strong>'.$mess[1].'</strong></div>';
				} elseif($mess[0] == 3){
					echo '<div class="alert alert-warning"><strong>'.$mess[1].'</strong></div>';
				}	
				?>
				<form class="form-horizontal" action="?page=password&action=changepw" method="POST">
					<div class="row clearfix">
						<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
							<label for="password_1">Current Password</label>
						</div>
						<div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
							<div class="form-group">
								<div class="form-line">
									<input type="password" id="password_1" class="form-control" name="curpassword" placeholder="Enter your current password">
								</div>
							</div>
						</div>
					</div>
					<div class="row clearfix">
						<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
							<label for="password_2">New Password</label>
						</div>
						<div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
							<div class="form-group">
								<div class="form-line">
									<input type="password" id="password_2" class="form-control" name="newpassword" placeholder="Enter your new password">
								</div>
							</div>
						</div>
					</div>
					<div class="row clearfix">
						<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
							<label for="password_3">Repeat New Password</label>
						</div>
						<div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
							<div class="form-group">
								<div class="form-line">
									<input type="password" id="password_3" class="form-control" name="rnewpassword" placeholder="Repeat your new password">
								</div>
							</div>
						</div>
					</div>
					<div class="row clearfix">
						<div class="col-lg-offset-2 col-md-offset-2 col-sm-offset-4 col-xs-offset-5">
							<button type="submit" class="btn btn-primary m-t-15 waves-effect">Save</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>