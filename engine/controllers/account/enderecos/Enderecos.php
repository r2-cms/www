<?php
	if ( !defined('SROOT') ) {
		require_once('../../connect.php');
	}
	
	require_once( SROOT ."engine/controllers/account/Account.php");
	
	class Enderecos extends Account {
		public function __construct() {
			global $GT8;
			
			$this->checkActionRequest();
			$this->setFields();
		}
		protected function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				$idLogin	= (integer)$_SESSION['login']['id'];
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