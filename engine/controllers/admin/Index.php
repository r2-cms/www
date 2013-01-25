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
		private $countPrintCards	= 0;
		public function getServerJSVars() {
			$this->jsVars[]	= array('adminPageIndex', (integer)$this->getParam('admin-page-index', 'admin'), true);
			
			return parent::getServerJSVars();
		}
		public function printCards($template) {
			require_once(SROOT.'engine/functions/Pager.php');
			
			//format: shortcut, title, ??, iprivilege
			$cards		= array(
				'explorer'				=> array("e",		"Gerenciamento de arquivos", ''),
				'users'					=> array("u",		"Gerenciamento de usuários e privilégios de acesso", ''),
				'address'				=> array("ad",		"Endereços", ''),
				'calendar'				=> array("c",		"Calendário - eventos e tarefas agendadas", ''),
				'analytics'				=> array("a",		"Relatórios de acessos", ''),
				'privileges'			=> array("p",		"Privilégios de acesso", ''),
				'orders'				=> array("o",		"Pedidos de compra", ''),
				'offers-config/home'	=> array("och",		"Ofertas Especiais na Home", ''),
				'banners-config/home'	=> array("bch",		"Configuração de banners na Home", ''),
				'security/scanner'		=> array("ss",		"Scanner de arquivos", '')
			);
			
			$Pager	= Pager(array(
				'sql'	=> 'privileges.list',
				'ids'	=> array(
					array('f.id_users', $_SESSION['login']['id'])
				)
			));
			$Pager	= $Pager['rows'];
			for( $i=0; $i<count($Pager); $i++) {
				$crr	= $Pager[$i];
				if ( isset($cards[substr($crr['url'], 0, -1)])) {
					$cards[substr($crr['url'], 0, -1)][3]	= $crr['iprivilege'];
				}
			}
			$Pager	= Pager(array(
				'sql'	=> 'modules.list',
				'addSelect'	=> ' SUBSTRING_INDEX(m.module, "/", 1) AS module2',
				'order'	=> 'm.page_index, m.card_index'
			));
			$Pager	= $Pager['rows'];
			
			if ( count($Pager) < count($cards)) {
				if ( $this->countPrintCards > 0) {
					die('<pre class="gt8-debug-error" >Loop infinito!<img src="'.CROOT.'imgs/gt8/delete-small.png" width="22" height="22" ></pre>');
					return;
				}
				foreach($cards as $name=>$row) {
					mysql_query("
						INSERT INTO
							gt8_modules (
								id_users,
								module,
								page_index,
								card_index,
								views,
								sumary,
								description
							)
							SELECT
								{$_SESSION['login']['id']},
								'{$name}',
								0,
								0,
								1,
								'{$row[1]}',
								'{$row[2]}'
							FROM
								gt8_modules
							WHERE
								id_users	= {$_SESSION['login']['id']} AND
								module		= '{$name}'
							HAVING
								COUNT(*) = 0
					");
				}
				$this->countPrintCards++;
				
				return $this->printCards($template);
			}
			
			$html	= '
			';
			$pindex	= -1;
			
			for ( $i=0; $i<count($Pager); $i++) {
				$crr	= $Pager[$i];
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
				if ( isset($cards[$crr['module']]) && $cards[$crr['module']][3]>0) {
					$crr['CROOT']	= CROOT;
					$crr	= array_merge($crr, $_SESSION['login']);
					$crr['sumary']	= utf8_decode($cards[$crr['module']][1]);
					$crr['shortcut']	= $cards[$crr['module']][0];
					$html	.= $this->getMatchPairs($template, $crr);
				}
				$pindex	= $crr['page_index'];
			}
			$html	.= '
				</div>
			</div>
			';
			print($html);
		}
		private function createData() {
			$this->data['h1']	= '';
			$this->data['title']	= 'Administrativo';
		}
	}
	function PrintHomeCards() {
		$cards		= array(
			array("e",	"explorer/",	"Gerenciamento de arquivos"),
			array("u",	"users/",	"Gerenciamento de usuários e privilégios de acesso"),
			array("ad",	"address/",	"Endereços"),
			array("c",	"calendar/",	"Calendário - eventos e tarefas agendadas"),
			array("i",	"",		"Teste e"),
			array("j",	"",		"Teste e")
		);
		$html		= '';
		for ($i=0; $i<count($cards); $i++) {
			list( $id, $page, $title) = $cards[$i];
			
			$img	= $page .'imgs/large.png';
			if ( ($prv = CheckPrivileges("*", null, $page)) < 1) {
				$url	= $page;
			} else {
				$url	= $page;
			}
			$attr	= "";
			if ( $page ) {
				if ($prv == 1) {
					$attr	= 'lock';
				} else if ($prv < 1 ) {
					$attr	= 'forbidden';
				}
			} else {
				$attr	= 'invisible';
				$img	= 'imgs/blank.gif';
			}
			$html		.= "
				<div id='$id' class='card $attr' ><a href='$url' ><img src='$img' alt='$title' /></a><span>$title</span><span class='hidden module-name' >$page</span><div>&nbsp;</div></div>
			";
		}
		print($html);
	}
?>