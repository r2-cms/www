<?php
	if ( !defined('SROOT') ) {
		require_once('../../connect.php');
	}
	
	require_once( SROOT ."engine/controllers/account/Account.php");
	
	class Confirmacao extends Account {
		public function __construct() {
			global $GT8;
			
		}
	}
?>