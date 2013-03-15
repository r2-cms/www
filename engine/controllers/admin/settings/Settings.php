<?php
	class Settings extends GT8 {
		function __construct() {
			global $GT8, $spath;
			
			$this->checkReadPrivileges('settings/');
			
			$this->checkActionRequest();
		}
		public function setParams( $id=0) {
			require_once( SROOT ."engine/functions/Pager.php");
			$id		= (integer)$id;
			
			$where	= '';
			if ( $id) {
				$where	= ' AND p.id = '. $id;
			}
			$Pager	= Pager(array(
				'select'	=> '
					p.id, p.id_users,
					p.name, p.value,
					p.category,
					p.read_privilege,
					p.write_privilege,
					
					l.pt AS pread,
					l2.pt AS pwrite
				',
				'from'	=> '
					gt8_param p
					LEFT JOIN gt8_levels l	ON l.id = p.read_privilege
					LEFT JOIN gt8_levels l2	ON l2.id = p.write_privilege
				',
				'where'	=> '
					AND p.id_users = 0
					'. $where .'
				',
				'order'	=> '
					p.category, p.name
				',
				'foundRows'	=> 100
			));
			$this->Pager	= $Pager['rows'];
			if ( !isset($this->Pager[0]) || !$this->Pager[0] || !isset($this->Pager[0]['id'])) {
				parent::on404();
			}
			
			$this->Pager	= $Pager['rows'];
			$this->data['params']	= $this->Pager;
			
		}
		private function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				$this->checkWritePrivileges('settings/');
				
				$format	= $_GET['format'];
				$id		= (integer)$_GET['id'];
				$name	= mysql_real_escape_string($_GET['name']);
				$value	= mysql_real_escape_string($_GET['value']);
				
				//se houver id, é exclusão ou inserção. Verifique se lhe é permitido isso
				if ( $id) {
					$this->setParams($id);
					if ( !isset($this->data['params'][0])) {
						if ( $format==='JSON') {
							die('//#error: Parâmetro não encontrado.'. PHP_EOL);
						}
						$this->redirect('forbidden');
					}
					$params	= $this->data['params'][0];
					$params['write_privilege']	= (integer)$params['write_privilege'];
					
					if ( $_SESSION['login']['level'] < $params['write_privilege'] ) {
						if ( $format==='JSON') {
							die('//#error: Você não tem permissão para alterar este parâmetro!'. PHP_EOL);
						}
						$this->redirect('forbidden');
					}
				}
				switch($_GET['action']) {
					case 'update-param': {
						$read	= (integer)$_GET['read'];
						$write	= (integer)$_GET['write'];
						
						if ( $id) {
							mysql_query("
								UPDATE
									gt8_param
								SET
									value	= '$value',
									read_privilege	= $read,
									write_privilege	= $write
								WHERE
									id = $id
							") or die(mysql_error());
							if ( $format === 'JSON') {
								print('//#affected: ('. mysql_affected_rows() .')'. PHP_EOL);
								print('//#message: Parâmetro alterado com sucesso.'.PHP_EOL);
							}
						} else {
							
							mysql_query("
								INSERT INTO
									gt8_param( name, value, id_users, read_privilege, write_privilege, category)
								VALUES(
									'$name',
									'$value',
									0,
									$read,
									$write,
									'system'
								)
							") or die('//#error: DB::Update error in admin.settings!'. PHP_EOL);
							
							if ( $format === 'JSON') {
								print('//#insert id: '. mysql_insert_id() .''. PHP_EOL);
								print('//#message: Parâmetro criado com sucesso.'.PHP_EOL);
							}
						}
						
						die();
					}
					case 'delete-param': {
						if ( !$id) {
							if ( $format==='JSON') {
								die('//#error: ID não encontrado!'. PHP_EOL);
							}
							die('//#error: ID não encontrado!');
						}
						mysql_query("
							DELETE FROM
								gt8_param
							where
								id = $id
						") or die(mysql_error());
						if ( $format === 'JSON') {
							print('//#affected: 1'. PHP_EOL);
							print('//#message: Parâmetro excluído com sucesso.'.PHP_EOL);
						}
						break;
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