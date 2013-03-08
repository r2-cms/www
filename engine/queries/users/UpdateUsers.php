<?php
	if ( !defined('CROOT')) {
		die('Undefined connection! (Q40->A114e00::U91a1e)');
	}
	require_once( SROOT .'engine/classes/Update.php');
	
	class UpdateUsers extends Update {
		public $name	= 'users';
		public $privilegeName	= 'users/';
		private $isContact	= false;
		
		public function UpdateUsers($options) {
			if ( substr($options['field'], 0, 8) == 'contact.' ) {
				$this->name	= 'users_contact';
				$this->logName	= 'users';
				$this->logId	= $options['id'];
				$this->logField	= $options['field'];
				$this->privilegeName	= 'users/';
				$options['field']	= substr($options['field'], 8);
				$this->isContact	= true;
				
				$this->id	= (integer)$_GET["idContact"];
				$options['id']	= $this->id;
				
				if ( !$this->id) {
					die('//#error: ID do contato é obrigatório!'. PHP_EOL);
				}
			}
			$this->Update( $options);
			
			//se a alteração for level, os campos de privilégios também devem ser alterados
			if ( $options['field'] == 'level') {
				$value	= $options['value'];
				
				require_once( SROOT .'engine/queries/users/updatePrivileges.php');
				$privilegeLeves	= array(
					'1234567890', //ignore this line. Levels: all registered customer support operacional            IT Manager Owner Developer Master
					'-----rwwxx',	//1		users/privileges/
					'-----rwxxx',	//2		users/
					'---wwwwxxx',	//3		crm/
					'---rwrwxxx',	//4		page-config/
					'---rwrwxxx',	//5		products/
					'---rwwwxxx',	//6		explorer/
					'---wwwwxxx',	//7		address/
					'-----rwxxx',	//8		users/contacts/
					'---rrrwxxx',	//9		analytics/
					'---rwwwxxx',	//10	redirects/
					'-----rwwxx',	//11	privileges/
					'-----rwxxx',	//12	calendar/
					'---wwwwxxx',	//13	orders/
					'---rwwwxxx'	//14	offers-config/
				);
				
				$level	= mysql_fetch_array(mysql_query("SELECT level+0 AS level FROM gt8_users WHERE id = ". $this->id));
				$level	= $level[0];
				for ( $i=1, $len=count($privilegeLeves); $i<$len; $i++) {
					updatePrivileges(array(
						'idPrivilege'	=> $i,
						'idUser'		=> $this->id,
						'privilege'		=> substr($privilegeLeves[$i], $level, 1),
						'format'		=> 'JSON'
					));
				}
			}
		}
		public function getValue( $field, $value) {
			if ( $field == 'stt') {
				$value	= strtoupper(RegExp($value, '[A-Za-z]{2}'));
			}
			return $value;
		}
		public function checkWritePrivileges( $url=null, $field='*', $format='OBJECT', $min=2) {
			$value	= $min;
			$url	= $this->privilegeName;
			if ( ($field=='enabled' || $field=='level') && $this->id == $_SESSION['login']['id']) {
				die('//#error: Você não pode alterar seus próprios privilégios!'. PHP_EOL);
			} else if ( $field == 'login' ) {
				die('//#error: Não é permitido alterar o login!'. PHP_EOL);
			} else if ( $field == 'hlogin' ) {
				die('//#error: Não é permitido alterar o login!'. PHP_EOL);
			} else if ( $field == 'cpfcnpj' ) {
				$value	= RegExp($_GET['value'], '[0-9\.\-\/]+');
				if ( strlen($value)!=14 && strlen($value)!=18) {
					die('//#error: Número de cadastro inválido!'. PHP_EOL);
				}
			} else if ( $field == 'level' || $field=='enabled') {
				//1) Tem o privilégio necessário?
				$prv	= CheckPrivileges('level', 'OBJECT', $url);
				if ( $prv == -404 || $prv < 2) {
					$prv	= CheckPrivileges('*', 'OBJECT', $url, 2);
					
					if ( $prv < 2) {
						die('//#error: Privilégio elevado requerido para este campo!'. PHP_EOL);
					}
				}
			} else {
				$id		= $this->isContact? (integer)$_GET['id']: $this->id;
				if ( $id != $_SESSION['login']['id'])  {
					parent::checkPrivileges( $url, $field, $format);
				}
			}
			
			//a tabela de contato tem ID diferente
			$id		= $this->isContact? (integer)$_GET['id']: $this->id;
			
			//2) Tem level igual ou superior ao que deseja alterar?
			$row	= mysql_fetch_assoc(mysql_query("
				SELECT id, level, level+0 AS ilevel FROM gt8_users WHERE id = $id
			"));
			
			if ( $_SESSION['login']['level'] < $row['ilevel']) {
				die('//#error: Privilégio elevado requerido para alterar o privilégio de outro usuário!'. PHP_EOL);
			}
			
		}
	}
?>