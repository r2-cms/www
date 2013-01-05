<?php
	/*
		Boolean AddFamily( props)
			Properties:
				idFamily	Required.
				idProduct	Required.
		Sample:
			AddFamily(array(
				"idFamily"	=> $_GET["idFamily"],
				"idProduct"	=> $_GET["idProduct"]
			));
		
	*/
	if ( !defined('CROOT')) {
		die('Undefined connection! (Q40->P4o1u710::A118a3i5y)');
	}
	require_once( SROOT . $GT8['admin']['root'] ."check.php");
	function AddFamily( $props) {
		CheckPrivileges('', '', "produtos/grupos/", 2);
		
		$idFamily	= (integer)$props["idFamily"];
		$idProduct	= (integer)$props["idProduct"];
		
		//errors
		if ( !$idFamily ) {
			print(PHP_EOL ."//subcategory id missing!");
			return;
		} else if ( !$idProduct ) {
			print(PHP_EOL ."//product id missing!");
			return;
		}
		
		//get family name
		$row	= mysql_fetch_array(mysql_query("SELECT title FROM gt8_level_family f WHERE f.id = $idFamily"));
		$family	= mysql_real_escape_string($row[0]);
		
		if ( !$family) {
			die("Invalid family");
		}
		
		require_once( SROOT ."queries/groups/addGroup.php");
		AddGroup(array(
			"idFamily"	=> $idFamily,
			"group"		=> utf8_decode("Família"),
			"subgroup"	=> $family
		));
		
		//get new sg
		$row	= mysql_fetch_array(mysql_query("
			SELECT
				sg.id
			FROM
				gt8_level_family f
				JOIN gt8_level_group g		ON f.id  = g.id_family
				JOIN gt8_level_subgroup sg	ON g.id = sg.id_group
			WHERE
				g.title = 'familia' AND 
				g.id_family = $idFamily
		"));
		$idSubgroup	= $row[0];
		
		if ( !$idSubgroup) {
			die("Houve um problema com os grupos!");
		}
		
		//insert into t the new family
		mysql_query("
			INSERT INTO
				gt8_level_tie( id_products, id_subgroup)
				SELECT
					$idProduct, $idSubgroup
				FROM
					gt8_level_tie
				WHERE
					id_products = $idProduct AND
					id_subgroup = $idSubgroup
				HAVING
					COUNT(*) = 0
		") or die("t error: ". mysql_error());
		print PHP_EOL ."//#tie inserted successfully! (". mysql_insert_id() .")";
		
		LogAdmActivity( array(
			"action"	=> "insert",
			"page"		=> "products/",
			"name"		=> 'family',
			"value"		=> $family,
			"idRef"		=> $idProduct
		));
		
		print PHP_EOL ."//#family inserted successfully! ($idSubgroup)";
		return;
	}
?>
