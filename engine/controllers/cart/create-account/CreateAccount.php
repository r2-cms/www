<?php
	require_once( SROOT .'engine/controllers/account/register/Register.php');
	
	class CreateAccount extends Register {
		public function __construct() {
			
			parent::__construct();
		}
		protected function redirectToAccount() {
			global $GT8;
			
			header('location: ../'. $GT8['cart']['delivery']['root']);
			die();
		}
	}
?>