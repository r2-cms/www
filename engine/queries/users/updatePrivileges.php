<?php
	if ( !defined('SROOT')) {
		die('Error: Que4ie0->U0e40->P4i8i5e6e0');
	}
	global $GT8;
	require_once( SROOT ."engine/functions/CheckLogin.php");
	/*
		updatePrivileges
			idUser:			user id. If null, login is required.
			login:			user login. If null, idUser is required.
			idPrivilege:	Required page id
			privilege:		-rwx
			format:			OBJECT|JSON|XML|TABLE
	*/
	function updatePrivileges( $options) {
		$user	= '';
		if ( isset($options['idUser'])) {
			$idUser	= (integer)($options["idUser"]);
			$user	= 'u.id = '. $idUser;
		} else {
			$login	= RegExp($options["login"], '[a-zA-Z0-9_\-\.]+');
			$user	= "u.login = '$login'";
		}
		$idPrivilege		= (integer)($options["idPrivilege"]);
		$privilege	= RegExp($options["privilege"], "-|r|w|x");
		$format		= isset($options["format"])? $options["format"]: "OBJECT";
		
		//o usuário tem o privilégio necessário para alterar permissões???
		CheckPrivileges( '', $format, 'users/privileges/', 2);
		
		if ( !$idPrivilege ) {
			die("//#error: ID do privilégio da página de referência não está presente!". PHP_EOL);
		}
		
		$result	= mysql_query("SELECT id, login, level+0 AS level FROM gt8_users u WHERE $user");
		$row	= mysql_fetch_assoc($result);
		$idUser	= $row['id'];
		
		if ( !$row["login"]) {
			die("//#error: não foi possível encontrar este usuário!". PHP_EOL);
		}
		
		//tem privilégio igual ou superior ao do usuário que deseja alterar?
		if ( $idUser != $_SESSION['login']['level']) {
			if ( $_SESSION['login']['level'] < $row['level'] ) {
				die("//#error: Seu tipo de usuário não permite alterar os privilégios deste usuário!". PHP_EOL);
			}
		}
		
		//crie o privilégio para este usuário, se não existir
		$sql	= "
			INSERT INTO gt8_privileges_fields(id_privileges, id_users, privilege)
			SELECT
				$idPrivilege, $idUser, '$privilege'
			FROM
				gt8_privileges_fields
			WHERE
				1 = 1
				AND id_users	= $idUser
				AND id_privileges	= $idPrivilege
			HAVING
				COUNT(*) = 0
		";
		$result	= mysql_query( $sql) or die($_SESSION['login']['level']>7? "SQL INSERT Error:". mysql_error(): '');
		
		$sql	= "
			UPDATE
				gt8_privileges_fields
			SET
				privilege = '$privilege'
			WHERE
				1=1
				AND id_users = $idUser
				AND id_privileges = $idPrivilege
		";
		//die($sql);
		$result	= mysql_query( $sql) or die($_SESSION['login']['level']>7? "SQL UPDATE Error:". mysql_error(): '');
		
		$afftected	= mysql_affected_rows();
		
		if ( $afftected) {
			//qual o nome do campo?
			$fieldName	= mysql_fetch_array(mysql_query("SELECT url FROM gt8_privileges WHERE id = $idPrivilege"));
			$fieldName	= $fieldName[0];
			
			require_once( SROOT .'engine/functions/LogAdmActivity.php');
			LogAdmActivity( array(
				"page"		=> "users/privileges/",
				"action"	=> "edit",
				"name"		=> $fieldName,
				"value"		=> $privilege,
				'idRef'		=> $idUser,
				"remarks"	=> "Alteração de privilégios do usuário"
			));
		}
		if ( $format == "JSON") {
				print("//#affected rows: 1". PHP_EOL);
				print("//#message: Privilégio atualizado com sucesso!". PHP_EOL);
		}
		return true;
	}
?>