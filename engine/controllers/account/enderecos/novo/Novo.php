<?php
	if ( !defined('SROOT') ) {
		require_once('../../connect.php');
	}
	
	require_once( SROOT ."engine/controllers/account/Account.php");
	
	class Novo extends Account {
		public function __construct() {
			global $GT8;
			
			$this->checkActionRequest();
			$this->setFields();
		}
		private function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				$idLogin	= (integer)$_SESSION['login']['id'];
				if ( $_GET['action'] == 'create-address') {
					
					$zip		= RegExp($_GET['zip'], '[0-9]{5}\-[0-9]{3}');
					
					require_once( SROOT .'engine/queries/address/getAddressByZip.php');
					$zrow	= Zip::getZip($zip);
					
					require_once( SROOT .'engine/queries/address/addNewAddress.php');
					addNewAddress(array(
						'log'			=> false,
						'idUser'		=> $_SESSION['login']['id'],
						'type'			=> 1,
						'street'		=> utf8_decode($zrow['logradouro']),
						'number'		=> '',
						'complement'	=> '',
						'reference'		=> '',
						'zip'			=> $zip,
						'district'		=> utf8_decode($zrow['bairro']),
						'city'			=> utf8_decode($zrow['cidade']),
						'stt'			=> $zrow['estado'],
						'format'		=> 'JSON'
					));
					die();
				}
			}
		}
		private function setFields() {
			
			require_once(SROOT .'engine/functions/Pager.php');
			$Pager	= Pager(array(
				'sql'	=> 'address.list',
				'ids'	=> array(
					array('a.id_users', $_SESSION['login']['id'])
				)
			));
			$this->data['addresses']	= $Pager['rows'];
		}
	}
?>