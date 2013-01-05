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
			
			
		}
		public function update() {
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
		public function getDirLocation() {
			global $GT8;
			$base	= str_replace('//', '/', CROOT . $this->root);
			$base	= CROOT . $this->root;
			$row	= $this->row;
			
			$estado	= $this->getStt();
			foreach ($estado as $name=>$value) {
				if ( $value[0] == $row['stt']) {
					$estado	= $value;
					break;
				}
			}
			$path	= CROOT . $GT8['admin']['root'].$GT8['address']['root'];
			$html	= '<a href="'. $path .'" class="button" style="z-index:7; padding-left:10px;" >Endereços</a>';
			$html	.= '<a class="button" id="nav-1" href="../?uf='. $estado[0] .'" style="z-index:3; " >'. $estado[1] .'</a>';
			$html	.= '<a class="button" id="nav-2" href="../?cidade='. $row['city'] .'" style="z-index:2; " >'. utf8_encode(htmlentities($row['city'])) .'</a>';
			$html	.= '<a class="button" id="nav-3" href="'. $row['zip'] .'/" style="z-index:1; " >'. utf8_encode(htmlentities($row['street'])) .', '. $row['number'] .'</a>';
			
			return $html;
		}
		public function getStt() {
			$arr	= array(
				array("0", 'Estados'),
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
			return $arr;
		}
		public function getType() {
			$arr	= array(
				array("Home", 'Residencial'),
				array("Business", 'Comercial'),
				array("Delivery", 'Entrega'),
				array("Friend", 'Amigos'),
				array("Office", 'Escritório'),
				array("Relative", 'Familiar'),
				array("Vacation", 'Férias'),
				array("Work", 'Trabalho'),
				array("Other", 'Outro'),
			);
			return $arr;
		}
		public function printUsage() {
			
		}
		public function getServerJSVars() {
			$this->jsVars[]	= array('userName', addslashes($_SESSION['login']['name']));
			
			return parent::getServerJSVars();
		}
	}
?>