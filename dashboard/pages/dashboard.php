<?php

	$domains = $func->DA_GET_DOMAINS();

	$i=0;
	foreach($domains as $domain){
		
	$SSL_info = $func->DA_GET_SSL_INFO($domain);
?>

	<div class="row clearfix">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="card">
				<div class="header">
					<div class="pull-left">
						<?php echo $func->SSL_STATUS($SSL_info,'small'); ?>
					</div>
					<h2 style="text-transform: uppercase;"><?php echo $domain; ?></h2>
					<div class="pull-right">
						<div class="switch panel-switch-btn">
							<span class="m-r-10 font-12">DETAILS</span>
							<label>
								<input type="checkbox" id="realtime" data-toggle="collapse" data-target="#<?php echo 'panel'.$i; ?>" aria-expanded="false" aria-controls="<?php echo 'panel'.$i; ?>">
								<span class="lever switch-col-light-green"></span>
							</label>
						</div>
					</div>
				</div>
				<div class="body collapse" id="<?php echo 'panel'.$i; ?>" >
				
					<div class="row clearfix">
						<div class="col-md-6">
							<?php
								echo $func->SSL_STATUS($SSL_info,'big');
							?>
						</div>
						<div class="col-md-6">
							<?php
								if($SSL_info == false){
							?>
								<a class="btn btn-block btn-lg btn-default waves-effect" href="?page=encrypt&domain=<?php echo $domain; ?>&step=1">Register SSL Certificate</a>
							<?php
								} elseif($SSL_info['issuer']['O'] == 'Let\'s Encrypt') {
							?>
								<a class="btn btn-block btn-lg btn-default waves-effect" href="?page=encrypt&domain=<?php echo $domain; ?>&step=1">Renew SSL Certificate</a>
							<?php
								}
							?>
						</div>
					</div>
					<?php
						if($SSL_info){
					?>
					<div class="row clearfix">
						<div class="col-md-12">	
							<table class="table table-bordered table-striped table-condensed">
								<thead>
									<tr>
										<th class="left">Property</th>
										<th class="left">Value</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="rdn">Common Name (CN)</td>
										<td class="value">
											<?php 
												echo $SSL_info['subject']['CN']; 
												if($SSL_info['subject']['CN'][0] == "*")
												{
													echo " - (Wildcard)";
												}
											?>
										</td>
									</tr>
									<tr>
										<td class="subject">Issuer Common name</td>
										<td class="value"><?php echo $SSL_info['issuer']['CN']; ?></td>
									</tr>
									<tr>
										<td class="subject">Organization</td>
										<td class="value"><?php echo $SSL_info['issuer']['O']; ?></td>
									</tr>
									<tr>
										<td class="subject">Issuer Country</td>
										<td class="value"><?php echo $SSL_info['issuer']['C']; ?></td>
									</tr>							
									<tr>
										<td class="subject">Valid From</td>
										<td class="value"><?php echo $func->SSL_PARSE_DATE($SSL_info['validFrom']); ?></td>
									</tr>
									<tr>
										<td class="subject">Valid Till</td>
										<td class="value"><?php echo $func->SSL_PARSE_DATE($SSL_info['validTo']); ?></td>
									</tr>
									<tr>
										<td class="subject">Serial Number</td>
										<td class="value"><?php echo $SSL_info['serialNumber']; ?></td>
									</tr>      
									<tr>
										<td class="subject">SANS</td>
										<td class="value">
											<?php
												$AN = $SSL_info['extensions']['subjectAltName'];
												$AN = str_replace("DNS:","",$AN);
												$ANS = explode(",", $AN);
												foreach ($ANS as $AN){
													echo '<div style="font-family:monospace;width:100%">'.$AN.'</div>';
												}
											?>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<?php
						}
					?>
				</div>
			</div>
		</div>
	</div>


<?php
	$i++;
	}
?>