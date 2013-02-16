<?php
	/**
	 * @name: ScanFilesDomain
	 * @author: Robson Cândido
	 * @version: 1.0
	 * @package: Esta classe faz parte do sistema R2-CMS
	 * @method: getFilesDomains()
	 * 		@Paramss:
	 * 			- $props['field']
	 * 				Nesta chave os campos devem ser passados separados por vírgula;
	 * 				Como essa query faz um JOIN tamnto com a tabela gt8_scan_domains quanto com a gt8_users é necessário utilizar os alias:
	 * 					gt8_scan_files = sf;
	 * 					gt8_scan_domains = sd;
	 * 					gt8_users = u;
	 * 				Caso o parâmetro $props['field'] venha indefinido a propriedade $sqlFull será passada como padrão;
	 *
	 * 	@method: updateFilesDomains()
	 * 		@params:
	 * 			- $id (id do arquivo à ser atualizado)
	 * 			- $field (campo a ser atualizado)
	 * 			- $value (valor de field. Valor do campo a ser atualizado)
	 * 			- $format (formato de retorno [OBJECT,TABLE,CARD,JSON,GRID,TEMPLATE])
    **/

	require_once( SROOT ."engine/queries/security/ScanDomain.php");
	require_once( SROOT ."engine/functions/Pager.php");
	require_once( SROOT ."engine/functions/validDateTime.php");
	
	class ScanFilesDomain extends ScanDomain implements IScanFilesDomain{
		private $id_domain = 0;
		private $id_file_domain = 0;
		private $currentDomain = "";
		private $args = array();
		public $privilegeName = 'security/scanner/';
		private $sqlFull = "
			sf.id AS idFilesDomain, sf.id_scan_domain, sf.filename, sf.type, sf.status, sf.size, sf.version, sf.location, sf.functionality, sf.description,
			sf.creation AS creationFilesDomain, sf.modification AS modifFIleDomain, sd.id AS idDomain, sd.id_user, sd.ftp, sd.domain, sd.login, sd.pass,
			sd.port, sd.scan_frequency, sd.creation AS creationDomain, sd.modification AS modifDomain, u.name AS userName"
		;
		public $sttsDefault = 0; //Obs.: Sé esse valor for = 0, é necessário o quanto antes definí-lo com um valor >0 de acordo com o Banco
		
		public function __construct(){
			$this->currentDomain = $_SERVER['SERVER_NAME'];
		}
		public function getFilesDomains($props = array()){
			$format = in_array($props['format'], explode(',', 'OBJECT,TABLE,CARD,JSON,GRID'))? $props['format']: 'OBJECT';
			$field = isset($props['fields']) && $props['fields']? $props['fields']: $this->sqlFull;
			$fields = null;
			$where = "";
			$limit = isset($props['limit'])? (integer)$props['limit']: 50;
			$index = isset($props['index'])? (integer)$props['index']: 0;
			$group = isset($props['group']) && $props['group']? RegExp($props['group'], '[a-zA-Z0-9_\-\.\s]+'): null;
			$template = isset($props['template']) && $props['template']? $props['template']: null;
			
			if(count(explode(",", $field))> 0){
				$field = explode(",", $field);
				$position = 0;
				$length = 0;
				$Field = null;
				$table = null;
				$alias = null;
				
				for($i=0; $i<count($field); $i++){
					$field[$i] = ltrim($field[$i]);
					
					if(substr($field[$i], 0, 6) == "COUNT("){
						if(!isset($group) || !$group){
							die("//#error: COUNT necessita da cláusula GROUP definida!");
						}
						if(substr($field[$i], 6, 3) == "sf."){
							$table = "scan_files";
							$alias = "sf";
						}elseif(substr($field[$i], 6, 3) == "sd."){
							$table = "scan_domains";
							$alias = "sd";
						}elseif(substr($field[$i], 6, 2) == "u."){
							$table = 'users';
							$alias = "u";
						}else{
							$table = "scan_files";
							$alias = "sf";
						}
					}
					
					if(substr($field[$i], 0, 3) == "sf."){
						$table = "scan_files";
						$alias = "sf";
					}
					if(substr($field[$i], 0, 3) == "sd."){
						$table = "scan_domains";
						$alias = "sd";
					}
					if(substr($field[$i], 0, 2) == "u."){
						$table = "users";
						$alias = "u";
					}
					if($this->validField($field[$i], $table, $alias)){
						$fields .= $field[$i] . ",";
					}else{
						print("<br />" . __FUNCTION__ . PHP_EOL . 'say: Stop Debug!');
						die();
					}
				}
				$fields = substr(rtrim($fields), -1) === ","? substr(rtrim($fields), 0, -1): $fields;
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
			$functionality = isset($props['functionality']) && $props['functionality']? mysql_real_escape_string($props['functionality']): null;
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
						functionality,
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
						'$functionality',
						'$description',
						'$creation',
						'$modification'
					)
			") or die($_SESSION['login']['level']>7? "//#error: SQL INSERT Error:". mysql_error() . PHP_EOL : '//#error: Erro ao acessar o banco de dados!'. PHP_EOL);
			$id = mysql_insert_id();
			
			if($id){
				print('//#affected rows: '. mysql_affected_rows() . PHP_EOL);
				return $id;
			}
		}
		public function updateFilesDomains($id = 0, $props = array()){
			require_once( SROOT .'engine/classes/Update.php');
			
			//Melhorias:
			//	Atualizar mais de um campo de uma vez
			
			$this->id_file_domain = (integer)$id;
			$field = isset($props['field'])? RegExp($props['field'], '[a-zA-Z_\-]+'): null;
			$value = isset($props['value'])? mysql_real_escape_string($props['value']): null;
			$format = in_array($props['format'], explode(',', 'OBJECT,TABLE,CARD,JSON,GRID'))? $props['format']: 'OBJECT';
			
			if(!isset($this->id_file_domain) || !$this->id_file_domain || $this->id_file_domain < 1){
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
			
			$this->args['id'] = $this->id_file_domain;
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
			$format = in_array($props['format'], explode(',', 'OBJECT,TABLE,CARD,JSON,GRID'))? $props['format']: 'OBJECT';
			
			if(!$id || $id==0){
				print('//#error: ID do arquivo é obrigatório!'. PHP_EOL);
				die();
			}
			$sqlDelete = "";
			$this->id_file_domain = $id;
			$this->args['format'] = $format;
			$this->args['field'] = "sf.id, sf.filename, sd.domain";
			$this->args['clauseWhere'] = "WHERE sf.id = " . $this->id_file_domain;
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
						gt8_scan_files
					WHERE
						id = " . $this->id_file_domain . "
			";
			mysql_query($sqlDelete) or die("//#error: Erro ao excluir o arquivo informado!");
			print("//#message: Arquivo excluído com sucesso!");
			return true;
		}
	}
?>