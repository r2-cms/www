<?php
	/*
		updateAttributeFromProduct
			idProduct:		Required product id
			idAttribute:	Required attribute id
			feature:		String
			format:			OBJECT|JSON
			execute
			print
		Sample:
			updateAttributeFromProduct(array(
				"idProduct"		=> $_GET["idProduct"],
				"idAttribute"	=> $_GET["idAttribute"],
				"feature"		=> $_GET["feature"],
				"format"		=> $_GET["format"]
			))
	*/
	if ( !defined('CROOT')) {
		die('Undefined connection! (Q40->A114i9u1e0::U91da1e)');
	}
	require_once( SROOT . $GT8['admin']['root'] ."check.php");
	function updateAttributeFromProduct( $prop) {
		CheckPrivileges( '', '', 'produtos/', 2);
		
		$idProduct		= (integer)($prop["idProduct"]);
		$idAttribute	= (integer)$prop["idAttribute"];
		$feature		= isset($prop["feature"])? mysql_real_escape_string(($prop["feature"])): "";
		$format			= isset($prop["format"])? $prop["format"]: "OBJECT";
		
		if ( !$idProduct) {
			if ( $format) {
				print( PHP_EOL ."//product id missing!". PHP_EOL);
			}
			return;
		}
		
		if ( !$idAttribute) {
			if ( $format) {
				print( PHP_EOL ."//attribute id missing!". PHP_EOL);
			}
			return;
		}
		
		//first, check if feature already exists. If not, create it!
		$sql	= "
			INSERT INTO
				gt8_products_features(id_product, id_attribute, feature)
				
				SELECT
					$idProduct, $idAttribute, ''
				FROM
					gt8_products_features
				WHERE
					1 = 1
					AND id_product		=  $idProduct
					AND id_attribuct	= $idAttribute
				HAVING
					COUNT(*) = 0
		";
		mysql_query($sql);
		
		$sql	= "
			UPDATE
				gt8_products_features
			SET
				feature	= '$feature'
			WHERE
				1 = 1
				AND id_product	= $idProduct
				AND id_attribute = $idAttribute
		";
		mysql_query( $sql);
		
		if ( $format == "JSON") {
			print(PHP_EOL ."//#affected rows: ". mysql_affected_rows() . PHP_EOL);
		}
		
		//get attr name
		$row		= mysql_fetch_array(mysql_query("SELECT attribute FROM gt8_products_attributes WHERE id = $idAttribute"));
		$attribute	= $row[0];
		
		LogAdmActivity( array(
			"action"	=> "update",
			"page"		=> "products/attributes/",
			"name"		=> $attribute,
			"value"		=> $feature,
			"idRef"		=> $idProduct
		));
		
		return true;
	}
?>