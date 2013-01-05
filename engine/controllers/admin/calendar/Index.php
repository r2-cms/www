<?php
	if ( !defined('CROOT')) {
		require_once('../../engine/connect.php');
		//die('Undefined GT8: a13i2->a114e00->Home');
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/functions/CheckPrivileges.php");
	
	//CheckPrivileges( null, null, 'calendar/', 1);
	require_once( SROOT .'engine/classes/CardLister.php');
	
	class Index extends CardLister {
		public $name		= 'calendar';
		public $root		= '';
		public $viewMode	= 'month';
		public $orderFilter	= array(
			array("id-desc", "natural", 'j.id DESC')
		);
		public $weekNames	= array( 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo');
		
		function __construct() {
			global $GT8;
			
			$this->checkActionRequest();
			/***************************************************************************
			*                                 CARD LISTER                              *
			***************************************************************************/
			$this->keywords	= isset($_GET["q"])? str_replace(explode(" ",'" \' % + ? \\ / ^ ~ ` ´ ! $ * '), " ", $_GET["q"]): null;
			if ( $this->keywords) {
				//$this->options['search']	= isset($this->options['search'])? $this->options['search']: array();
				//$this->options['search'][]	= array('a.street, a.district, a.city', utf8_decode($this->keywords));
			}
			parent::CardLister();
			$this->createAdminStructure();
		}
		private function createAdminStructure() {
			global $GT8;
			
			$this->addToolbarItem('Adicionar endereço', 'add-new-event', CROOT.$GT8['admin']['root'].'address/novo/', CROOT.'imgs/gt8/toolbar/file-add-small.png');
			$this->addToolbarItemG(array(
				array('Excluir endereço(s)', 'delete-event', 'delete/', CROOT.'imgs/gt8/toolbar/file-del-small.png', 'return Pager.toggleDeleteButtons()')
			), 'group-toggle');
			$this->addToolbarItemG(array(
				array('Exibir marcas', 'brands', '', '', '', 'Marcas'),
				array('Exibir filtros', 'filters', '', CROOT .'imgs/gt8/toolbar/filter-small.png'),
				array('Exibir filtros de estados', 'estados', '', '', '', 'UF')
			));
			$this->addSideBarItem('Marcas', 'brands', (CROOT.'imgs/gt8/favorite-small.png'), '');
			$this->addSideBarItem( 'Filtros', 'filters', (CROOT.'imgs/gt8/filter-small.png'), '
				<label title="Procurar por palavras chaves" ><span><input type="text" value="'. (isset($_GET['q'])? (htmlentities(utf8_decode($_GET['q']))): '') .'" name="q" class="gt8-update input-rounded-shadowed" onkeyup="Pager.searchIn(this, event); Pager.search(this, event)" /><small>busca por palavras</small></span></label>
				<label title="Busca por CEP" ><span><input type="text" value="'. (isset($_GET['zip'])? (htmlentities(utf8_decode($_GET['zip']))): '') .'" name="zip" class="gt8-update input-rounded-shadowed" onkeyup="Pager.search(this, event)" /><small>Busca por cep</small></span></label>
			');
		}
		public function getServerJSVars() {
			$this->jsVars[]	= array('u', addslashes($_SESSION['login']['name']));
			return parent::getServerJSVars();
		}
		private function checkActionRequest() {
			if ( $_GET['opt'] == 'update') {
				die();
			}
		}
		public function getHTMLCalendar() {
			$this->options['sql']		= 'calendar.list';
			$template	= '';
			
			if ( $this->viewMode == 'month') {
				$template	= $this->getHTMLMonth();
			}
			
			return $template;
		}
		public function getHTMLMonth() {
			$year	= date('Y');
			$month	= date('n');
			$day	= date('j');
			$startDay	= (integer)date("w", mktime(0,0,0,$month, 1, $year));
			$lastDay	= (integer)date("j", mktime(0,0,0,$month+1, 0, $year));
			
			$templateDay	= "
							<div class='col' >
								<div class='weekname' >@weekname@</div>
								<div class='daynum' >@daynum@</div>
							</div>
			";
			$template	= "
				<div class='Calendar' >
					<div class='view-month' >
			";
			$crrDay	= '';
			$countCells	= 0;
			for ( $i=1; $i<7; $i++) {
				$cols	= '';
				for ( $j=1; $j<8; $j++) {
					$classes	= '';
					
					$col	= str_replace(
						array('@daynum@'),
						array($crrDay),
						$templateDay
					);
					if ( $i<2) {
						$col	= str_replace("@weekname@", $this->weekNames[$j-1], $col);
					} else {
						$col	= str_replace("<div class='weekname' >@weekname@</div>", '', $col);
					}
					if ( $day == $crrDay) {
						$classes	.= ' today';
					}
					
					$col	= str_replace("<div class='col' >", "<div class='col $classes' >", $col);
					
					$cols	.= $col;
					if ( $countCells > 27 ) {
						if ( $crrDay=='' || $crrDay > $lastDay-1) {
							$crrDay	= '';
						} else {
							$crrDay++;
						}
					} else if ( $startDay-3 < $countCells) {
						$crrDay	= $crrDay==''? 1: $crrDay+1;
					}
					$countCells++;
				}
				$template	.= "
						<div class='row' >
							$cols
						</div>
				";
			}
			$template	.= "
					</div>
				</div>
			";
			
			
			return $template;
		}
	}
?>