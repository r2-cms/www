<?php
	require_once( SROOT .'engine/classes/GT8.php');
	
	require_once( SROOT .'engine/controllers/Index.php');
	class Search extends Index {
		public function __construct( ) {
			parent::__construct();
		}
	}
?>