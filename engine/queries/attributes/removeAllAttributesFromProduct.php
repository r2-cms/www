<?php
	require_once( $_SERVER["DOCUMENT_ROOT"] ."/jsAdmin/check.php");
	
	if ( CheckPrivileges( null, null, 'produtos/') < 2) {
		include($GT8['root'] .'jsAdmin/login/forbidden.php');
		die();
	}
	/*
		removeAllAttributesFromProduct
			idProduct:		Required product id
			format:			OBJECT|JSON
			print
	*/
	function removeAllAttributesFromProduct( $prop) {
		$idProduct		= (integer)($prop["idProduct"]);
		$format			= isset($prop["format"])? $prop["format"]: "OBJECT";
		
		if ( !$idProduct) {
			if ( $format == 'JSON') {
				print( PHP_EOL ."//product id missing!". PHP_EOL);
			}
			return false;
		}
		
		$sql	= "
			DELETE FROM
				gt8_products_features
			WHERE
				id_product	= $idProduct
		";
		mysql_query( $sql);
		
		if ( $format == "JSON") {
			print(PHP_EOL ."//#affected rows: ". mysql_affected_rows() . PHP_EOL);
		}
		
		LogAdmActivity( array(
			"action"	=> "delete",
			"page"		=> "products/attributes/",
			"name"		=> 'all',
			"value"		=> '',
			"idRef"		=> $idProduct
		));
		
		return true;
	}
	
	if ( isset($_GET["print"]) ) {
		$_GET["print"]	= NULL;
		print (removeAllAttributesFromProduct(array(
			"idProduct"		=> $_GET["idProduct"],
			"format"		=> $_GET["format"]
		)));
	}
?>