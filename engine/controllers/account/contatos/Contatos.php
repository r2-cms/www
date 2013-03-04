<?php
	if ( !defined('SROOT') ) {
		require_once('../../connect.php');
	}
	
	require_once( SROOT ."engine/controllers/account/Account.php");
	
	class Contatos extends Account {
		public function __construct() {
			global $GT8;
			
			$this->checkActionRequest();
			require_once( SROOT .'engine/functions/Pager.php');
			$this->setContacts();
			
			$this->data['primary-mail']	= $_SESSION['login']['login'];
		}
		private function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				$idLogin	= (integer)$_SESSION['login']['id'];
				if ( $_GET['action'] == 'save-account-contacts') {
					$count	= 0;
					foreach ($_GET AS $name=>$value) {
						if ( substr(strtolower($name), 0, 8) === 'channel-') {
							$count++;
							$id	= (integer)substr($name, 8);
							$value	= mysql_real_escape_string($value);
							
							mysql_query("
								UPDATE
									gt8_users_contact
								SET
									value	= '$value'
								WHERE
									1 = 1
									AND id = $id
									AND id_users = $idLogin
							". PHP_EOL) or die('//#error: Erro interno. Por favor, tente mais tarde'. PHP_EOL);
						}
					}
					print("//#affected: ($count)". PHP_EOL);
					print("//#message: Dados atualizados com sucesso!". PHP_EOL);
					die();
				}
				if ( $_GET['action'] == 'set-pass') {
					//$this->update( 'pass', $_POST['pass']);
				}
			}
		}
		private function setContacts() {
			require_once( SROOT ."engine/queries/users/list-contact.php");
			
			$Pager	= Pager(array(
				'sql'	=> 'users.list-contact',
				'ids'	=> array(
					array('uc.id_users', $_SESSION['login']['id'])
				),
				'order'	=> 'uc.channel, uc.type, uc.value'
			));
			$this->data['contacts']	= $Pager['rows'];
		}
	}
?>