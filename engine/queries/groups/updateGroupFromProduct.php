<?php
	/*
		updateGroupFromProduct
			idProduct:		Required product id
			idSubgroup:		Required old subgroup id
			checked:		boolean
			format:			OBJECT|JSON|XML|TABLE
			execute
			print
		Sample:
			updateGroupFromProduct(array(
				"idProduct"		=> $_GET["idProduct"],
				"idSubgroup"	=> $_GET["idSubgroup"],
				"checked"		=> $_GET["checked"],
				"format"		=> $_GET["format"]
			));
	*/
	if ( !defined('CROOT')) {
		die('Undefined connection! (Q40->G4ou90::U91da1e)');
	}
	require_once( SROOT . $GT8['admin']['root'] ."check.php");
	function updateGroupFromProduct( $prop) {
		CheckPrivileges('', '', "produtos/grupos/", 2);
		
		$idProduct		= (integer)($prop["idProduct"]);
		$idSubgroup		= (integer)$prop["idSubgroup"];
		$checked		= (boolean)isset($prop["checked"]);
		$format			= isset($prop["format"])? $prop["format"]: "OBJECT";
		
		if ( !$idProduct) {
			print( PHP_EOL ."//product id missing!". PHP_EOL);
			return;
		}
		
		if ( !$idSubgroup) {
			print( PHP_EOL ."//subgroup id missing!". PHP_EOL);
			return;
		}
		
		//obtem o nome do grupo
		$rowInfo	= mysql_fetch_array(mysql_query("
			SELECT
				g.title AS `group`, sg.title AS subgroup
			FROM
				gt8_level_subgroup sg
				JOIN gt8_level_group g	ON g.id  = sg.id_group
			WHERE
				sg.id		= $idSubgroup
		"));
		
		if ( $checked) {
			$sql	= "
				INSERT INTO gt8_level_tie  (id_products, id_subgroup)
					
					SELECT
						$idProduct, $idSubgroup
					FROM
						gt8_level_tie
					WHERE
						id_products	= $idProduct AND
						id_subgroup	= $idSubgroup
					HAVING
						COUNT(*) = 0
			";
			mysql_query($sql);
			if ( $format == "JSON") {
				print(PHP_EOL ."//#inserted id: ". mysql_insert_id() . PHP_EOL);
			}
			LogAdmActivity( array(
				"action"	=> "insert",
				"page"		=> "products/groups/",
				"name"		=> $rowInfo[0],
				"value"		=> $rowInfo[1],
				"idRef"		=> $idProduct
			));
		} else {
			$sql	= "
				DELETE FROM
					gt8_level_tie
				WHERE
					id_products	= $idProduct AND
					id_subgroup	= $idSubgroup
			";
			mysql_query( $sql);
			if ( $format == "JSON") {
				print(PHP_EOL ."//#removed rows: ". mysql_affected_rows() . PHP_EOL);
			}
			LogAdmActivity( array(
				"action"	=> "delete",
				"page"		=> "products/groups/",
				"name"		=> $rowInfo[0],
				"value"		=> $rowInfo[1],
				"idRef"		=> $idProduct
			));
		}
		
		return true;
	}
?>