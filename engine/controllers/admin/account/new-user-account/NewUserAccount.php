<?php
	require_once( SROOT .'engine/controllers/admin/account/Editor.php');
	class NewUserAccount extends AdminEditor {
		public function __construct() {
			$this->checkActionRequest();
			parent::__construct();
		}
		private function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				switch($_GET['action']) {
					case 'check-login': {
						$login	= RegExp($_GET['login'], '[A-Za-z0-9_\-\.\:\@]+');
						if ( $login != $_GET['login']) {
							die('//#error: caractere inválido!');
						}
						$hlogin	= md5(strtolower($login));
						$row	= mysql_fetch_array(mysql_query("SELECT id FROM gt8_users WHERE hlogin = '$hlogin' LIMIT 1"));
						
						if ( $row ) {
							print('//#error: login já existente!');
						} else {
							print('//#affected: 1');
						}
						die();
					}
					case 'new': {
						//only for compatibility
					}
					case 'new-user-account': {
						require_once( SROOT .'engine/queries/users/InsertUser.php');
						$_GET['format']	= 'JSON';
						$_GET['enabled']	= 1;
						$_GET['approval_level_required']	= 7;//manager
						$_GET['createImg']	= true;
						InsertUser($_GET);
						die();
					}
				}
			}
		}
	}
?>