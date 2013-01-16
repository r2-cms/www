<?php
	if ( !defined('CROOT')) {
		die('Undefined GT8: a1i32->a114e00->e1i1o4->Home');
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/classes/CardLister.php");
	require_once( SROOT ."engine/queries/security/ScanDomain.php");
	
	class Index extends CardLister {
		public $name	= 'security-scanner/';
		public $tableType	= null;
		
		function __construct() {
			global $GT8;
			parent::CardLister();
			
			$this->checkActionRequest();
			$this->checkReadPrivileges();
			
			
			//teste
			$ScanDomain = new ScanDomain();
			$ScanDomain->getDomains(
				array(field=>array(id=>'id', name=>'name', creation=>)'creation')
			);
		}
		public function returnScanDomain(){
			//&action=new-domain&value=elefante.com.br
			$Pager	= Pager( array(
				'sql'		=> 'security.listDomains'
			));
			$this->data['domains'] = $Pager['rows'];
			return $this->data['domains'];
		}
		
		
		public function on404() {
			if ( !$this->id ) {
				parent::on404();
			}
		}
		private function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				switch($_GET['action']) {
					case 'name': {
						$this->update( 'name', $_GET['value']);
						break;
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
			//$this->jsVars[]	= array('contactsAPath', CROOT.$GT8['admin']['root'].$GT8['admin']['contacts']['root']);
			return parent::getServerJSVars();
		}
	}
?>