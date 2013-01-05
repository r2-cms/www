<?php
	/*
		UpdateUrl
			id:			Required url_history id
			field:		[a-zA-Z0-9_]+
			value:		all
			print
		Sample:
			UpdateUrl(array(
				"id"		=> $_GET["id"],
				"field"		=> $_GET["field"],
				"value"		=> $_GET["value"],
				"format"	=> $_GET["format"]
			));
	*/
	if ( !defined('CROOT')) {
		die('Undefined connection! (Q40->U45::U91a1e)');
	}
	require_once( SROOT ."engine/classes/Update.php");
	class UpdateUrl extends Update {
		public $name	= 'url_history';
		public $privilegeName	= 'redirects/';
		
		public function __construct($options) {
			$this->Update( $options);
		}
		public function getValue( $field, $value) {
			
			return $value;
		}
	}
?>