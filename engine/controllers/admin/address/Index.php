<?php
	if ( !defined('CROOT')) {
		require_once('../../engine/connect.php');
		//die('Undefined GT8: a13i2->a114e00->Home');
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/functions/CheckPrivileges.php");
	
	CheckPrivileges( null, null, 'address/', 1);
	require_once( SROOT .'engine/classes/CardLister.php');
	
	class Index extends CardLister {
		public $name		= 'address';
		public $root		= '';
		public $estados		= '';
		public $cidades		= '';
		public $orderFilter	= array(
			array("id-desc", "natural", 'a.id DESC')
		);
		
		function __construct() {
			global $GT8;
			
			$__tmp	= explode('/', $_GET['path']);
			$spath	= array();
			$dirFound	= 0;
			$dir	= explode('/', $GT8['admin']['root'] . 'enderecos');
			for ( $i=0; $i<count($__tmp); $i++) {
				$crr = $__tmp[$i];
				if ( $crr && $dirFound>= count($dir)) {
					$spath[]	= $crr;
				}
				if ( $crr == $dir[$dirFound]) {
					$dirFound++;
				}
			}
			$this->checkActionRequest();
			/***************************************************************************
			*                                 ESTADOS                                  *
			***************************************************************************/
			$this->estados	= isset($_GET['uf'])? explode(',', $_GET['uf']): array();
			for( $i=0; $i<count($this->estados); $i++) {
				$this->estados[$i]	= RegExp($this->estados[$i], '[A-Z]{2}');
			}
			$this->estados	= array_unique($this->estados);
			if ( count($this->estados) && $this->estados[0]) {
				$this->options['equal']	= array(
					array('a.stt', join(',', $this->estados))
				);
			}
			/***************************************************************************
			*                                 CIDADES                                  *
			***************************************************************************/
			$this->cidades	= isset($_GET['cidade'])? mysql_real_escape_string($_GET['cidade']): '';
			if ( $this->cidades ) {
				$this->options['search']	= array(
					array('a.city', $this->cidades)
				);
			}
			/***************************************************************************
			*                                 CARD LISTER                              *
			***************************************************************************/
			$this->keywords	= isset($_GET["q"])? str_replace(explode(" ",'" \' % + ? \\ / ^ ~ ` ´ ! $ * '), " ", $_GET["q"]): null;
			if ( $this->keywords) {
				$this->options['search']	= isset($this->options['search'])? $this->options['search']: array();
				$this->options['search'][]	= array('a.street, a.district, a.city', utf8_decode($this->keywords));
			}
			if ( isset($_GET['zip']) && $_GET['zip']) {
				$this->options['searchR']	= isset($this->options['searchR'])? $this->options['searchR']: array();
				$this->options['searchR'][]	= array('a.zip', substr(RegExp($_GET['zip'], '[0-9\-]+'), 0, 9));
			}
			parent::CardLister($options);
			$this->buildHTML();
		}
		public function on404() {
			print("<pre>". print_r(22227, 1) ."</pre>". PHP_EOL);
			die();
		}
		private function buildHTML() {
			//$this->addDirLocation('Endereços', CROOT . $GT8['admin']['root'] .'address/');
			$this->addToolbarItem('Adicionar endereço', 'add-new-address', CROOT.$GT8['admin']['root'].'address/novo/', CROOT.'imgs/gt8/toolbar/file-add-small.png');
			$this->addToolbarItemG(array(
				array('Excluir endereço(s)', 'delete-address', 'delete/', CROOT.'imgs/gt8/toolbar/file-del-small.png', 'return Pager.toggleDeleteButtons()')
			), 'group-toggle');
			$this->addToolbarItemG(array(
				array('Exibir marcas', 'brands', '', '', '', 'Marcas'),
				array('Exibir filtros', 'filters', '', CROOT .'imgs/gt8/toolbar/filter-small.png'),
				array('Exibir filtros de estados', 'estados', '', '', '', 'UF')
			));
			
			$this->addSideBarItem('Estados', 'estados', (CROOT.'imgs/gt8/favorite-small.png'), $this->getBREstadosFilter());
			$this->addSideBarItem('Marcas', 'brands', (CROOT.'imgs/gt8/favorite-small.png'), '');
			$this->addSideBarItem( 'Filtros', 'filters', (CROOT.'imgs/gt8/filter-small.png'), '
				<label title="Busca por CEP" ><span><input type="text" value="'. (isset($_GET['zip'])? (htmlentities(utf8_decode($_GET['zip']))): '') .'" name="zip" class="gt8-update input-rounded-shadowed" onkeyup="Pager.search(this, event)" /><small>Busca por cep</small></span></label>
				<label title="Busca por estado" ><span><input type="text" value="'. (isset($_GET['uf'])? (htmlentities(utf8_decode($_GET['uf']))): '') .'" name="uf" class="gt8-update input-rounded-shadowed" onkeyup="Pager.search(this, event)" /><small>Busca por estados</small></span></label>
				<label title="Busca por cidade" ><span><input type="text" value="'. (isset($_GET['city'])? (htmlentities(utf8_decode($_GET['city']))): '') .'" name="city" class="gt8-update input-rounded-shadowed" onkeyup="Pager.search(this, event)" /><small>Busca por cidades</small></span></label>
				<label title="Procurar por ruas" ><span><input type="text" value="'. (isset($_GET['street'])? (htmlentities(utf8_decode($_GET['street']))): '') .'" name="street" class="gt8-update input-rounded-shadowed" onkeyup="Pager.search(this, event)" /><small>Procurar por ruas</small></span></label>
			');
			$this->jsVars[]	= array('id', 0);
			$this->jsVars[]	= array('u', addslashes($_SESSION['login']['name']));
		}
		private function checkActionRequest() {
			if ( $_GET['opt'] == 'update') {
				require_once( SROOT .'queries/address/UpdateAddress.php');
				new UpdateAddress(array(
					"id"		=> $_GET["id"],
					"field"		=> $_GET["field"],
					"value"		=> $_GET["value"],
					'format'	=> 'JSON'
				));
				die();
			} else if ( $_GET['opt'] == 'delete') {
				require_once( SROOT .'queries/address/deleteAddress.php');
				deleteAddress($_GET);
				die();
			} else if ( $_GET['opt'] == 'get' && $_GET['sql'] == 'zip') {
				require_once( SROOT .'queries/address/getAddressByZip.php');
				print(Zip::getZip($_GET['zip'], $_GET['format']));
				die();
			} else if ( $_GET['opt'] == 'new') {
				require_once( SROOT .'queries/address/addNewAddress.php');
				addNewAddress($_GET);
				die();
			} else if ( $spath[0] == 'novo' || $spath[0] == 'new') {
				require_once( 'new-address.php');
				die();
			} else if ( $spath[0] ) {
				$_GET['id']	= $spath[0];
				require_once( 'editor/index.php');
				die();
			}
		}
		public function getBREstadosFilter() {
			
			$arr	= array(
				array("AC", 'Acre'),
				array("AL", 'Alagoas'),
				array("AM", 'Amazonas'),
				array("AP", 'Amapá'),
				array("BA", 'Bahia'),
				array("CE", 'Ceará'),
				array("DF", 'Distrito Federal'),
				array("ES", 'Espírito Santo'),
				array("GO", 'Goiás'),
				array("MA", 'Maranhão'),
				array("MG", 'Minas Gerais'),
				array("MS", 'Mato Grosso do Sul'),
				array("MT", 'Mato Grosso'),
				array("PA", 'Pará'),
				array("PB", 'Paraíba'),
				array("PE", 'Pernambuco'),
				array("PI", 'Piauí'),
				array("PR", 'Paraná'),
				array("RJ", 'Rio de Janeiro'),
				array("RN", 'Rio Grande do Norte'),
				array("RO", 'Rondônia'),
				array("RR", 'Roraima'),
				array("RS", 'Rio Grande do Sul'),
				array("SC", 'Santa Catarina'),
				array("SE", 'Sergipe'),
				array("SP", 'São Paulo'),
				array("TO", 'Tocantins')
			);
			$html	= '
				<ul class="folder checkbox" >
			';
			$q	= join(',', $this->estados);
			for ( $i=0; $i<count($arr); $i++) {
				$found	= strpos($q, $arr[$i][0]) !== false;
				if ( $found ) {
					$crr	= str_replace($arr[$i][0], '', $q);
					$class	= 'class="checked" ';
				} else {
					$crr	= $q .','. $arr[$i][0];
					$class	= '';
				}
				if ( substr($crr, 0, 1) == ',') {
					$crr	= substr($crr, 1);
				}
				if ( substr($crr, -1) == ',') {
					$crr	= substr($crr, 0, -1);
				}
				$html	.= '<li><a id="uf-'. $crr.'" href="?uf='. $crr .'" '.$class.'>'. $arr[$i][1] .'</a></li>';
			}
			$html	.= '
				</ul>
			';
			return $html;
		}
		public function printCards($template='') {
			$this->options['sql']		= 'address.list';
			$this->options['grid']	= array(
					'id'	=> '<a href="$value$/" >#value#</a>'. PHP_EOL .'								'
			);
			//$this->options['debug']=1;
			parent::printCards($template);
		}
	}
?>