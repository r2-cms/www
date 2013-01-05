<?php
	if ( !defined('SROOT')) {
		die('Not allowed in Qu34ie0.A114e00::De5e1e!');
	}
	/*
		Integer deleteAddress( options)
			Options:
				id
	*/
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/functions/CheckPrivileges.php");
	
	function deleteAddress( $options) {
		
		if ( isset($options['id'])) {
			$id	= (integer)$options['id'];
		}
		
		//tabela users requer privilégios específicos
		$row	= mysql_fetch_assoc(mysql_query("
			SELECT
				id, id_users
			FROM
				gt8_address
			WHERE
				id = $id
		"));
		$idUser	= $row[1];
		
		if ( $idUser != $_SESSION['login']['id']) {
			CheckPrivileges('', '', 'address/', 2);
		}
		
		mysql_query("
			DELETE FROM
				gt8_address
			WHERE
				id = $id
		");
		
		if ( mysql_affected_rows()) {
			print('//#affected rows: '. mysql_affected_rows() .'!'. PHP_EOL);
			require_once( SROOT ."engine/functions/LogAdmActivity.php");
			LogAdmActivity( array(
				"action"	=> "delete",
				"page"		=> "endereco/",
				"name"		=> '',
				"value"		=> '',
				"idRef"		=> $id
			));
		} else {
			print('//#error: '. ( $_SESSION['login']['level']>6? mysql_error(): '') .'!'. PHP_EOL);
		}
	}
?>
