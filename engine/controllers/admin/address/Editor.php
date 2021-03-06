<?php
	if ( !defined('CROOT')) {
		//require_once( "../../../../engine/connect.php");
		die('Undefined GT8: a1i32->a114e00->e1i1o4->Home');
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/functions/CheckPrivileges.php");
	
	CheckPrivileges( null, null,'address/', 1);
	require_once( SROOT .'engine/classes/Editor.php');
	
	class AdminEditor extends Editor {
		public $id	= 0;
		public $name	= 'address';
		public $row	= array();
		
		public function __construct() {
			global $GT8, $paths;
			
			$spath	= $paths;
			if ( empty($spath[count($spath)-1]) ) {
				array_pop($spath);
			}
			if ( $spath[count($spath)-1] == 'novo') {
				require_once(SROOT .'engine/controllers/admin/address/NewAddress.php');
				$new	= new NewAddress();
				GT8::printView(
					SROOT .'engine/views/admin/address/new-address.inc',
					$new->data,
					null,
					$new
				);
				die();
			}
			$zip	= $this->getSPath('address/');
			if ( !$zip) {
				$this->redirect('404');
			}
			
			$Pager	= Pager( array(
				'sql'		=> 'address.list',
				'required'	=> array(
					array('a.id', $zip, true)
				)
			));
			
			$this->Pager	= $Pager;
			$this->row	= $Pager['rows'][0];
			$this->id	= $Pager['rows'][0]['id'];
			
			if ( !$this->id) {
				$this->redirect('404');
			}
			parent::Editor();
			
			$this->root		= $GT8['admin']['root'] . 'address/';
			$this->sumModalColWidth	= $this->isModal? 6: 0;
		}
		public function on404() {
			if ( !$this->id) {
				parent::on404();
			}
		}
		public function update( &$field='', &$value='') {
			parent::update( $field, $value);
			
			if ( $field) {
				require_once( SROOT.'engine/queries/address/UpdateAddress.php');
				new UpdateAddress(array(
					'id'	=> $this->id,
					'field'	=> $field,
					'value'	=> $value,
					'format'=> 'JSON'
				));
			}
		}
		public function notFound() {
			//die('Address not found!');
		}
		public function prnt( $field, $props=array()) {
			switch( $field) {
				case 'attributes': {
					$this->printAttributes();
					break;
				}
				default:
					print(utf8_encode(addslashes(htmlentities($this->row[$field]))));
					break;
			}
		}
		public function setUfs() {
			$this->data['ufs']	= array(
				array('uf'=>"0",  'name'=>'Estados'),
				array('uf'=>"AC", 'name'=>'Acre'),
				array('uf'=>"AL", 'name'=>'Alagoas'),
				array('uf'=>"AM", 'name'=>'Amazonas'),
				array('uf'=>"AP", 'name'=>'Amapá'),
				array('uf'=>"BA", 'name'=>'Bahia'),
				array('uf'=>"CE", 'name'=>'Ceará'),
				array('uf'=>"DF", 'name'=>'Distrito Federal'),
				array('uf'=>"ES", 'name'=>'Espírito Santo'),
				array('uf'=>"GO", 'name'=>'Goiás'),
				array('uf'=>"MA", 'name'=>'Maranhão'),
				array('uf'=>"MG", 'name'=>'Minas Gerais'),
				array('uf'=>"MS", 'name'=>'Mato Grosso do Sul'),
				array('uf'=>"MT", 'name'=>'Mato Grosso'),
				array('uf'=>"PA", 'name'=>'Pará'),
				array('uf'=>"PB", 'name'=>'Paraíba'),
				array('uf'=>"PE", 'name'=>'Pernambuco'),
				array('uf'=>"PI", 'name'=>'Piauí'),
				array('uf'=>"PR", 'name'=>'Paraná'),
				array('uf'=>"RJ", 'name'=>'Rio de Janeiro'),
				array('uf'=>"RN", 'name'=>'Rio Grande do Norte'),
				array('uf'=>"RO", 'name'=>'Rondônia'),
				array('uf'=>"RR", 'name'=>'Roraima'),
				array('uf'=>"RS", 'name'=>'Rio Grande do Sul'),
				array('uf'=>"SC", 'name'=>'Santa Catarina'),
				array('uf'=>"SE", 'name'=>'Sergipe'),
				array('uf'=>"SP", 'name'=>'São Paulo'),
				array('uf'=>"TO", 'name'=>'Tocantins')
			);
			for( $i=0; $i<count($this->data['ufs']); $i++){
				$this->data['ufs'][$i]['name']	= utf8_decode($this->data['ufs'][$i]['name']);
				$this->data['ufs'][$i]['selected']	= $this->data['ufs'][$i]['uf'] === $this->row['stt']? 'selected="selected"': '';
			}
		}
		public function getAddressTypes($template) {
			require_once( SROOT .'engine/functions/Pager.php');
			$Pager	= Pager(array(
				'select'	=> 'at.id, at.type, IF (at.type="'. $this->row['type'] .'", "selected=\'selected\'","") AS selected',
				'from'		=> 'gt8_address_type at',
				'foundRows'	=> 10,
				'format'	=> 'TEMPLATE',
				'template'	=> $template
			));
			for( $i=0; $i<count($Pager['raw']); $i++){
				$Pager['raw'][$i]['selected']	= $Pager['raw'][$i]['type'] === $this->row['type']? 'selected="selected"': '';
			}
			return $Pager['rows'];
		}
		public function printUsage() {
			
		}
		public function getServerJSVars() {
			$this->jsVars[]	= array('userName', addslashes($_SESSION['login']['name']));
			
			return parent::getServerJSVars();
		}
	}
?>