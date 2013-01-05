<?php
	/*
		Boolean AddGroup( props)
			Properties:
				idFamily	Optional family id. If null, line will be required
				group		group name
				subgroup	subgroup name
	*/
	require_once( $_SERVER["DOCUMENT_ROOT"] ."/jsAdmin/check.php");
	CheckPrivileges('', '', "produtos/grupos/", 2);
	function AddGroup( $props) {
		$idFamily	= isset($props["idFamily"])? (integer)$props["idFamily"]: 0;
		$group			= mysql_real_escape_string($props["group"]);
		$subgroup		= mysql_real_escape_string($props["subgroup"]);
		
		if ( !$idFamily ) {
			print(PHP_EOL ."//idFamily missing!");
			return;
		}
		
		$sql	= "SELECT title FROM gt8_level_family WHERE id = '". $idFamily ."' LIMIT 1";
		$result	= mysql_query($sql) or die("MySQL SELECT Error (family.id). ". mysql_error());
		$row	= mysql_fetch_array($result);
		$family	= $row[0];
		
		//errors
		if ( !$idFamily ) {
			print(PHP_EOL ."//idFamily missing!");
			return;
		} else if ( !$group ) {
			print(PHP_EOL ."//group missing!");
			return;
		} else if ( !$subgroup ) {
			print(PHP_EOL ."//subgroup missing!");
			return;
		}
		
		//insert into level_group if not exists
		$sql	= "
			INSERT INTO gt8_level_group(title, id_family, creation)
			
			SELECT
				'$group', $idFamily, NOW()
			FROM
				gt8_level_family f,
				gt8_level_group g
			WHERE
				f.id = g.id_family
				AND f.id = $idFamily AND g.title = '$group'
			HAVING
				COUNT(*) = 0
		";
		mysql_query($sql) or die("//MySQL INSERT Error (level group)");
		
		$sql	= "
			SELECT
				g.id, f.title
			FROM
				gt8_level_family f,
				gt8_level_group g
			WHERE
				f.id = g.id_family AND
				f.id = $idFamily AND
				g.title = '$group'
			LIMIT
				1
		";
		$result	= mysql_query($sql) or die("//MySQL SELECT Error (subgroup.id query)!");
		$row	= mysql_fetch_array( $result);
		$idGroup	= $row[0];
		$familia	= $row[1];
		
		
		//insert subgroup level if not exists
		$sql	= "
			INSERT INTO gt8_level_subgroup(title, id_group)
			
			SELECT
				'$subgroup', $idGroup
			FROM
				gt8_level_family f,
				gt8_level_group g,
				gt8_level_subgroup sg
			WHERE
				f.id = g.id_family AND
				g.id = sg.id_group AND
				g.id = $idGroup AND
				sg.title = '$subgroup'
			HAVING
				COUNT(*) = 0
		";
		mysql_query($sql) or die("//MySQL INSERT Error (subgroup)!". mysql_error());
		
		LogAdmActivity( array(
			"action"	=> "insert",
			"page"		=> "products/grupos/",
			"name"		=> $group,
			"value"		=> $subgroup,
			"idRef"		=> $idFamily
		));
		
		print PHP_EOL ."//group sucessfully added! (idFamily $idFamily)";
		return;
	}
	
	if ( isset($_GET["print"]) ) {
		AddGroup(array(
			"idFamily"	=> $_GET["idFamily"],
			"group"		=> $_GET["group"],
			"subgroup"	=> $_GET["subgroup"],
			"verbose"	=> $_GET["verbose"]
		));
	}
?>
