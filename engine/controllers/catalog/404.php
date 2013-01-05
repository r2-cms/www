<?php
	require_once( SROOT .'engine/controllers/catalog/Product.php');
	$Product	= new Product();
	GT8::printView(
		SROOT .'engine/views/catalog/product.inc',
		$Product->row
	);
	die();
?>