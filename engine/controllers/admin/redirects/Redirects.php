<?php
	if ( !defined('CROOT')) {
		require_once('../../engine/connect.php');
		//die('Undefined GT8: a13i2->a114e00->Home');
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/functions/CheckPrivileges.php");
	
	CheckPrivileges( null, null, 'address/', 1);
	require_once( SROOT .'engine/classes/CardLister.php');
	
	class Redirects extends CardLister {
		public $name		= 'redirects';
		public $root		= '';
		public $estados		= '';
		public $cidades		= '';
		public $orderFilter	= array(
			array("id-desc", "natural", 'r.id DESC')
		);
		
		function __construct() {
			global $GT8;
			$this->checkActionRequest();
			/***************************************************************************
			*                                 CARD LISTER                              *
			***************************************************************************/
			//$this->keywords	= isset($_GET["q"])? str_replace(explode(" ",'" \' % + ? \\ / ^ ~ ` ´ ! $ * '), " ", $_GET["q"]): null;
			//if ( $this->keywords) {
			//	$this->options['search']	= isset($this->options['search'])? $this->options['search']: array();
			//	$this->options['search'][]	= array('a.street, a.district, a.city', utf8_decode($this->keywords));
			//}
			//if ( isset($_GET['zip']) && $_GET['zip']) {
			//	$this->options['searchR']	= isset($this->options['searchR'])? $this->options['searchR']: array();
			//	$this->options['searchR'][]	= array('a.zip', substr(RegExp($_GET['zip'], '[0-9\-]+'), 0, 9));
			//}
			$gridState	= $this->getParam( 'grid-state-'.$this->name, $category='admin');
			if ( $gridState) {
				$this->options['gridState']	= $gridState;
			}
			parent::CardLister($options);
			$this->buildHTML();
		}
		private function buildHTML() {
			$this->jsVars[]	= array('id', 0);
			$this->jsVars[]	= array('u', utf8_encode(addslashes($_SESSION['login']['name'])));
			
			return;
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
		}
		private function checkActionRequest() {
			if ( $_GET['action'] == 'update') {
				require_once( SROOT .'engine/queries/redirects/UpdateUrl.php');
				new UpdateUrl(array(
					"id"		=> $_GET["id"],
					"field"		=> $_GET["field"],
					"value"		=> $_GET["value"],
					'format'	=> 'JSON'
				));
				die();
			} else if ( $_GET['action'] == 'delete') {
				die();
				require_once( SROOT .'queries/address/deleteAddress.php');
				deleteAddress($_GET);
				die();
			} else if ( $_GET['action'] == 'get' && $_GET['sql'] == 'zip') {
				die();
				require_once( SROOT .'queries/address/getAddressByZip.php');
				print(Zip::getZip($_GET['zip'], $_GET['format']));
				die();
			} else if ( $_GET['action'] == 'new') {
				die();
				require_once( SROOT .'queries/address/addNewAddress.php');
				addNewAddress($_GET);
				die();
			} else if ( $spath[0] == 'novo' || $spath[0] == 'new') {
				die();
				require_once( 'new-address.php');
				die();
			}
		}
		public function printCards($template='') {
			$this->options['sql']		= 'redirects.list';
			$this->options['format']	= 'GRID';
			$this->options['grid']	= array(
					'id'	=> '<a href="$value$/" >#value#</a>'. PHP_EOL .'								'
			);
			//$this->options['debug']=1;
			parent::printCards($template);
		}
	}
?>