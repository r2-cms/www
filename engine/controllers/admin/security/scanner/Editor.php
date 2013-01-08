<?php
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/classes/Editor.php");
	
	class AdminEditor extends Editor {
		public $name	= 'banners-config';
		public $tableType	= null;
		
		function __construct() {
			global $GT8;
			
			//obtém o nome do diretório que vem logo após o endereço passado no argumento
			$page	= mysql_real_escape_string(join('', $this->getSPath('security/scanner/')));
			
			//consulte a existência da página no banco
			$Pager	= Pager(array(
				'sql'		=> 'banners-config.list',
				'required'	=> array(
					array('bc.page', $page)
				)
			));
			$this->Pager	= $Pager['rows'];
			if ( !isset($this->Pager[0]) || !$this->Pager[0] || !isset($this->Pager[0]['id'])) {
				$this->on404();
			}
			
			$this->Pager	= $Pager['rows'][0];
			$this->data	= $this->Pager;
			$this->id	= $this->Pager['id'];
			
			$this->checkActionRequest();
			$this->checkReadPrivileges();
			
			parent::Editor();
		}
		public function on404() {
			if ( !$this->id ) {
				parent::on404();
			}
		}
		private function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				switch($_GET['action']) {
					case 'sample': {
						require_once( SROOT .'engine/queries/module-name/ClassUpdateOrFunction.php');
						new ClasseUpdateSample(array(
							'page'	=> 'home',
							'field'	=> 'source',
							'value'	=> $_GET['ids'],
							'format'=> 'JSON'
						));
						die();
					}
				}
			}
		}
		public function getServerJSVars() {
			//envie variáveis para serem acessadas no navegador via js. Ex:
			//$this->jsVars[]	= array( varName, value);
			
			return parent::getServerJSVars();
		}
		public function remoteLogin( $host, $user, $pass) {
			/**
			 * Realiza login remoto no servidor para poder iniciar as atividades remotas
			*/
		}
		protected function getRemoteFiles( $dir) {
			/**
			 * Retorna um array bidimensional no formato:
			 * [
			 *		[file, ext, creation, modification, hash]
			 * ]
			 *
			*/
		}
	}
?>