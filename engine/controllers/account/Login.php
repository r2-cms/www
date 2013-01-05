<?php
	if ( !defined('SROOT') ) {
		header('location: ../');
		die('');
	}
	if ( !isset($GT8)) {
		global $GT8;
	}
	
	require_once(SROOT.'engine/classes/GT8.php');
	
	GT8::enterSSL();
	
	class Login extends GT8 {
		static $includeCustomView	= '';
		public function __construct() {
			global $spath, $GT8;
			
			require_once( SROOT ."engine/functions/CheckLogin.php");
			header("Cache-Control: no-cache, must-revalidate");
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
			
			//se o usuário tiver sessão iniciada, não há necessidade de entrar na página de login novamente. 
			if ( isset($_SESSION['login']['id']) && $_SESSION['login']['id'] && strpos('##'. $_SERVER['REQUEST_URI'], $GT8['account']['root'] . $GT8['account']['login']['root']) ) {
				header('location: '. CROOT . $GT8['account']['root']);
				die();
			}
			parent::GT8();
		}
	}
?>