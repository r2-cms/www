<?php
	/*
		updateProduct
			idProduct:		Required product id
			field:			[a-zA-Z0-9_]+
			value:			all
			print
		Sample:
			updateProduct(array(
				"id"		=> $_GET["id"],
				"field"		=> $_GET["field"],
				"value"		=> $_GET["value"],
				"format"	=> $_GET["format"]
			));
	*/
	if ( !defined('CROOT')) {
		die('Undefined connection! (Q40->P4o1u710::U91a1e)');
	}
	require_once( SROOT . $GT8['admin']['root'] ."check.php");
	function updateProduct( $prop) {
		$id		= (integer)($prop["id"]);
		$field	= RegExp($prop["field"], "[a-zA-Z0-9_]+");
		$value	= $prop["value"];
		$format	= $prop["format"];
		
		if ( !$id) {
			print( PHP_EOL ."//product id missing!". PHP_EOL);
			return;
		}
		
		$prv	= CheckPrivileges($field, '', "products/");
		if ( $prv == -404 || $prv < 2) {
			CheckPrivileges('', '', "products/", 2);
		}
		
		//some specific fields
		if ( $field == 'code' ) {
			$value	= trim(mysql_real_escape_string($value));
			$result	= mysql_query("SELECT code FROM gt8_products WHERE code = '$value' LIMIT 1") or die("Error");
			if ( $row=mysql_fetch_array($result)) {
				if ( $format == "JSON") {
					print(PHP_EOL ."//#duplicated code!". PHP_EOL);
				}
				return false;
			}
		} else if ( $field == "url" || $field == 'canonical') {
			$field	= 'canonical';
			$value	= trim( str_replace(str_split('"?&%$#@!<>/\\|][{}* "\''), '-', $value));
		}
		
		
		//type
		$result	= mysql_query('
			DESCRIBE gt8_products
		') or die();
		$Field	= array();
		while( $row = mysql_fetch_assoc($result)) {
			$Field[]	= $row;
		}
		for ($i=0; $i<count($Field); $i++) {
			if ( $Field[$i]['Field'] == $field ) {
				$Field	= $Field[$i];
				break;
			}
		}
		
		if ( strpos(strtolower($Field['Type']), 'int(') !== false) {
			$value	= (integer)$value;
		} else if ( strpos(strtolower($Field['Type']), 'float') !== false) {
			$value	= (float)(str_replace(",", ".", $value));
		} else if ( strpos(strtolower($Field['Type']), 'double') !== false) {
			$value	= (float)(str_replace(",", ".", $value));
		} else if ( strpos(strtolower($Field['Type']), 'char(') !== false ) {
			$value	= mysql_real_escape_string($value);
		} else if ( strpos(strtolower($Field['Type']), 'blob') !== false ) {
			$value	= mysql_real_escape_string($value);
		} else if ( strpos(strtolower($Field['Type']), 'text') !== false ) {
			$value	= mysql_real_escape_string($value);
		}
		
		$sql	= "
			UPDATE
				gt8_products
			SET
				$field = '$value'
			WHERE
				id = $id
			LIMIT
				1
		";
		$result	= mysql_query( $sql) or die("SQL UPDATE produtos Error (1)");
		
		if ( $format == "JSON") {
			print(PHP_EOL ."//#affected rows: ". mysql_affected_rows() . PHP_EOL);
			print(PHP_EOL ."//#[[VALUE]]". $value .'[[END]]');
		}
		
		LogAdmActivity( array(
			"action"	=> "update",
			"page"		=> "products/",
			"name"		=> $field,
			"value"		=> $value,
			"idRef"		=> $id
		));
		return true;
	}
?>