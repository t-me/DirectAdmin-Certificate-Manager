<?php
	$key = $config->get('root','privatekey');
	$daconfig = $config->getSection('directadmin');
	$leconfig = $config->getSection('letsencrypt');
	
	if(isset($_GET['action'])){
		if($_GET['action'] == "changeda"){
			
			if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
				
				$dapass = $func->encrypt($_POST['dapass'],$key);
				
				$daconfigs = $config->setSection('directadmin', array(
					'HOST' => $_POST['dahost'],
					'USERNAME' => $_POST['dauser'],
					'PASSWORD' => $dapass,
				));
									
				$config->write(__DIR__ . "/../../config/config.ini", $daconfigs);

				$da_mess = array('1','DirectAdmin settings changed.');
				$daconfig = $config->getSection('directadmin');
			}
		}
		
		if($_GET['action'] == "changele"){
			
			if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
					
				$leconfigs = $config->setSection('letsencrypt', array(
					'DOMAINFOLDER' => $func->LE_Dir_Trailing_Slash(trim($_POST['ledomain'])),
					'LETSFOLDER' => $func->LE_Dir_Leading_Slash(trim($_POST['lefolder'])),
					'PUBLICFOLDER' => $func->LE_Dir_Leading_Slash(trim($_POST['lepublic'])),
				));
									
				$config->write(__DIR__ . "/../../config/config.ini", $leconfigs);

				$le_mess = array('1','Let\'s Encrypt settings changed.');
				$leconfig = $config->getSection('letsencrypt');
			}
		}
	}
?>

<div class="row clearfix">
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<div class="card">
			<div class="header">
				<h2>DirectAdmin Settings</h2>
			</div>
			<div class="body">
				<?php 
				if(isset($da_mess)){
					echo '<div class="alert alert-success"><strong>'.$da_mess[1].'</strong></div>';
				}
				?>
				<form class="form-horizontal" action="?page=settings&action=changeda" method="POST">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="material-icons">public</i>
						</span>
						<div class="form-line">
							<input type="text" class="form-control" name="dahost" placeholder="DirectAdmin Host" value="<?php echo $daconfig['HOST']; ?>">
						</div>
						<div class="help-info">DirectAdmin Host adres "for ssl connecction start with : ssl://"</div>
					</div>
					<div class="input-group">
						<span class="input-group-addon">
							<i class="material-icons">person</i>
						</span>
						<div class="form-line">
							<input type="text" class="form-control" name="dauser" placeholder="DirectAdmin Username" value="<?php echo $daconfig['USERNAME']; ?>">
						</div>
						<div class="help-info">DirectAdmin Username</div>
					</div>
					<div class="input-group">
						<span class="input-group-addon">
							<i class="material-icons">lock</i>
						</span>
						<div class="form-line">
							<input type="password" class="form-control" name="dapass"  placeholder="DirectAdmin Password" value="<?php echo $func->decrypt($daconfig['PASSWORD'],$key); ?>">
						</div>
						<div class="help-info">DirectAdmin Password</div>
					</div>
					<div class="clearfix">
						<button type="submit" class="btn btn-primary m-t-15 waves-effect pull-right">Save DirectAdmin Settings</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<div class="card">
			<div class="header">
				<h2>Let's Encrypt Settings</h2>
			</div>
			<div class="body">
				<?php 
				if(isset($le_mess)){
					echo '<div class="alert alert-success"><strong>'.$le_mess[1].'</strong></div>';
				}
				?>
				<form class="form-horizontal" action="?page=settings&action=changele" method="POST">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="material-icons">folder</i>
						</span>
						<div class="form-line">
							<input type="text" class="form-control" name="ledomain" placeholder="Domains Folder" value="<?php echo $leconfig['DOMAINFOLDER']; ?>">
						</div>
						<div class="help-info">Folder where the domains are stored "example: /home/dausername/domains".</div>
					</div>
					<div class="input-group">
						<span class="input-group-addon">
							<i class="material-icons">folder</i>
						</span>
						<div class="form-line">
							<input type="text" class="form-control" name="lefolder" placeholder="Let's Encrypt Folder" value="<?php echo $leconfig['LETSFOLDER']; ?>">
						</div>
						<div class="help-info">Folder where the certificates are stored "example: /letsencrypt".</div>
					</div>
					<div class="input-group">
						<span class="input-group-addon">
							<i class="material-icons">folder</i>
						</span>
						<div class="form-line">
							<input type="text" class="form-control" name="lepublic"  placeholder="Public HTML Folder" value="<?php echo $leconfig['PUBLICFOLDER']; ?>">
						</div>
						<div class="help-info">Folder where the public html is set "example: /public_html"</div>
					</div>
					<div class="clearfix">
						<button type="submit" class="btn btn-primary m-t-15 waves-effect pull-right">Save Let's Encrypt Settings</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>