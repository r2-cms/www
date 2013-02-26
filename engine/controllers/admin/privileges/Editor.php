<?php
	if ( !defined('CROOT')) {
		//require_once( "../../../../engine/connect.php");
		die('Undefined GT8: admin.privileges.Home');
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/functions/CheckPrivileges.php");
	require_once( SROOT ."engine/classes/Editor.php");
	
	class AdminEditor extends Editor {
		public $name		= 'privileges';
		public $row			= array();
		public $tableType	= null;
		
		function __construct() {
			global $GT8;
			
			$spath	= $this->getSPath($GT8['admin']['root'] .'privileges/');
			$userLogin	= RegExp($spath[0], '[a-zA-Z0-9_\-\.\,\&\@]+');
			
			parent::Editor();
			
			//security
			$User	= mysql_fetch_assoc(mysql_query("SELECT level+0 AS ilevel, login, id, enabled, name FROM gt8_users u WHERE u.login = '$userLogin'"));
			$this->data	= $User;
			
			if ( !isset($User['id'])) {
				$this->getUrlHistory();
			}
			$this->id	= $User['id'];
			$level	= $User['ilevel'];
			
			if ( $level > $_SESSION['login']['level']) {
				$this->redirect('forbidden');
			}
			
			$Pager	= Pager( array(
				'sql'		=> 'privileges.list',
				'replace'	=> array(
					array('= u.id', '= u.id AND u.login = "'. $userLogin .'" AND u.level+0 <= '. $_SESSION['login']['level'])
				)
			));
			//$this->id		= $Pager['rows'][0]['id_users'];
			$this->Pager	= $Pager;
			
			$this->Pager['rows'][0]['explorer_img']	= 'users/'. $Pager['rows'][0]['login'] . '/?preview';
			
			
			$this->processActions();
			CheckPrivileges( '', '', 'users/privileges/', 1);
		}
		public function on404() {
			
		}
		private function processActions() {
			
			if ( isset($_GET['action'])) {
				
				switch( $_GET['action']) {
					case 'update.privileges': {
						require_once( SROOT .'engine/queries/users/updatePrivileges.php');
						$_GET['idUser']		= $this->id;
						$_GET['privilege']	= $_GET['value'];
						updatePrivileges($_GET);
						break;
					}
				}
				die();
			}
		}
		public function printPrivilegeFields($template) {
			global $GT8;
			
			$html	= '';
			$rows	= $this->Pager['rows'];
			
			for ( $i=0, $len=count($rows); $i<$len; $i++) {
				$row	= $rows[$i];
				
				$html	.= str_replace(
					array(
						'@id@',
						'@category@',
						'@url@',
						'@page@',
						'@field@',
						'@privilege@',
						'@iprivilege@',
						'@privilege-label@',
						'@privilege-combo-options@'
					),
					array(
						$row['id'],
						$row['category'],
						CROOT . $GT8['admin']['root'] . $row['url'],
						$row['url'],
						utf8_encode($row['field']),
						$row['privilege'],
						$row['iprivilege'],
						($row['iprivilege']==0? 'sem acesso':($row['iprivilege']==1? 'somente leitura': ($row['iprivilege']==2? 'leitura e escrita': 'total'))),
						'
								<option value="-" '. ($row['privilege']=='-'? 'selected="selected" ': '') .' >sem acesso</option>
								<option value="r" '. ($row['privilege']=='r'? 'selected="selected" ': '') .' >somente leitura</option>
								<option value="w" '. ($row['privilege']=='w'? 'selected="selected" ': '') .' >leitura e escrita</option>
								<option value="x" '. ($row['privilege']=='x'? 'selected="selected" ': '') .' >total</option>
						'
					),
					$template
				);
			}
			print($html);
			
		}
		public function printManagerModal($options=array()) {
			parent::printManagerModal(array(
				'html'		=> ''
			));
		}
	}
?>