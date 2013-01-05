<?php
	if ( !defined('SROOT')) {
		require_once('../engine/connect.php');
	}
	require_once( SROOT .'engine/classes/GT8.php');
	
	if ( isset($_GET['action'])) {
		switch( $_GET['action']){
			case 'send-mail': {
				require_once( SROOT .'engine/mail/Mail.php');
				if ( isset($_POST['message'])) {
					$m	= new Mail(100, 'JSON');
					$m->from	= array('www@funicar.com.br', utf8_decode('Funicar | Ferramentas de restauração'));
					$m->statusId	= 1;
					$m->printAfterSending	= false;
					$m->copyOnDb	= false;
					$_GET['message']	= substr($_POST['message'], 0, 2024);
					$m->send($_GET);
					
					die();
				}
			}
			default: {
				die('//#error: Não foi possível completar esta operação!'. PHP_EOL);
				break;
			}
		}
	}
?>