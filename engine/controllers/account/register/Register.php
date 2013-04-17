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
			$this->data['name']	= '';
			$this->data['birth']	= '';
			$this->data['cpfcnpj']	= '';
			$this->data['document']	= '';
			$this->data['mail']	= '';
			$this->data['phone-home']	= '';
			$this->data['phone-mobile']	= '';
			$this->data['zip']	= '';
			$this->data['street']	= '';
			$this->data['number']	= '';
			$this->data['complement']	= '';
			$this->data['reference']	= '';
			$this->data['district']	= '';
			$this->data['city']	= '';
			$this->data['pass1']	= '';
			$this->data['stt']	= '';
			$this->data['natureza']	= '';
			$this->data['genre']	= '';
			$this->data['accountError']	= '';
			
			$this->checkActionRequest();
			$this->setFields();
			require_once( SROOT .'engine/functions/Pager.php');
			
		}
		protected function redirectToAccount() {
			header('location: ../');
			die();
		}
		private function checkActionRequest() {
			global $GT8;
			
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
						print('//#message: cadastro disponível!'. PHP_EOL);
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
					
					if ( !$idUser || $idUser === 'login already exists') {
						//Rigel Sèi "><script>alert(666)</script>
						$this->addExpirableData('name', htmlentities(utf8_encode($_GET['name'])), 300);
						$this->addExpirableData('birth', RegExp($_GET['birth'], '[0-9\-\/]+'), 300);
						$this->addExpirableData('cpfcnpj', RegExp($_GET['cpfcnpj'], '[0-9\.\-\/]+'), 300);
						$this->addExpirableData('document', RegExp($_GET['document'], '[0-9\.\-\/a-zA-Z\ ]+'), 300);
						$this->addExpirableData('mail', RegExp($_GET['mail'], '[A-Za-z0-9_\-\.\:\@]+'), 300);
						$this->addExpirableData('phone-home', RegExp($_GET['phone-home'], '[0-9\-\(\)\ \.]+'), 300);
						$this->addExpirableData('phone-mobile', RegExp($_GET['phone-mobile'], '[0-9\-\(\)\ \.]+'), 300);
						$this->addExpirableData('zip', RegExp($_GET['zip'], '[0-9]{5}\-[0-9]{3}'), 300);
						$this->addExpirableData('street', htmlentities(utf8_encode($_GET['street'])), 300);
						$this->addExpirableData('number', htmlentities(utf8_encode($_GET['number'])), 300);
						$this->addExpirableData('complement', htmlentities(utf8_encode($_GET['complement'])), 300);
						$this->addExpirableData('reference', htmlentities(utf8_encode($_GET['reference'])), 300);
						$this->addExpirableData('district', htmlentities(utf8_encode($_GET['district'])), 300);
						$this->addExpirableData('city', htmlentities(utf8_encode($_GET['city'])), 300);
						$this->addExpirableData('pass1', RegExp($_GET['pass1'], '[a-zA-Z0-9]+'));
						$this->addExpirableData('pass2', RegExp($_GET['pass2'], '[a-zA-Z0-9]+'));
						$this->addExpirableData('stt', RegExp($_GET['stt'], '[A-Z]{2,2}'), 300);
						$this->addExpirableData('natureza', RegExp($_GET['natureza'], 'F|J'), 300);
						$this->addExpirableData('genre', RegExp($_GET['genre'], 'M|F'), 300);
						
						if ( $idUser == 'login already exists') {
							$this->addMessage('accountError', '
								<p>
									O e-mail escolhido já está sendo usado! Por favor, escolha outro e-mail
								</p>
								<p>
									Se este e-mail {{mail}} lhe pertence, <a href="{{CROOT}}{{GT8:account.root}}" >clique aqui</a> para acessar seu cadastro. <br />
									Se você esqueceu a senha, <a href="{{CROOT}}{{GT8:account.root}}{{GT8:account.resetPassword.root}}" >clique aqui</a> para recuperar a senha.
								</p>
							');
							$this->addExpirableData('accountError', $idUser);
						} else {
							$this->addMessage('accountError', '
								<p>
									Ocorreu um erro ao registrar o cadastro!
								</p>
								<p>
									Por favor, certifique-se que as informações digitadas estão corretas e/ou 
									aguarde alguns instantes e tente novamente.
								</p>
								<p>
									Se precisar de ajuda, contate nossa Central de Atendimento '. $this->getParam('phone-comercial') .', das '. $this->getParam('opening-hours') .'.
								</p>
							');
							$this->addExpirableData('account-error', 'undefined error');
							die('Não foi possível criar o novo cadastro. Por favor, tente mais tarde.');
						}
						header('location: ./');
						die();
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
					
					$this->redirectToAccount();
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