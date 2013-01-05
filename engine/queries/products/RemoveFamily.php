<?php
	/*
		Boolean RemoveFamily( props)
			Properties:
				idFamily	Required.
				idProduct		Required.
	*/
	if ( !defined('CROOT')) {
		die('Undefined connection! (Q40->P4o1u710::Re339e8a3i5y)');
	}
	require_once( SROOT . $GT8['admin']['root'] ."check.php");
	CheckPrivileges('', '', "produtos/grupos/", 2);
	function RemoveFamily( $props) {
		$idTie		= (integer)$props["idTie"];
		$idProduct	= (integer)$props["idProduct"];
		
		//errors
		if ( !$idTie ) {
			print(PHP_EOL ."//tie id missing!");
			return;
		} else if ( !$idProduct ) {
			print(PHP_EOL ."//product id missing!");
			return;
		}
		
		//a maior parte deste código destina-se apenas para fins de log
		$row		= mysql_fetch_assoc(mysql_query("
			SELECT
				l.title AS line, f.title AS family, sg.title AS subgroup
			FROM
				gt8_level_tie t
				JOIN gt8_level_subgroup sg ON sg.id = t.id_subgroup
				JOIN gt8_level_group g ON g.id = sg.id_group
				JOIN gt8_level_family f ON f.id = g.id_family
				JOIN gt8_level l ON l.id = f.id_level
			WHERE
				t.id = $idTie
			LIMIT
				1
		"));
		$subgroup	= $row['subgroup'];
		$family		= $row['family'];
		$line		= $row['line'];
		if ( $family) {
			
			LogAdmActivity( array(
				"action"	=> "delete",
				"page"		=> "products/",
				"name"		=> 'family',
				"value"		=> "$line::$family",
				"idRef"		=> $idProduct
			));
		}
		
		//apenas este único código é necessário para se eliminar a família secundária do produto
		mysql_query("
			DELETE
				FROM
					gt8_level_tie
				WHERE
					id = $idTie
		") or die("tie delete error");
		
		print PHP_EOL ."//#family removed successfully! ($idSubgroup)";
		return;
	}
	
	if ( isset($_GET["print"]) ) {
		RemoveFamily(array(
			"idTie"			=> $_GET["idTie"],
			"idProduct"		=> $_GET["idProduct"]
		));
	}
?>
