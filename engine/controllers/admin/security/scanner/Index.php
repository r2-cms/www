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
		private $fieldsDefault = array(
			"sd.id AS idDomain", "sd.id_user", "sd.ftp", "sd.domain", "sd.login", "sd.pass", "sd.port", "sd.scan_frequency", "sd.creation", "sd.modification",
			"u.id AS idUser", "u.natureza", "u.name", "u.cpfcnpj", "u.document", "u.genre", "u.birth", "u.login", "u.hlogin", "u.pass", "u.LEVEL", "u.enabled",
			"u.approval_level_required", "u.creation", "u.modification", "u.last_access", "u.access_counter", "u.agent", "u.SIGN", "u.remarks",
			"others"
		);	//eg: COUNT(modification) AS modif
		protected $fields = array();
		protected $values = array();
		private $idDomain = 0;
		private $args = array();
		protected $format = "";
		protected $where = "";
		protected $clauseAnd = "";
		protected $limit = "";
		protected $index = "";
		protected $group = "";
		protected $id_user = 0;
		protected $ftp = null;
		protected $domain = null;
		protected $login = null;
		protected $pass = null;
		protected $port = 0;
		protected $scan_frequency = 0;
		protected $creation = 'NOW()';
		protected $modification = 'NOW()';
		
		function __construct() {
			parent::__construct();
			global $GT8;
			
			$this->format = isset($_GET['format']) && ($_GET['format'])? $_GET['format']: 'OBJECT';
			$this->checkActionRequest();
			$this->checkReadPrivileges();
			$fieldsArgs = array();
			$fieldsArgs[] = isset($_GET['fields']) && $_GET['fields']!=null && $_GET['fields']!=""? count(explode(",", $_GET['fields']))>0? explode(",", $_GET['fields']): "": "";
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
			$this->idDomain = isset($_GET['idDomain'])? (integer)$_GET['idDomain']: 0;
			
			//$this->getScanDomain();
			//$this->addScanDomain();
			//$this->updateScanDomain();
			//$this->deleteScanDomains();
			
		}
		
		public function getScanDomains($template){
			//&action=new-domain&value=elefante.com.br
			$this->format = 'TEMPLATE';
			$getDomains = $this->getDomains(
				array(
					field=>$this->fields,
					clauseWhere=>$this->where,
					clauseAnd=>$this->clauseAnd,
					limit=>$this->limit,
					index=>$this->index,
					group=>$this->group,
					format=>$this->format,
					template=>$template
				)
			);
			
			$this->data['domains'] = $getDomains['rows'];
			return $this->data['domains'];
		}
		
		public function addScanDomain(){
			print(
				$this->addDomains(
					array(
						id_user=>$this->id_user,
						ftp=>$this->ftp,
						domain=>$this->domain,
						login=>$this->login,
						pass=>$this->pass,
						port=>$this->port,
						scan_frequency=>$this->scan_frequency,
						creation=>$this->creation,
						modification=>$this->modification
					)
				)
			);
			die();
		}
		public function updateScanDomain(){
			$this->idDomain = $this->idDomain;
			$this->args['field'] = $this->fields;
			$this->args['value'] = $this->values;
			$this->args['format'] = $this->format;
			$this->updateDomains($this->idDomain, $this->args);
		}
		public function deleteScanDomains(){
			$this->deleteDomains($this->idDomain, array());
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