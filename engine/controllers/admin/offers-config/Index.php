<?php
	if ( !defined('CROOT')) {
		die('Undefined GT8: a1i32->a114e00->e1i1o4->Home');
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/functions/CheckPrivileges.php");
	require_once( SROOT ."engine/classes/Editor.php");
	
	class Index extends Editor {
		public $name	= 'home-offers';
		public $tableType	= null;
		
		function __construct() {
			global $GT8;
			parent::Editor();
			
			//$Pager	= Pager(array(
			//	'sql'		=> 'orders.list-orders',
			//	'addSelect'	=> ', t.type, o.a_stt, o.a_city, o.a_district, o.a_street, o.a_number, o.a_zip, o.id_analytics, o.creation AS creation2',
			//	'addFrom'	=> '
			//		INNER JOIN gt8_address_type t	ON t.id = o.a_id_type
			//	',
			//	'ids'		=> array(
			//		array('o.id', $id)
			//	)
			//));
			//$this->Pager	= $Pager;
			//$this->data	= $Pager['rows'][0];
			//$this->id	= $id;
			$this->setFields();
			
			CheckPrivileges( 0, 'OBJECT','home-offers/', 1);
			
			$this->checkActionRequest();
			
		}
		public function on404() {
			if ( !$this->id ) {
				parent::on404();
			}
		}
		private function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				switch($_GET['action']) {
					case '@set-pass': {
						$this->update( 'pass', $_POST['pass']);
					}
				}
			}
		}
		public function update( $field, $value) {
			require_once(SROOT.'engine/queries/users/UpdateUsers.php');
			new UpdateUsers(array(
				"id"		=> $this->id,
				"field"		=> $field,
				"value"		=> $value,
				'format'	=> 'JSON'
			));
			die();
		}
		public function getServerJSVars() {
			global $GT8;
			//$this->jsVars[]	= array('contactsAPath', CROOT.$GT8['admin']['root'].$GT8['admin']['contacts']['root']);
			
			return parent::getServerJSVars();
		}
		private function setFields() {
			global $GT8;
			
		}
	}
?>