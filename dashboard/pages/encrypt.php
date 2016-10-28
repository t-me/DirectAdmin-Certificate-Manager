<?php

	$key = $config->get('root','privatekey');
	$daconfig = $config->getSection('directadmin');
	$leconfig = $config->getSection('letsencrypt');
	
	$domain = $_GET['domain'];
	$step = $_GET['step'];

	if(!$domain || !$step){
		header('Location: /');  
	}

?>

<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="card">
			<div class="header">
				<h2>Let's Encrypt <span class="label bg-blue pull-right">Step <?php echo $step; ?></span></h2>
			</div>
			<div class="body">

				<?php if($step==1){ ?>
				<h2>Select Domains</h2>
				<form class="form-horizontal" action="?page=encrypt&domain=<?php echo $domain; ?>&step=2" method="POST">
					<select id='domain-keep-order' name="certdomains[]" multiple='multiple'>
						<?php 
							$subs = $func->DA_GET_SUB_DOMAINS($domain);
							foreach($subs as $sub){
								$subdomains[] = $sub . '.' . $domain;
							}
							$domainsarr = array($domain, 'www.'.$domain);
							$alldomains = array_merge($domainsarr, $subdomains);
						
							foreach($alldomains as $certdomain){
								if($certdomain == 'www.'.$domain || $certdomain == $domain){
									echo '<option value="'.$certdomain.'" selected>'.$certdomain.'</option>';
								}else{
									echo '<option value="'.$certdomain.'">'.$certdomain.'</option>';
								}
							}
						?>
					</select>
					<div class="clearfix">
						<button type="submit" class="btn btn-primary m-t-15 waves-effect pull-right">Go To Step 2</button>
					</div>
				</form>
				<?php }elseif($step==2){ ?>
				
				<form class="form-horizontal" action="?page=encrypt&domain=<?php echo $domain; ?>&step=3" method="POST">
					<div class="row clearfix">
						<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
							<label for="email_address_2">Let's Encrypt Certificate directory</label>
						</div>
						<div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
							<div class="form-group">
								<div class="form-line">
									<input type="text" name="lefolder" class="form-control" value="<?php echo $leconfig['DOMAINFOLDER'] .$domain. $leconfig['LETSFOLDER']; ?>" readonly >
								</div>
							</div>
						</div>
					</div>
					<div class="row clearfix">
						<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
							<label for="password_2">Domain Public directory</label>
						</div>
						<div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
							<div class="form-group">
								<div class="form-line">
									<input type="text" name="pufolder" class="form-control" value="<?php echo $leconfig['DOMAINFOLDER'] .$domain. $leconfig['PUBLICFOLDER']; ?>" readonly >
								</div>
							</div>
						</div>
					</div>
					<div class="row clearfix">
						<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
							<label for="password_2">Domains</label>
						</div>
						<div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
							<div class="form-group">
								<div class="form-line">
									<input type="text" name="domains" class="form-control"  value="<?php echo implode(", ", $_POST['certdomains']); ?>" readonly >
								</div>
							</div>
						</div>
					</div>
					<div class="clearfix">
						<button type="submit" class="btn btn-primary m-t-15 waves-effect pull-right">Go To Step 3 (Create Certificates)</button>
					</div>
				</form>
				
				<?php 
				}elseif($step==3){

					require_once __DIR__ . "/../../lib/letsencrypt/lescript.php";
					require_once __DIR__ . "/../../lib/letsencrypt/logger.php";
					
					$logger = new Logger();
					$maindomain = $domain;
					
					echo '<pre>';
					try {
						
						$le = new \Lescript($_POST['lefolder'], $_POST['pufolder'], $logger);
						$le->initAccount();
						$le->signDomains(preg_split("/[\s,]+/",$_POST['domains']),$maindomain);

					} catch (\Exception $e) {
						
						$logger->error($e->getMessage());
						$logger->error($e->getTraceAsString());

					}
					echo '</pre>';
				?>
				<form class="form-horizontal" action="?page=encrypt&domain=<?php echo $domain; ?>&step=4" method="POST">
					<input type="hidden" name="lefolder" value="<?php echo $_POST['lefolder'] ?>">
					<div class="clearfix">
						<button type="submit" class="btn btn-primary m-t-15 waves-effect pull-right">Go To Step 4 (Install Certificates in DirectAdmin)</button>
					</div>
				</form>
				<?php
				}elseif($step==4){
					$CERT_RESPONCE = $func->SSL_SET_CERT($_POST['lefolder'],$domain);
					
					if($CERT_RESPONCE['error']== 1){
						echo '<div class="alert alert-danger"><strong>Oh snap!</strong> Something went wrong. ('.$CERT_RESPONCE['text'].')</div>';
					} else {
						
						echo '<div class="alert alert-success"><strong>Well done!</strong> '.$CERT_RESPONCE['text'].'</div>';
						echo '<br>';
						echo '<h3>Certificate</h3>';
						echo '<pre>';
								print_r(str_replace("certificate=", "", $CERT_RESPONCE['details']););
						echo '</pre>';
						echo '<br>';
						echo '<h3>Key</h3>'
						echo '<pre>';
								print_r($CERT_RESPONCE['key']);
						echo '</pre>';
						
					}

				}
				?>
			</div>
		</div>
	</div>
</div>



