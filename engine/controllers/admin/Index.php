<?php
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/functions/CheckPrivileges.php");
	require_once( SROOT ."engine/classes/GT8.php");
	
	class Index extends GT8 {
		
		function __construct() {
			if ( $_SESSION['login']['level'] < 4) {
				header('location: ../');
				die();
			}
			
			$this->checkActionRequest();
			
			$this->createData();
		}
		private function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				switch($_GET['action']) {
					case 'save-admin-icon-position': {
						require_once(SROOT.'engine/queries/modules/SaveAdminIconPosition.php');
						
						$s	= RegExp($_GET['value'], '[a-zA-Z\_0-9\-\.\|\,\/]+');
						if ( $s) {
							$s	= explode('|', $s);
							for( $i=0; $i<count($s); $i++) {
								$crr	= explode(',', $s[$i]);
								new SaveAdminIconPosition(array(
									'module'	=> $crr[0],
									'page_index'=> $crr[1],
									'card_index'=> $crr[2],
									'format'	=> 'JSON'
								));
							}
						}
						break;
					}
					case 'save-admin-page-index-position': {
						$this->saveParam('admin-page-index', $_GET['value'], 'admin');
						break;
					}
				}
				die();
			}
		}
		public function getServerJSVars() {
			$this->jsVars[]	= array('adminPageIndex', (integer)$this->getParam('admin-page-index', 'admin'), true);
			
			return parent::getServerJSVars();
		}
		public function getCards($template) {
			require_once(SROOT.'engine/functions/Pager.php');
			
			$Base	= Pager(array(
				'sql'	=> 'modules.list',
				'where'	=> 'AND m.id_users = 0'
			));
			$Base	= $Base['rows'];
			$Modules	= Pager(array(
				'sql'	=> 'modules.list',
				'addSelect'	=> ' SUBSTRING_INDEX(m.module, "/", 1) AS module2, m.custom',
				'order'	=> 'm.page_index, m.card_index'
			));
			
			$Modules	= $Modules['rows'];
			for($i=0; $i<count($Base); $i++) {
				$crr	= $Base[$i];
				
				$found	= false;
				for ( $j=0; $j<count($Modules); $j++) {
					$crrJ	= $Modules[$j];
					
					if ( $crr['module'] === $crrJ['module']) {
						$found	= true;
						break;
					}
				}
				if ( !$found) {
					mysql_query("
						INSERT INTO
							gt8_modules (
								id_users,
								module,
								page_index,
								card_index,
								views,
								shortcut,
								sumary,
								description,
								img
							)
							SELECT
								{$_SESSION['login']['id']},
								'{$crr['module']}',
								0,
								0,
								1,
								'{$crr['shortcut']}',
								'". (mysql_real_escape_string($crr['sumary'])) ."',
								'". (mysql_real_escape_string($crr['description'])) ."',
								'". (mysql_real_escape_string($crr['img'])) ."'
							FROM
								gt8_modules
							WHERE
								id_users	= {$_SESSION['login']['id']} AND
								module		= '{$crr['module']}'
							HAVING
								COUNT(*) = 0
					");
					$Modules[]	= $crr;
				}
			}
			$Privileges	= Pager(array(
				'sql'	=> 'privileges.list',
				'ids'	=> array(
					array('f.id_users', $_SESSION['login']['id'])
				)
			));
			$Privileges	= $Privileges['rows'];
			for( $i=0; $i<count($Privileges); $i++) {
				$crr	= $Privileges[$i];
				for ($j=0; $j<count($Modules); $j++) {
					if ( $Modules[$j]['module'] === substr($crr['url'], 0, -1) ) {
						$Modules[$j]['iprivilege']	= $crr['iprivilege'];
						break;
					}
				}
			}
			$html	= '
			';
			$pindex	= -1;
			for ( $i=0; $i<count($Modules); $i++) {
				$crr	= $Modules[$i];
				$crr['url']	= $crr['module'].'/';
				if ( isset($crr['custom']) && $crr['custom'] == 1) {
					$crr['iprivilege']	= 1;
					$crr['url']	= substr($crr['url'], 0, -1);
				}
				
				if ( $crr['page_index'] != $pindex) {
					if ( $pindex > -1) {
					$html	.= '
						<div class="card invisible" ><div class="spacing bg-linear-gray" ></div></div>
						<div class="card invisible" ><div class="spacing bg-linear-gray" ></div></div>
						<div class="card invisible" ><div class="spacing bg-linear-gray" ></div></div>
						<div class="card invisible" ><div class="spacing bg-linear-gray" ></div></div>
					</div>
				</div>
					';
					}
					$html	.= '
				<div class="page-container" >
					<div class="home-cards-container row" >
					';
				}
				if ( $crr['iprivilege'] > 0 ) {
					$crr['CROOT']	= CROOT;
					$crr	= array_merge($crr, $_SESSION['login']);
					//$crr['sumary']	= utf8_decode($crr['sumary']);
					if ( empty($crr['shortcut'])) {
						for ( $j=0; $j<count($Base); $j++) {
							
							if ( $Base[$j]['module'] === $crr['module']) {
								$crr['shortcut']	= $Base[$j]['shortcut'];
								break;
							}
						}
					}
					$html	.= $this->getMatchPairs($template, $crr);
				}
				$pindex	= $crr['page_index'];
			}
			$html	.= '
				</div>
			</div>
			';
			return $html;
		}
		private function createData() {
			$this->data['h1']	= '';
			$this->data['title']	= 'Administrativo';
		}
	}
?>