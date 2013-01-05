<?php
	if ( !defined('SROOT')) {
		die('Not allowed in Qu34ie0.A114e00!');
	}
	/*
		Integer addNewAddress( options)
			Options:
				idUser
				type
				street
				number
				complement
				reference
				zip
				district
				city
				stt,
				log		true|false
	*/
	require_once( SROOT .'engine/functions/CheckLogin.php');
	function addNewAddress( $options) {
		
		$idUser	= isset($options['idUser'])? (integer)$options['idUser']: 0;
		if ( $idUser && $idUser != $_SESSION['login']['id']) {
			require_once( SROOT .'engine/functions/CheckPrivileges.php');
			CheckPrivileges('', '', 'address/', 2);
		} else {
			$idUser	= $_SESSION['login']['id'];
		}
		
		$options['log']	= isset($options['log'])? $options['log']: true;
		
		$type		= (integer)$options['type'];
		$street		= mysql_real_escape_string(substr(trim($options['street']), 0, 100));
		$number		= mysql_real_escape_string(substr(RegExp(trim($options['number']), '[0-9A-Za-z\ \.\,\-]+'), 0, 10));
		$complement	= mysql_real_escape_string(substr((trim($options['complement'])), 0, 30));
		$reference	= mysql_real_escape_string(substr((trim($options['reference'])), 0, 200));
		$zip		= RegExp(trim($options['zip']), '[0-9]{5}\-[0-9]{3}');
		$district	= mysql_real_escape_string(substr((trim($options['district'])), 0, 20));
		$city		= mysql_real_escape_string(substr((trim($options['city'])), 0, 30));
		$stt		= RegExp(trim($options['stt']), '[A-Z]{2}');
		
		if ( !$type) {
			$type	= 1;
		}
		
		if ( !$idUser) {
			if ( $options['format'] == 'JSON') {
				die('//#error: Não foi possível identificar o usuário! Por favor, tente mais tarde.');
			} else if ( $options['format'] == 'OBJECT') {
				return 'ERROR: invalid user id!';
			} else {
				return false;
			}
			
		} else if ( !$street) {
			if ( $options['format'] == 'JSON') {
				die('//#error: O nome da rua/avenida é obrigatório! Por favor, corriga para prosseguir.');
			} else if ( $options['format'] == 'OBJECT') {
				return 'ERROR: missing street field!';
			} else {
				return false;
			}
			
		} else if ( strlen($zip) > 9) {
			if ( $options['format'] == 'JSON') {
				die('//#error: CEP no formato incorreto! Por favor, corriga para prosseguir.');
			} else if ( $options['format'] == 'OBJECT') {
				return 'ERROR: incorrect zip format!';
			} else {
				return false;
			}
			
		} else if ( strlen($stt) != 2) {
			if ( $options['format'] == 'JSON') {
				die('//#error: Estado no formato errado! Por favor, corrija para prosseguir');
			} else if ( $options['format'] == 'OBJECT') {
				return 'ERROR: incorrect state format!';
			} else {
				return false;
			}
			
		}
		
		$r	= mysql_query("INSERT INTO gt8_address(
			id_users, id_type, street,
			number, complement, reference,
			zip, district, city,
			stt, active,
			creation, modification
		) VALUES(
			$idUser, '$type', '$street',
			'$number', '$complement', '$reference',
			'$zip', '$district', '$city',
			'$stt', 1,
			NOW(), NOW()
		)") or die(mysql_error());
		
		if ( mysql_insert_id()) {
			if ( $options['format'] === 'JSON') {
				print('//#affected rows: 1!'. PHP_EOL);
				print('//#insert id: '. mysql_insert_id() .PHP_EOL);
				print('//#message: endereço criado com sucesso.'. PHP_EOL);
			}
			if ( $_SESSION['login']['level'] > 3 ) {
				require_once( SROOT ."engine/functions/LogAdmActivity.php");
				LogAdmActivity( array(
					"action"	=> "insert",
					"page"		=> "endereco/",
					"name"		=> '',
					"value"		=> '',
					"idRef"		=> mysql_insert_id()
				));
			}
			return mysql_insert_id();
		} else {
			if ( $options['format'] === 'JSON') {
				die('//#error: Não foi possível inserir um novo endereço agora!'. PHP_EOL);
			} else if ( $options['format'] === 'OBJECT') {
				return 'ERROR: could not create an address!';
			} else {
				return false;
			}
		}
	}
?>
