<?php
	if ( !defined('SROOT') ) {
		require_once('../../connect.php');
	}
	
	require_once( SROOT ."engine/classes/GT8.php");
	
	class Register extends GT8 {
		public function __construct() {
			global $GT8;
			
			header("Cache-Control: no-cache, must-revalidate");
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
			
			if ( isset($_SESSION['login']) && isset($_SESSION['login']['id']) && $_SESSION['login']['id']) {
				header('location: ../');
				die();
			}
			
			$this->checkActionRequest();
			$this->setFields();
			require_once( SROOT .'engine/functions/Pager.php');
			
		}
		private function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				if ( $_GET['action'] === 'get-zip') {
					$zip		= RegExp($_GET['zip'], '[0-9]{5}\-[0-9]{3}');
					
					require_once( SROOT .'engine/queries/address/getAddressByZip.php');
					print(Zip::getZip($zip, 'JSON'));
					
					die();
				}
				if ( $_GET['action'] === 'check-mail') {
					
					$mail	= RegExp($_GET['mail'], '[a-zA-Z@\-\.\_0-9]+');
					require_once( SROOT .'engine/functions/Pager.php');
					$Pager	= Pager(array(
						'sql'	=> 'users.list',
						'required'	=> array(
							array('login', $mail)
						)
					));
					if ( count($Pager['rows']) > 0 ) {
						print('//#error: e-mail já cadastrado!'. PHP_EOL);
					} else {
						print('//#E-mail ainda não cadastrado.'. PHP_EOL);
					}
					die();
				}
				if ( $_GET['action'] === 'create-account') {
					
					require_once( SROOT. 'engine/queries/users/InsertUser.php');
					$_GET['login']		= $_GET['mail'];
					$_GET['level']		= 1;
					$_GET['enabled']	= 1;
					$_GET['remarks']	= '';
					$_GET['createImg']		= false;
					$_GET['pass']			= $_POST['pass'];
					$_GET['format']			= 'OBJECT';
					$idUser	= InsertUser($_GET);
					//$idUser	= 19;
					
					if ( !$idUser) {
						die('Não foi possível criar o novo cadastro. Por favor, tente mais tarde.');
					}
					
					$_POST["pass"]	= md5($_POST["pass"] .'-'. $_SESSION['GT8']['tstart']);
					$_GET["user"]	= $_GET['login'];
					require_once( SROOT .'engine/functions/CheckLogin.php');
					
					require_once( SROOT. 'engine/queries/users/addContact.php');
					addContact(array(
						'format'	=> 'OBJECT',
						'idUser'	=> $idUser,
						'channel'	=> 'Telefone',
						'type'		=> 'Residencial',
						'value'		=> substr(RegExp($_GET['phone-mobile'], '[0-9\(\)\ \-\.\#]+'), 0, 20)
					));
					addContact(array(
						'format'	=> 'OBJECT',
						'idUser'	=> $idUser,
						'channel'	=> 'Telefone',
						'type'		=> 'Comercial',
						'value'		=> ''
					));
					addContact(array(
						'format'	=> 'OBJECT',
						'idUser'	=> $idUser,
						'channel'	=> 'Celular',
						'type'		=> 'Pessoal',
						'value'		=> substr(RegExp($_GET['phone-mobile'], '[0-9\(\)\ \-\.\#]+'), 0, 20)
					));
					
					require_once( SROOT .'engine/queries/address/addNewAddress.php');
					addNewAddress(array(
						'log'			=> false,
						'idUser'		=> $idUser,
						'type'			=> 1,
						'street'		=> $_GET['street'],
						'number'		=> $_GET['number'],
						'complement'	=> $_GET['complement'],
						'reference'		=> $_GET['reference'],
						'zip'			=> $_GET['zip'],
						'district'		=> $_GET['district'],
						'city'			=> $_GET['city'],
						'stt'			=> $_GET['stt']
					));
					
					header('location: ../');
					die();
				}
				if ( $_GET['action'] == 'set-pass') {
					//$this->update( 'pass', $_POST['pass']);
					
					die();
				}
			}
		}
		private function setFields() {
			$this->data['estados']	= array(
				array('Estados',			'',		'',					1,	''),
				array('Acre',				'AC',	'Rio Branco',		0,	'Norte'),
				array('Alagoas',			'AL', 	'Maceió',			0,	'Nordeste'),
				array('Amapá',				'AP',	'Macapá',			0,	'Norte'),
				array('Amazonas',			'AM',	'Manaus',			0,	'Norte'),
				array('Bahia',				'BA',	'Salvador',			0,	'Nordeste'),
				array('Ceará',				'CE',	'Fortaleza',		0,	'Nordeste'),
				array('Distrito Federal',	'DF',	'Brasília',			0,	'Centro-Oeste'),
				array('Espírito Santo',		'ES',	'Vitória',			0,	'Sudeste'),
				array('Goiás',				'GO',	'Goiânia',			0,	'Centro-Oeste'),
				array('Maranhão',			'MA',	'São Luiz', 		0,	'Nordeste'),
				array('Mato Grosso',		'MT',	'Cuiabá',			0,	'Centro-Oeste'),
				array('Mato Grosso do Sul',	'MS',	'Campo Grande',		0,	'Centro-Oeste'),
				array('Minas Gerais', 		'MG',	'Belo Horizonte',	0,	'Sudeste'),
				array('Paraná',				'PR',	'Curitiba',			0,	'Sul'),
				array('Paraíba',			'PB',	'João Pessoa',		0,	'Nordeste'),
				array('Pará',				'PA',	'Belém',			0,	'Norte'),
				array('Pernambuco',			'PE',	'Recife',			0,	'Nordeste'),
				array('Piauí',				'PI',	'Terezina',			0,	'Nordeste'),
				array('Rio de Janeiro',		'RJ',	'Rio de Janeiro',	0,	'Sudeste'),
				array('Rio Grande do Norte','RN',	'Natal',			0,	'Nordeste'),
				array('Rio Grande do Sul',	'RS',	'Porto Alegre',		0,	'Sul'),
				array('Rondonia',			'RO',	'Porto Velho',		0,	'Norte'),
				array('Roraima',			'RR',	'Boa Vista',		0,	'Norte'),
				array('Santa Catarina',		'SC',	'Florianópolis',	0,	'Sul'),
				array('Sergipe',			'SE',	'Aracajú',			0,	'Nordeste'),
				array('São Paulo',			'SP',	'São Paulo',		0,	'Sudeste'),
				array('Tocantins',			'TO',	'Palmas',			0,	'Norte')
			);
		}
	}
?>