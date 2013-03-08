<?php
	if ( !defined('SROOT')) {
		die('Not allowed in Qu34ie0.A114e00::De5e1e!');
	}
	/*
		Integer deleteContact( options)
			Options:
				id
				idRef
				tableRef
	*/
	function deleteContact( $options) {
		
		if ( isset($options['id'])) {
			$id	= (integer)$options['id'];
		}
		
		if ( !$id) {
			die('//#error: Missing id!'. PHP_EOL);
		}
		
		//tabela users requer privilégios específicos
		$idUser	= mysql_fetch_assoc(mysql_query("
		SELECT
			id, id_users, channel, type, value
		FROM
			gt8_users_contact
		WHERE
			id = $id
		"));
		
		$channel	= $idUser['channel'];
		$type		= $idUser['type'];
		$value		= $idUser['value'];
		$idUser		= $idUser['id_users'];
		
		if ( !$idUser) {
			die('//#error: Este contato já não existe mais.'. PHP_EOL);
		}
		
		//o operador precisa ter privilégios superiores ao do contato
		if ( $idUser != $_SESSION['login']['id']) {
			$row	= mysql_fetch_assoc(mysql_query("SELECT id, level+0 AS level, login FROM gt8_users WHERE id = $idUser"));
			die("<pre>". print_r( $row, 1) ."</pre>".PHP_EOL);
			if ( !($row['level'] < $_SESSION['login']['level'])) {
				die('//#error: Privilégio elevado é requerido para remover contato a este usuário!'. PHP_EOL);
			}
			print("<h1>". $row['level'] ."</h1>");
			print("<h1>". $_SESSION['login']['level'] ."</h1>".PHP_EOL);
		}
		die("<pre>". print_r( 2222222, 1) ."</pre>".PHP_EOL);
		mysql_query("
			DELETE FROM
				gt8_users_contact
			WHERE
				id = $id
		");
		
		if ( mysql_affected_rows()) {
			if ( $options['format'] === 'JSON') {
				print('//#affected rows: '. mysql_affected_rows() .'!'. PHP_EOL);
				print('//#message: contato excluído com sucesso!'. PHP_EOL);
			}
			require_once( SROOT .'engine/functions/LogAdmActivity.php');
			LogAdmActivity( array(
				"action"	=> "delete",
				"page"		=> "users/",
				"name"		=> 'contato',
				"value"		=> $channel .' - '. $type .' - '. mysql_real_escape_string($value),
				"idRef"		=> $id
			));
		} else {
			print('//#error: '. ( $_SESSION['login']['level']>6? mysql_error(): '') .'!'. PHP_EOL);
		}
	}
?>
