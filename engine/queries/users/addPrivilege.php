<?php
	require_once( $_SERVER["DOCUMENT_ROOT"] ."/jsAdmin/check.php");
	/*
		addPrivilege
			idUser:		Required user id
			category:	[a-zA-Z0-9_ ]+
			page:		valid uri
			field:		[a-zA-Z0-9_ ]+
			privilege:	-rwx
			format:		OBJECT|JSON|XML|TABLE
			print
	*/
	function addPrivilege( $prop) {
		$idUser		= (integer)($prop["idUser"]);
		$category	= RegExp($prop["category"], "[a-zA-Z0-9_\ ]+");
		$page		= RegExp($prop["page"], "[a-zA-Z0-9_\ \/\.\,\-\\\*\+]+");
		$field		= mysql_real_escape_string($prop["field"]);
		$privilege	= RegExp($prop["privilege"], '-|r|w|x');
		$format		= isset($prop["format"])? $prop["format"]: "OBJECT";
		
		if ( !$idUser) {
			$idUser	= $_SESSION["id-admin"];
		}
		
		//o usuário tem o privilégio necessário para adicionar permissões???
		if ( CheckPrivileges( '*', "OBJECT", "users/privileges/") < 2) {
			die("Negado!");
		}
		
		$result	= mysql_query("SELECT id, nome FROM a744e89c3d WHERE id = $idUser");
		$row	= mysql_fetch_assoc($result);
		
		if ( !$row["nome"]) {
			die("Usu&aacute;rio n&atilde;o existe!");
		}
		
		LogAdmActivity( array(
			"page"		=> "/jsAdmin/Usuarios/privilegios/{$row['nome']}/",
			"action"	=> "insert",
			"name"		=> "privilegio",
			"value"		=> $value,
			"remarks"	=> "Adição de privilégio para o usuário"
		));
		
		$row	= mysql_fetch_array(mysql_query("
			SELECT
				id
			FROM
				prvlgs_flds
			WHERE
				1 = 1
				AND category	= '$category'
				AND url			= '$page'
				AND field		= '$field'
		"));
		$idPage	= $row[0];
		
		if ( !$idPage) {
			$sql	= "
				INSERT INTO prvlgs_flds(
					category, url, field
				)
				VALUES(
					'$category', '$page', '$field'
				)
			";
			$result	= mysql_query( $sql) or die("SQL UPDATE Error (1)");
			$idPage	= mysql_insert_id();
		}
		
		if ( !$idPage) {
			die("Page not found!");
		}
		
		$sql	= "
			INSERT INTO prvlgs(
				id_user, id_page, privilege
			)
			SELECT
				$idUser, $idPage, '$privilege'
			FROM
				prvlgs
			WHERE
				1 = 1
				AND id_user		= $idUser
				AND id_page		= $idPage
				AND privilege	= '$privilege'
			HAVING
				COUNT(*) = 0
		";
		$result	= mysql_query( $sql) or die("SQL INSERT Error (2)");
		
		if ( $format == "JSON") {
			print("
				//#privilege created successfully (". mysql_affected_rows() .")
				idPage	= $idPage;
			");
		}
		return true;
	}
	
	if ( isset($_GET["print"]) ) {
		$_GET["print"]	= NULL;
		print (addPrivilege(array(
			"idUser"	=> $_GET["idUser"],
			"category"	=> $_GET["category"],
			"page"		=> $_GET["page"],
			"field"		=> $_GET["field"],
			"category"	=> $_GET["category"],
			"privilege"	=> $_GET["privilege"],
			"format"	=> $_GET["format"]
		)));
	}
?>