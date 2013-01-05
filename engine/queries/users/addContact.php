<?php
	if ( !defined('SROOT')) {
		die('Not allowed in Qu34ie0.U0e40::A11Co21a71!');
	}
	/*
		Integer addContact( options)
			Options:
				format
				idUser
				channel
				type
				value
				
	*/
	global $GT8;
	require_once( SROOT .'engine/functions/CheckLogin.php');
	require_once( SROOT .'engine/functions/Pager.php');
	
	function addContact( $options) {
		
		$idUser		= (integer)$options['idUser'];
		$channel	= $options['channel'];
		$type		= $options['type'];
		$value		= mysql_real_escape_string($options['value']);
		if ( !isset($options['format'])) {
			$options['format']	= 'JSON';
		}
		
		if ( !$idUser) {
			die('//#error: Usuário inválido!'. PHP_EOL);
		}
		
		//privilégios
		//o operador precisa ter privilégios superiores ao do contato
		if ( $idUser != $_SESSION['login']['id']) {
			$row	= mysql_fetch_assoc(mysql_query("SELECT id, level+0 AS level FROM gt8_users WHERE id = $idUser"));
			if ( !($row['level'] < $_SESSION['login']['level'])) {
				if ( $options['format'] === 'JSON') {
					die('//#error: Privilégio elevado é requerido para adicionar contato a este usuário!'. PHP_EOL);
				} else if ($options['format'] === 'OBJECT') {
					return 'ERROR: higher privilege required!';
				} else {
					return false;
				}
			}
			
			require_once( SROOT .'engine/functions/CheckPrivileges.php');
			CheckPrivileges( '',$options['format'],'users/contacts/', 2);
		}
		
		//validação dos enumeradores
		$result			= mysql_query("DESCRIBE gt8_users_contact");
		$typeFound		= false;
		$channelFound	= false;
		while( ($row=mysql_fetch_assoc($result))) {
			if ( $row['Field']=='type' && strpos('#'.strtolower($row['Type']),strtolower($type)) > 0 ) {
				$typeFound	= true;
			}
			if ( $row['Field']=='channel' && strpos('#'.strtolower($row['Type']),strtolower($channel)) > 0 ) {
				$channelFound	= true;
			}
		}
		
		if ( !$typeFound) {
			$type	= 'Outro';
		}
		if ( !$channelFound) {
			$channel	= 'Outro';
		}
		
		mysql_query("
			INSERT INTO gt8_users_contact(
				id_users, 
				channel, type, value,
				creation, modification
			) VALUES(
				$idUser,
				'$channel', '$type', '$value',
				NOW(), NOW()
			)
		") or die($_SESSION['login']['level']>8?mysql_error(): '//#error: valores inválidos!'.PHP_EOL);
		
		if ( mysql_insert_id()) {
			if ( $options['format'] === 'JSON') {
				print('//#affected rows: 1'. PHP_EOL);
				print('//#insert id: '. mysql_insert_id() . PHP_EOL);
				print('//#message: contato adicionado com sucesso!'. PHP_EOL);
			}
			if ( $_SESSION['login']['level'] > 3 ) {
				require_once( SROOT .'engine/functions/LogAdmActivity.php');
				LogAdmActivity( array(
					"action"	=> "insert",
					"page"		=> "users/",
					"name"		=> 'contato',
					"value"		=> '# '. mysql_insert_id() ." - $channel - $type - $values",
					"idRef"		=> $idUser
				));
			}
		} else {
			if ( $options['format'] === 'JSON') {
				print('//#error: não foi possível criar um contato agora!<br />Por favor, tente mais tarde.'. PHP_EOL);
			} else if ($options['format'] === 'OBJECT') {
				return 'ERROR: could not create contact now!';
			} else {
				return false;
			}
		}
	}
?>
