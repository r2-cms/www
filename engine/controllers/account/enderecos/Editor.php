<?php
	if ( !defined('SROOT') ) {
		require_once('../../connect.php');
	}
	
	require_once( SROOT ."engine/controllers/account/Account.php");
	
	class Editor extends Account {
		public function __construct() {
			global $GT8, $paths;
			
			
			$id	= $paths;
			if ( empty($id[count($id)-1])) {
				array_pop($id);
			}
			$this->id	= (integer)$id[count($id)-1];
			
			$this->checkActionRequest();
			$this->setFields();
		}
		public function on404() {
			//parent::on404();
		}
		private function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				$idLogin	= (integer)$_SESSION['login']['id'];
				
				if ( $_GET['action'] == 'save-account-address') {
					
					$id			= (integer)$_GET['id'];
					$type		= (integer)$_GET['type'];
					$zip		= RegExp($_GET['zip'], '[0-9]{5}\-[0-9]{3}');
					$street		= mysql_real_escape_string($_GET['street']);
					$number		= mysql_real_escape_string($_GET['number']);
					$complement	= mysql_real_escape_string($_GET['complement']);
					$reference	= mysql_real_escape_string($_GET['reference']);
					$district	= mysql_real_escape_string($_GET['district']);
					$city		= mysql_real_escape_string($_GET['city']);
					$stt		= RegExp($_GET['stt'], '[A-Z]{2}');
					mysql_query("
						UPDATE
							gt8_address
						SET
							id_type		= $type,
							zip			= '$zip',
							street		= '$street',
							number		= '$number',
							complement	= '$complement',
							reference	= '$reference',
							district	= '$district',
							city		= '$city',
							stt			= '$stt'
						WHERE
							1 = 1
							AND id = $id
							AND id_users	= $idLogin
					". PHP_EOL) or die('//#error: Erro interno. Por favor, tente mais tarde'. PHP_EOL);
					print('//#affected rows: 1'. PHP_EOL);
					print("//#message: Endereço atualizado com sucesso!". PHP_EOL);
					die();
				}
				if ( $_GET['action'] == 'delete-account-address') {
					$id			= (integer)$_GET['id'];
					mysql_query("
						DELETE FROM
							gt8_address
						WHERE
							1 = 1
							AND id = $id
							AND id_users	= $idLogin
					". PHP_EOL) or die('//#error: Erro interno. Por favor, tente mais tarde'. PHP_EOL);
					print('//#affected rows: 1'. PHP_EOL);
					print("//#message: Endereço excluído com sucesso!". PHP_EOL);
					die();
				}
			}
		}
		private function setFields() {
			
			require_once(SROOT .'engine/functions/Pager.php');
			$Pager	= Pager(array(
				'sql'	=> 'address.list',
				'ids'	=> array(
					array('a.id_users', $_SESSION['login']['id']),
					array('a.id', $this->id)
				)
			));
			
			if ( count($Pager['rows']) == 0) {
				$this->on404();
			}
			
			$this->data	= $Pager['rows'][0];
			
			$this->data['estados']	= array(
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
			$stt	= $this->data['stt'];
			foreach ( $this->data['estados'] AS $i=>$value) {
				if ( strtoupper($stt) == $value[1]) {
					$this->data['estados'][$i][3]	= true;
					break;
				}
			}
			$result	= mysql_query("SELECT id, type, IF( type='{$this->data['type']}', 1, 0) AS selected FROM gt8_address_type");
			$this->data['types']	= array();
			while( ($row=mysql_fetch_assoc($result))) {
				$this->data['types'][]	= $row;
			}
		}
	}
?>