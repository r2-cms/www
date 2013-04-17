<?php
	class MailSubscribers extends GT8 {
		function __construct() {
			global $GT8, $spath;
			
			$this->checkReadPrivileges('mail-subscribers/');
			$this->checkActionRequest();
		}
		public function loadRows( $id=0) {
			require_once( SROOT ."engine/functions/Pager.php");
			$id		= (integer)$id;
			
			$where	= '';
			if ( $id) {
				$where	= ' AND ms.id = '. $id;
			}
			$Pager	= Pager(array(
				'select'	=> '
					ms.id, ms.id_users,
					ms.name,
					ms.mail,
					ms.genre,
					ms.birth,
					DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(ms.birth)), "%Y")+0 AS age,
					
					ms.enabled,
					
					ms.creation, ms.modification,
					
					IF (ms.genre="", "selected=\"selected\"", "") AS `genre-`,
					IF (ms.genre="f", "selected=\"selected\"", "") AS `genre-f`,
					IF (ms.genre="m", "selected=\"selected\"", "") AS `genre-m`
				',
				'from'	=> '
					gt8_mail_subscribers ms
				',
				'where'	=> '
					'. $where .'
				',
				'order'	=> '
					ms.id DESC
				',
				'foundRows'	=> 50
			));
			$this->Pager	= $Pager['rows'];
			if ( !isset($this->Pager[0]) || !$this->Pager[0] || !isset($this->Pager[0]['id'])) {
				//parent::on404();
			}
			
			$this->Pager	= $Pager['rows'];
			$this->data['rows']	= $this->Pager;
			
		}
		private function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				
				$format	= isset($_GET['format'])? $_GET['format']: '';
				
				if ( $_GET['action'] === 'update') {
					
					$this->checkWritePrivileges('mail-subscribers/');
					
					$id		= (integer)$_GET['id'];
					$field	= RegExp( $_GET['field'], '[a-zA-Z0-9\ \-\_\.]+');
					$value	= mysql_real_escape_string($_GET['value']);
					
					if ( !$id) {
						$this->throwError('Id ausente.');
					}
					
					$this->loadRows($id);
					if ( !isset($this->data['rows'][0])) {
						$this->throwError('ID inválido!<br />E-mail não encontrado. O e-mail pode ter sido removido.');
					}
					$rows	= $this->data['rows'][0];
					
					//forbidden fields
					if ( in_array($field, array('mail', 'id'))) {
						$this->throwError('Campo inválido');
					}
					
					mysql_query("
						UPDATE
							gt8_mail_subscribers
						SET
							`$field`	= '$value'
						WHERE
							id = $id
						LIMIT
							1
					") or die(mysql_error());
					
					if ( $format === 'JSON') {
						print('//#message: Campo atualizado com sucesso.'.PHP_EOL);
						die();
					}
				}
				if ( $_GET['action'] === 'insert') {
					
					$mail	= RegExp( $_GET['mail'], '[a-zA-Z0-9\ \-\_\.\@]+');
					$name	= mysql_real_escape_string($_GET['name']);
					$birth	= RegExp( $_GET['birth'], '[0-9]{2,2}.[0-9]{2,2}.[0-9]{4,4}');
					$genre	= RegExp( $_GET['genre'], 'F|M');
					
					mysql_query("
						INSERT INTO
							gt8_mail_subscribers( mail, name, birth, genre, enabled, creation)
						VALUES(
							'$mail',
							'$name',
							'$birth',
							'$genre',
							1,
							NOW()
						)
					") or ($this->throwError('Erro no banco de dados'. ($_SESSION['login']['level']>5? ':<br/>'. mysql_error(): '')));
						
					if ( $format === 'JSON') {
						print('//#insert id: '. mysql_insert_id() .''. PHP_EOL);
						print('//#message: Parâmetro criado com sucesso.'.PHP_EOL);
						die();
					}
				}
			}
		}
		public function getServerJSVars() {
			global $GT8;
			//$this->jsVars[]	= array('contactsAPath', CROOT.$GT8['admin']['root'].$GT8['admin']['contacts']['root']);
			
			return parent::getServerJSVars();
		}
	}
?>