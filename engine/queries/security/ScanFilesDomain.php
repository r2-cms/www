<?php
	require_once( SROOT ."engine/queries/security/Security.php");
	require_once( SROOT ."engine/functions/Pager.php");
	require_once( SROOT ."engine/functions/validDateTime.php");
	
	class ScanFilesDomain extends Security implements IScanFilesDomain{
		private $idDomain = 0;
		private $idFileDomain = 0;
		private $currentDomain = "";
		private $args = array();
		public $privilegeName = 'security/scanner/';
		protected $sqlFull = "
			sf.id AS idFilesDomain, sf.id_scan_domain, sf.filename, sf.type, sf.status, sf.size, sf.version, sf.location, sf.function, sf.description,
			sf.creation AS creationFilesDomain, sf.modification AS modifFIleDomain, sd.id AS idDomain, sd.id_user, sd.ftp, sd.domain, sd.login, sd.pass,
			sd.port, sd.scan_frequency, sd.creation AS creationDomain, sd.modification AS modifDomain, u.name AS userName"
		;
		public $sttsDefault = 0; //Obs.: Sé esse valor for = 0, é necessário o quanto antes definí-lo com um valor >0 de acordo com o Banco	
		public function __construct(){
			$this->currentDomain = $_SERVER['SERVER_NAME'];
			$this->name = 'scan_files';
		}
		public function getFilesDomains($props = array()){
			$format = isset($props['format'])? strtoupper($props['format']): "OBJECT";
			$field = isset($props['fields']) && $props['fields']? $props['fields']: $this->sqlFull;
			$fields = null;
			$where = "";
			$limit = isset($props['limit'])? (integer)$props['limit']: 50;
			$index = isset($props['index'])? (integer)$props['index']: 0;
			$group = isset($props['group']) && $props['group']? RegExp($props['group'], '[a-zA-Z0-9_\-\.\s]+'): null;
			$template = isset($props['template']) && $props['template']? $props['template']: null;
			
			if(count(explode(",", $field))> 0){
				$field = explode(",", $field);
				if(substr($field, 0, 6) == "COUNT("){
					if(!isset($group) || !$group){
						die("//#error: COUNT necessita da cláusula GROUP definida!");
					}
				}
				for($i=0; $i<count($field); $i++){
					$fields .= RegExp($field[$i], '[a-zA-Z0-9_\-\.\s]+') . ",";
				}
				$fields = substr(rtrim($fields), -1) === ","? substr(rtrim($fields), 0, -1): $fields;
			}
			
			switch ($format){
				case 'OBJECT':
					$format = "OBJECT";
					break;
				case 'TABLE':
					$format = "TABLE";
					break;
				case 'CARD':
					$format = "CARD";
					break;
				case 'JSON':
					$format = "JSON";
					break;
				case 'GRID':
					$format = "GRID";
					break;
				case 'TEMPLATE':
					$format = "TEMPLATE";
					break;
				default:
					$format = "OBJECT";
			}
			
			if(isset($props['clauseWhere']) && $props['clauseWhere']){
				$where = trim(str_replace("WHERE", "AND", mysql_real_escape_string($props['clauseWhere']))) . PHP_EOL;
			}
			if(isset($props['clauseAnd']) && $props['clauseAnd']){
				$where .= mysql_real_escape_string($props['clauseAnd']) . PHP_EOL;
			}
			
			$Pager = Pager(array(
				'select' => $fields,
				'from' => '
					gt8_scan_files sf
						RIGHT JOIN
							gt8_scan_domains sd
								ON
									sd.id = sf.id_scan_domain
						INNER JOIN
							gt8_users u
								ON
									u.id = sd.id_user
				',
				'where' => $where,
				'index' => $index,
				'limit' => $limit,
				'group' => $group,
				'format' => $format,
				'template' => $template
			));
			return $Pager;
		}
		public function addFilesDomains($props = array()){
			$id = 0;
			$id_scan_domain = isset($props['id_scan_domain']) && $props['id_scan_domain']? (integer)$props['id_scan_domain']: 0;
			$filename = isset($props['filename']) && $props['filename']? RegExp($props['filename'], '[a-zA-Z0-9_\-\.\s]+'): null;
			$type = isset($props['type']) && $props['type']? RegExp($props['type'], '[a-zA-Z\.]+'): null;
			$status = isset($props['status']) && $props['status']? (integer)$props['status']: 0;
			$size = isset($props['size']) && $props['size']? (integer)$props['size']: 0;
			$version = isset($props['version']) && $props['version']? RegExp($props['version'], '[0-9\.]+'): 0;
			$location = isset($props['location']) && $props['location']? RegExp($props['location'], '[a-zA-Z0-9_\-\.\/]+'): null;
			$function = isset($props['function']) && $props['function']? mysql_real_escape_string($props['function']): null;
			$description = isset($props['description']) && $props['description']? mysql_real_escape_string($props['description']): null;
			$creation = isset($props['creation'])? $props['creation']: date('Y-m-d H:i:s');
			$modification = date('Y-m-d H:i:s');
			$sqlInsert = "";
			
			$sqlInsert = mysql_query("
				INSERT INTO
					gt8_scan_files(
						id_scan_domain,
						filename,
						type,
						status,
						size,
						version,
						location,
						function,
						description,
						creation,
						modification
					)VALUES(
						$id_scan_domain,
						'$filename',
						'$type',
						$status,
						$size,
						$version,
						'$location',
						'$function',
						'$description',
						'$creation',
						'$modification'
					)
			") or die($_SESSION['login']['level']>7? "//#error: SQL INSERT Error:". mysql_error() . PHP_EOL : '//#error: Erro ao acessar o banco de dados!'. PHP_EOL);
			$id = mysql_insert_id();
			
			if($id){
				print('//#affected rows: 1!'. PHP_EOL);
				return $id;
			}
		}
		public function updateFilesDomains($id = 0, $props = array()){
			require_once( SROOT .'engine/classes/Update.php');
			
			//Melhorias:
			//	Atualizar mais de um campo de uma vez
			
			$this->idFileDomain = (integer)$id;
			$field = isset($props['field'])? RegExp($props['field'], '[a-zA-Z_\-]+'): null;
			$value = isset($props['value'])? mysql_real_escape_string($props['value']): null;
			$format = in_array($props['format'], explode(',', 'OBJECT,TABLE,CARD,JSON,GRID'))? $props['format']: 'OBJECT';
			
			if(!isset($this->idFileDomain) || !$this->idFileDomain || $this->idFileDomain < 1){
				print('//#error: ID do arquivo é obrigatório!'. PHP_EOL);
				die();
			}
			if($field == "id_scan_domain"){
				if(!isset($value) || !$value || (integer)$value < 1){
					print('//#error: ID do domínio é obrigatório!'. PHP_EOL);
					die();
				}
			}
			if($status == "status"){
				if(!isset($value) || !$value || (integer)$value < 1){
					print('//#error: valor para o campo ' . strtoupper($field) . ' não definido!' . PHP_EOL);
					$value = $this->sttsDefault;
					print('Definido valor padrão que poderá ser alterado posteriormente!');
				}
			}
			if(isset($field) && $field && $field != null){
				if(is_int($value)){
					if($value < 1){
						print('//#error: valor incorreto para o campo ' . strtoupper($field) . '!');
					}
				}
				if(!isset($value) || !$value || $value == null){
					print('//#error: valor para o campo ' . strtoupper($field) . ' não definido!');
					die();
				}
			}else{
				print('//#error: É necessário informar o campo a ser atualizado!');
				die();
			}
			
			$this->args['id'] = $this->idFileDomain;
			$this->args['field'] = $field;
			$this->args['value'] = $value;
			$this->args['format'] = $format;
			$this->args['privilegeName'] = $this->privilegeName;
			$this->args['name'] = 'scan_files';
			$Update = new Update($this->args);
			
			if($Update){
				print("//#message: Registro alterado com sucesso!");
				die();
			}else{
				print("//#error: Erro ao alterar registro!");
				die();
			}
		}
		public function deleteFilesDomains($id = 0, $props = array()){
			$id = (integer)$id;
			if(!$id || $id==0){
				print('//#error: ID do arquivo é obrigatório!'. PHP_EOL);
				die();
			}
			$sqlDelete = "";
			$this->idFileDomain = $id;
			$this->args['format'] = "OBJECT";
			$this->args['field'] = "sf.id, sf.filename, sd.domain";
			$this->args['clauseWhere'] = "WHERE sf.id = " . $this->idFileDomain;
			$getFilesDomains = $this->getFilesDomains(
				array(
					field=>$this->args['field'],
					clauseWhere => $this->args['clauseWhere']
				)
			);
			for($i=0; $i<count($getFilesDomains['rows']); $i++){
				if($getFilesDomains['rows'][$i]['domain'] == $this->currentDomain){
					print('//#message: Atenção, você esta prestes a ecluir um arquivo do seu próprio domínio!');
					die();
				}	
			}
			$sqlDelete = "
				DELETE
					FROM
						gt8_scan_domains
					WHERE
						id = " . $this->idFileDomain . "
			";
			mysql_query($sqlDelete) or die("//#error: Erro ao excluir o domínio informado!");
			print("//#message: Domínio excluído com sucesso!");
			return;
		}
	}
?>