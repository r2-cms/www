<?php
	require_once( SROOT ."engine/controllers/account/Account.php");
	
	class Security extends Account {
		public function __construct() {
			global $GT8;
			
			$this->checkActionRequest();
			$this->setFields();
		}
		protected function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				$idLogin	= (integer)$_SESSION['login']['id'];
				
				if ( isset($_POST['pass'])) {
					
					$pass	= RegExp($_POST['pass'], '[a-zA-Z0-9]+');
					
					if ( $pass === $_POST['pass']) {
						mysql_query("
							UPDATE
								gt8_users
							SET
								pass	= '". $pass ."'
							WHERE
								id	= $idLogin
						") or die('//#error: Erro no servidor. Por favor, tente mais tarde.'. PHP_EOL);
					}
					print('//#affected rows: 1'. PHP_EOL);
					print('//#message: Senha alterada com sucesso!'. PHP_EOL);
					die();
				}
			}
		}
		private function setFields() {
			
		}
	}
?>