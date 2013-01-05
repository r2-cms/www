<?php
	/*
		updateAddress
			id:			Required address id
			field:		[a-zA-Z0-9_]+
			value:		all
			print
		Sample:
			updateAddress(array(
				"id"		=> $_GET["id"],
				"field"		=> $_GET["field"],
				"value"		=> $_GET["value"],
				"format"	=> $_GET["format"]
			));
	*/
	if ( !defined('CROOT')) {
		die('Undefined connection! (Q40->A114e00::U91a1e)');
	}
	require_once( SROOT ."engine/classes/Update.php");
	class UpdateAddress extends Update {
		public $name	= 'address';
		public $privilegeName	= 'address/';
		
		public function UpdateAddress($options) {
			$this->Update( $options);
		}
		public function getValue( $field, $value) {
			if ( $field == 'stt') {
				$value	= strtoupper(RegExp($value, '[A-Za-z]{2}'));
			}
			return $value;
		}
	}
?>