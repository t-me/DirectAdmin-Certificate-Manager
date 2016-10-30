<?php
$domain_menu = $func->DA_GET_DOMAINS_MENU();

$page = '';
if($_GET){
	$page = $_GET['page'];	
}
?>
<section>
	<!-- Left Sidebar -->
	<aside id="leftsidebar" class="sidebar">
		<!-- Menu -->
		<div class="menu">
			<ul class="list">
				<li class="header">MAIN NAVIGATION</li>
				<li <?php if(!$page){echo 'class="active"';} ?>>
					<a href="../">
						<i class="material-icons">home</i>
						<span>Dashboard</span>
					</a>
				</li>
				<?php if($domain_menu){?>
					<li <?php if($page==='domaininfo'){echo 'class="active"';} ?>>
						<a href="javascript:void(0);" class="menu-toggle waves-effect waves-block">
							<i class="material-icons">public</i>
							<span>Domains</span>
						</a>
						<ul class="ml-menu" style="display: none;">
							<?php echo $domain_menu;?>
						</ul>
					</li>
				<?php } ?>
				<li <?php if($page==='settings'){echo 'class="active"';} ?>>
					<a href="?page=settings">
						<i class="material-icons">settings</i>
						<span>Settings</span>
					</a>
				</li>					
				<li <?php if($page==='password'){echo 'class="active"';} ?>>
					<a href="?page=password	">
						<i class="material-icons">vpn_key</i>
						<span>Change Password</span>
					</a>
				</li>
				<li>
					<a href="../?do=logout">
						<i class="material-icons">input</i>
						<span>Sign Out</span>
					</a>
				</li>
				<?php echo $func->CheckForUpdates(); ?>
			</ul>
		</div>
		<!-- #Menu -->
		<!-- Footer -->
		<div class="legal">
			<div class="copyright">
				&copy; 2016 <a target="_blank" href="https://www.tieme-alberts.nl/">Tieme Alberts</a>.
			</div>
			<div class="copyright">
				<small><a target="_BLANK" href="https://github.com/t-me/direactadmin-certificate-manager">GitHub Project Page.</a></small>
			</div>
			<div class="version">
				<b>Version: </b> 0.1
			</div>
		</div>
		<!-- #Footer -->
	</aside>
	<!-- #END# Left Sidebar -->
</section>
