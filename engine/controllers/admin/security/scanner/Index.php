<?php
	if ( !defined('CROOT')) {
		die('Undefined GT8: a1i32->a114e00->e1i1o4->Home');
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/classes/CardLister.php");
	require_once( SROOT ."engine/queries/security/ScanFilesDomain.php");
	
	class Index extends ScanFilesDomain{
		public $name	= 'security-scanner/';
		public $tableType	= null;
		protected $fields = null;
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
		protected $idFileDomain = 0;
		protected $filename = null;
		protected $type = null;
		protected $status = 0;
		protected $size = 0;
		protected $version = 0;
		protected $location = null;
		protected $functionality = null;
		protected $description = null;
		protected $creation = null;
		protected $modification = null;
		
		function __construct() {
			parent::__construct();
			global $GT8;
			
			$this->checkActionRequest();
			$this->checkReadPrivileges();
			$this->fields = isset($_GET['fields']) && $_GET['fields']? $_GET['fields']: null;
			$this->format = isset($_GET['format']) && ($_GET['format'])? $_GET['format']: 'TEMPLATE';
			$this->where = isset($_GET['where'])? mysql_real_escape_string($_GET['where']): "";
			$this->clauseAnd = isset($_GET['and'])? mysql_real_escape_string($_GET['and']): "";
			$this->limit = isset($_GET['limit'])? (integer)($_GET['limit']): 50;
			$this->index = isset($_GET['index'])? (integer)($_GET['index']): 0;
			$this->group = isset($_GET['group'])? mysql_real_escape_string($_GET['group']): "";
			
			/*ScanDomain*/
			$this->idDomain = isset($_GET['idDomain'])? (integer)$_GET['idDomain']: 0;
			$this->id_user = $_GET['id_user'];
			$this->ftp = $_GET['ftp'];
			$this->domain = $_GET['domain'];
			$this->login = $_GET['login'];
			$this->pass = $_GET['pass'];
			$this->port = $_GET['port'];
			$this->scan_frequency = $_GET['scan_frequency'];
			
			/*ScanFilesDomain*/
			$this->idFileDomain = $_GET['idFileDomain'];
			$this->filename = $_GET['filename'];
			$this->type = $_GET['type'];
			$this->status = $_GET['status'];
			$this->size = $_GET['size'];
			$this->version = $_GET['version'];
			$this->location = $_GET['location'];
			$this->functionality = $_GET['functionality'];
			$this->description = $_GET['description'];
			
			/*Common variables*/
			$this->creation = date('Y-m-d H:i:s');
			$this->modification = date('Y-m-d H:i:s');
		}
		public function getScanDomains($template){
			//&action=new-domain&value=elefante.com.br
			
			$getDomains = $this->getDomains(
				array(
					fields=>$this->fields,
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
			$this->id_domain = $this->idDomain;
			$this->args['field'] = $this->fields;
			$this->args['value'] = $this->values;
			$this->args['format'] = $this->format;
			$this->updateDomains($this->idDomain, $this->args);
		}
		public function deleteScanDomains(){
			$this->deleteDomains($this->idDomain, array());
		}
		public function getScanFilesDomains($template){
			$getFilesDomains = $this->getFilesDomains(
				array(
					fields=>$this->fields,
					clauseWhere=>$this->where,
					clauseAnd=>$this->clauseAnd,
					limit=>$this->limit,
					index=>$this->index,
					group=>$this->group,
					format=>$this->format,
					template=>$template
				)
			);
			$this->data['filesDomain'] = $getFilesDomains['rows'];
			return $this->data['filesDomain'];
		}
		public function addScanFilesDomain(){
			print(
				$this->addFilesDomains(
					array(
						id_scan_domain=>$this->idDomain,
						filename=>$this->filename,
						type=>$this->type,
						status=>$this->status,
						size=>$this->size,
						version=>$this->version,
						location=>$this->location,
						functionality=>$this->functionality,
						description=>$this->description,
						creation=>$this->creation,
						modification=>$this->modification
					)
				)
			);
			die();
		}
		public function updateScanFilesDomain(){
			$this->id_domain = $this->idDomain;
			$this->args['field'] = $this->fields;
			$this->args['value'] = $this->values;
			$this->args['format'] = $this->format;
			$this->updateDomains($this->id_domain, $this->args);
		}
		public function deleteScanFilesDomain(){
			$this->deleteFilesDomains($this->idFileDomain, array());
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