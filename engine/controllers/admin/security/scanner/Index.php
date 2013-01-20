<?php
	if ( !defined('CROOT')) {
		die('Undefined GT8: a1i32->a114e00->e1i1o4->Home');
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/classes/CardLister.php");
	require_once( SROOT ."engine/queries/security/ScanDomain.php");
	
	class Index extends ScanDomain {
		public $name	= 'security-scanner/';
		public $tableType	= null;
		private $fieldsDefault = array("id", "id_user", "ftp", "domain", "login", "pass", "port", "scan_frequency", "creation", "modification");
		protected $fields = array();
		protected $format = "";
		protected $where = "";
		protected $clauseAnd = "";
		protected $limit = "";
		protected $index = "";
		protected $group = "";
		
		function __construct() {
			parent::__construct();
			global $GT8;
			
			$this->checkActionRequest();
			$this->checkReadPrivileges();
			$fieldsArgs = array();
			$fieldsArgs[] = isset($_GET['fields']) & $_GET['fields']!=null & $_GET['fields']!=""? count(explode(",", $_GET['fields']))>0? explode(",", $_GET['fields']): "": "";
			$fieldsDiff = array_diff($fieldsArgs, $this->fieldsDefault);
			
			foreach($fieldsDiff as $key=>$value){
				for($i=0; $i<count($fieldsArgs); $i++){
					unset($fieldsArgs[$key]);
				}
			}
			$fieldsArgs = array_values($fieldsArgs);
			
			for($i=0; $i<count($this->fieldsDefault); $i++){
				for($j=0; $j<count($fieldsArgs); $j++){	
					if($this->fieldsDefault[$i] === $fieldsArgs[$j]){
						$this->fields[$this->fieldsDefault[$i]] = $fieldsArgs[$j];
					}
				}
			}
			
			$this->format = isset($_GET['format'])? mysql_real_escape_string($_GET['format']): "object";
			$this->where = isset($_GET['where'])? mysql_real_escape_string($_GET['where']): "";
			$this->clauseAnd = isset($_GET['and'])? mysql_real_escape_string($_GET['and']): "";
			$this->limit = isset($_GET['limit'])? (integer)($_GET['limit']): 50;
			$this->index = isset($_GET['index'])? (integer)($_GET['index']): 0;
			$this->group = isset($_GET['group'])? mysql_real_escape_string($_GET['group']): "";
			//$this->returnScanDomain();
			$this->addScanDomain();
		}
		
		public function returnScanDomain(){
			//&action=new-domain&value=elefante.com.br
			$getDomains = $this->getDomains(
				array(
					field=>$this->fields,
					clauseWhere=>$this->where,
					clauseAnd=>$this->clauseAnd,
					limit=>$this->limit,
					index=>$this->index,
					group=>$this->group,
					format=>$this->format
				)
			);
			
			$this->data['domains'] = $getDomains['rows'];
			print_r($this->data['domains']);
			die('admin/security/scanner/Index.php');
			return $this->data['domains'];
		}
		
		public function addScanDomain(){
			print(
				$this->addDomains(
					array(creation=>"2013/01/20")
				)	
			);
			die();
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