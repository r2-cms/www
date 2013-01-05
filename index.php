<?php
	require_once( 'engine/connect.php');
	require_once( SROOT .'engine/classes/GT8.php');
	
	require_once( SROOT.'engine/controllers/Index.php');
	$Index	= new Index();
	$Index->printView(
		SROOT .'engine/views/index.inc',
		$Index->data,
		null,
		$Index
	);
?>