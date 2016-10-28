<?php
require_once 'directadmin/httpsocket.php';

Class cm_function {
	
    Public Function __construct() {

    }

##
 # Global Functions
##
	
	Private Function Get_Config($section){
		require_once __DIR__ . "/config.php";
		$config = new Config_Lite(__DIR__ . "/../config/config.ini");
		return $config->getSection($section);
	}
	
	Public Function encrypt($string,$key){
		
		return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
		 
	}
	
	Public Function decrypt($string,$key){

		return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
		
	}

	Public Function CheckForUpdates(){
		#
		# Still working on this. 
		# dont want to get a request every time you load a new page.
		#
		$LiveUpdateBase = "https://update.tieme-alberts.nl/dacm.txt";
				
		$LatestVersion = @file_get_contents($LiveUpdateBase);
		$CurrentVersion = 0.1;

		if (version_compare($CurrentVersion, $LatestVersion, '<')){
			return '<li><a href="https://github.com/t-me/DireactAdmin-Certificate-Manager" target="_blank" class="waves-effect waves-block"><i class="material-icons col-red">donut_large</i><span>UPDATE AVAILABLE</span></a></li>';
		}
			
	}
##
 # DirectAdmin Functions
##

	Private Function DA_CONNECT(){
		
		$Rconfig = $this->Get_Config('root');
		$DAconfig = $this->Get_Config('directadmin');
		
		$da = new \DirectAdmin();
		$da->connect($DAconfig['HOST'], 2222);
		$da->set_login($DAconfig['USERNAME'], $this->decrypt($DAconfig['PASSWORD'],$Rconfig['privatekey']));
		
		return $da;
	}
	
	Public Function DA_GET_DOMAINS(){
		
		$da = $this->DA_CONNECT();
		$da->query('/CMD_API_SHOW_DOMAINS');
		$domains = $da->fetch_parsed_body();
		
		return $domains['list'];
	}
	
	Public Function DA_GET_SUB_DOMAINS($domain){
		
		$da = $this->DA_CONNECT();
		$da->query('/CMD_API_SUBDOMAINS',
			array(
				'domain' => $domain
			));
		$domains = $da->fetch_parsed_body();
		
		return $domains['list'];
	}
	
	Public Function DA_GET_DOMAINS_MENU(){
		
		$da = $this->DA_CONNECT();
		$da->query('/CMD_API_SHOW_DOMAINS');
		$domains = $da->fetch_parsed_body();
		
		$menu_item = '';
		foreach ($domains['list'] as $domain){
			$menu_item .= '<li><a href="?page=domaininfo&domain='.$domain.'" class=" waves-effect waves-block">'.$domain.'</a></li>';
		}
		return $menu_item;
	}
	
	Public Function DA_GET_SSL_INFO($domain){
		
		$da = $this->DA_CONNECT();
		$da->query('/CMD_API_SSL',
			array(
				'domain' => $domain
			));
		$ssl = $da->fetch_parsed_body();

		if($ssl["ssl_on"] == 'yes'){
			return openssl_x509_parse($ssl["certificate"]);
		} else {
			return false;
		}	
	}
	
	Public Function DA_GET_DOMAIN($domain){
		
		$da = $this->DA_CONNECT();
		$da->query('/CMD_API_ADDITIONAL_DOMAINS',array(
				'action' => 'view',
				'domain' => $domain
			));
		$domaininfo = $da->fetch_parsed_body();
		
		return $domaininfo;
	}
	
##
 # SSL CERT Functions
##

	Public Function SSL_PARSE_DATE($date){
		$GMT = new DateTimeZone('Europe/Amsterdam');
		$date =  new DateTime($this->formatValidityString($date), $GMT);
		return $date->format('l d F Y');
	}

	Public Function formatValidityString($dateTime) {
		if(strlen($dateTime) > 13) {
			$dateTime = substr($dateTime, 0, 8).'T'.substr($dateTime, 8, -1);
		} else {
			$dateTime = substr($dateTime, 0, 6).'T'.substr($dateTime, 6, -1);
			if(substr($dateTime, 0, 2) >= 50)
				$dateTime = '19'.$dateTime;
			else
				$dateTime = '20'.$dateTime;
		}
		return $dateTime;
	}
	
	Public Function SSL_STATUS($SSL_info,$option){
		if ($option == 'small'){
			if($SSL_info == false){
					return '<a style="margin-left: -10px;margin-top:-15px;margin-right: 10px;" class="btn btn-info btn-circle-lg waves-effect waves-circle waves-float"><i class="material-icons">live_help</i></a>';
			}
			
			$dateto = new DateTime($this->formatValidityString($SSL_info['validTo']));
			$datenow = new datetime();
			$datediff = $dateto->diff($datenow);
			
			if($datediff->format('%R') == '-'){
				$days = $datediff->format('%a');
				if ($days < 15){
					return '<a style="margin-left: -10px;margin-top:-15px;margin-right: 10px;" class="btn btn-warning btn-circle-lg waves-effect waves-circle waves-float"><i class="material-icons">warning</i></a>';
				} else {
					return '<a style="margin-left: -10px;margin-top:-15px;margin-right: 10px;" class="btn btn-success btn-circle-lg waves-effect waves-circle waves-float"><i class="material-icons">verified_user</i></a>';
				}
			} else{
					return '<a style="margin-left: -10px;margin-top:-15px;margin-right: 10px;" class="btn btn-danger btn-circle-lg waves-effect waves-circle waves-float"><i class="material-icons">error</i></a>';
			}
		} elseif ($option == 'big'){
			if($SSL_info == false){
				return '<div class="alert alert-info">No SSL Certificate found</div>';
			}
			
			$dateto = new DateTime($this->formatValidityString($SSL_info['validTo']));
			$datenow = new datetime();
			$datediff = $dateto->diff($datenow);
			
			if($datediff->format('%R') == '-'){
				$days = $datediff->format('%a');
				if ($days < 15){
					return '<div class="alert alert-warning">SSL Certificate expires in <strong>'.$days.'</strong> dagen</div>';
				} else {
					return '<div class="alert alert-success">SSL Certificate expires in <strong>'.$days.'</strong> days</div>';
				}
			} else{
				return '<div class="alert alert-danger">ERROR</div>';
			}
	
		}
	}
	
	Public Function SSL_SET_CERT($dir,$domain){
		
		$cert_location = $dir.'/'.$domain;
		
		$cert_key = file_get_contents($cert_location.'/private.pem');
		$cert_chain = file_get_contents($cert_location.'/fullchain.pem');
		
		$certificate = $cert_key.$cert_chain;
		$certificate = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $certificate);
		
		//$domaininfo = $this->DA_GET_DOMAIN($domain);
		
		$da = $this->DA_CONNECT();
		
		$da->method = "POST";

		$da->query('/CMD_API_SSL',
			array(
				'domain' => $domain,
				'action' => 'save',
				'type' => 'paste',
				'certificate' => $certificate
			));
		$resultPasteSSL = $da->fetch_parsed_body();
	/*
		$da->query('/CMD_API_DOMAIN',
			array(
				'action' => 'modify',
				'bandwidth'=> $domaininfo['bandwidth'],
				'quota'=> $domaininfo['quota'],
				'domain' => $domaininfo['domain'],
				'ssl' => 'ON',
				'cgi' => $domaininfo['cgi'],
				'php' => $domaininfo['php']
			));
			
		$resultActivateSSL = $da->fetch_parsed_body();
		
		return array($resultPasteSSL,$resultActivateSSL);
	*/	
		return $resultPasteSSL;
	}
}