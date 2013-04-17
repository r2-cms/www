<?php
	if ( !defined('CROOT')) {
		//require_once( "../../../../engine/connect.php");
		die('Undefined GT8: a1i32->a114e00->e1i1o4->Home');
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/functions/CheckPrivileges.php");
	
	CheckPrivileges( null, null,'address/', 1);
	require_once( SROOT .'engine/classes/Editor.php');
	
	class NewAddress extends Editor {
		public $id	= 0;
		public $name	= 'address';
		public $row	= array();
		
		function __construct() {
			global $GT8, $paths;
			
			$this->checkActionRequest();
		}
		private function checkActionRequest() {
			if ( isset($_GET['action'])) {
				if ( $_GET['action'] == 'add-new-address' ) {
					require_once( SROOT .'/engine/queries/address/addNewAddress.php');
					addNewAddress($_GET);
					die();
				}
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
		public function getAddressTypes($template) {
			require_once( SROOT .'engine/functions/Pager.php');
			$Pager	= Pager(array(
				'select'	=> 'at.id, at.type',
				'from'		=> 'gt8_address_type at',
				'foundRows'	=> 10,
				'format'	=> 'TEMPLATE',
				'template'	=> $template
			));
			
			return $Pager['rows'];
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
			}
		}
		public function getServerJSVars() {
			$this->jsVars[]	= array('userName', addslashes($_SESSION['login']['name']));
			
			return parent::getServerJSVars();
		}
	}
?>