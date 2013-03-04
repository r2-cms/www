<?php
	if ( !defined('SROOT') ) {
		require_once('../../connect.php');
	}
	
	require_once( SROOT ."engine/controllers/account/Account.php");
	
	class Data extends Account {
		public function __construct() {
			global $GT8;
			
			$this->checkActionRequest();
			$this->setFields();
		}
		private function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				$idLogin	= (integer)$_SESSION['login']['id'];
				if ( $_GET['action'] == 'save-account-data') {
					
					$name		= mysql_real_escape_string($_GET['name']);
					$genre		= RegExp($_GET['genre'], 'F|M');
					$natureza	= RegExp($_GET['natureza'], 'F|J');
					$cpfcnpj	= RegExp($_GET['cpfcnpj'], '[0-9\/\.\-]+');
					$document	= RegExp($_GET['document'], '[a-z-A-Z0-9\.\-\ ]+');
					preg_match('#([0-9]{2})/([0-9]{2})/([0-9]{4})#', $_GET['birth'], $birth);
					$birth		= $birth[3].'-'.$birth[2].'-'.$birth[1];
					mysql_query("
						UPDATE
							gt8_users
						SET
							name		= '$name',
							genre		= '$genre',
							birth		= '$birth',
							natureza	= '$natureza',
							cpfcnpj		= '$cpfcnpj',
							document	= '$document'
						WHERE
							1 = 1
							AND id = $idLogin
					". PHP_EOL) or die('//#error: Erro interno. Por favor, tente mais tarde'. PHP_EOL);
					print("//#message: Dados atualizados com sucesso!". PHP_EOL);
					die();
				}
				if ( $_GET['action'] == 'set-pass') {
					//$this->update( 'pass', $_POST['pass']);
				}
			}
		}
		private function setFields() {
			
			require_once(SROOT .'engine/functions/Pager.php');
			$Pager	= Pager(array(
				'sql'	=> 'users.list',
				'addSelect'	=> '
					, l.pt AS level_pt
				',
				'addFrom'	=> '
					INNER JOIN gt8_levels l ON l.id = u.level
				',
				'ids'	=> array(
					array('u.id', $_SESSION['login']['id'])
				)
			));
			$this->data	= $Pager['rows'][0];
			
			$this->data['natureza-f-selected']	= $this->data['natureza'] == 'F'? 'selected="selected"': '';
			$this->data['natureza-j-selected']	= $this->data['natureza'] == 'J'? 'selected="selected"': '';
			
			$this->data['genre-f-selected']		= $this->data['genre'] == 'F'? 'selected="selected"': '';
			$this->data['genre-m-selected']		= $this->data['genre'] == 'M'? 'selected="selected"': '';
		}
	}
?>