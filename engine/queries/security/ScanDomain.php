<?php
    /**
     * @author: Robson Cândido
     * @version: 1.0
     * @package: Esta classe faz parte do sistema R2-CMS
     * @method: getDomains()
     * 		Params: $props['field']
     * 			Nesta chave os campos devem ser passados por vírgula;
     * 			Como essa query faz um JOIN entre as tabelas gt8_scan_domains e gt8_users é necessário utilizar os prefixos:
     * 				sd.campo para a tabela gt8_scan_domains e u.campo para a tabela gt8_users;
     * 				Caso o parâmetro $props['field'] venha indefinido a propriedade $sqlFull será passada como padrão
    **/

	require_once( SROOT ."engine/queries/security/Security.php");
	require_once( SROOT ."engine/functions/Pager.php");
	require_once( SROOT ."engine/functions/validDateTime.php");
	
	class ScanDomain extends Security implements IScanDomain{
		private $idDomain = 0;
		private $args = array();
		private $currentDomain = "";
		public $privilegeName	= 'security/scanner/';
		protected $sqlFull = "
			sd.id AS idDomain, sd.id_user, sd.ftp, sd.domain, sd.login AS domainLogin, sd.pass AS domainPass, sd.port, sd.scan_frequency,
			sd.creation AS domainCreation, sd.modification AS domainModification,
			u.id AS idUser, u.natureza, u.name, u.cpfcnpj, u.document, u.genre, u.birth, u.login AS userLogin, u.hlogin AS userhLogin,
			u.pass AS userPass, u.level, u.enabled, u.approval_level_required, u.creation AS userCreation, u.modification AS userModification,
			u.last_access, u.access_counter, u.agent, u.sign, u.remarks
		";
		
		//Params para ajax:
		//#message: Campo atualizado com sucesso!
		//#affected rows: 1
		//#affected
		//#error:
		//#message:
		
		function __construct(){
			$this->currentDomain = $_SERVER['SERVER_NAME'];
			$this->name = 'scan_domains';
		}
		public function getDomains($props = array()){
			$format = isset($props['format'])? strtoupper($props['format']): "OBJECT";
			$field = isset($props['fields']) && $props['fields']? $props['fields']: $this->sqlFull;
			$fields = null;
			$where = "";
			$limit = isset($props['limit'])? (integer)$props['limit']: 50;
			$index = isset($props['index'])? (integer)$props['index']: 0;
			$group = isset($props['group'])? mysql_real_escape_string($props['group']): null;
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
					
					//$this->validField($field, $table, $alias);
					
					if(substr($field[$i], 0, 6) == "COUNT("){
						if(!isset($group) || !$group){
							die("//#error: COUNT necessita da cláusula GROUP definida!");
						}
						if(substr($field[$i], 6, 3) == "sd."){
							$this->name = 'scan_domains';
							$position = 9;
							$length = strpos(substr($field[$i], $position), ")");
						}elseif(substr($field[$i], 6, 2) == "u."){
							$this->name = 'users';
							$position = 8;
							$length = strpos(substr($field[$i], $position), ")");
						}else{
							$position = 6;
							$length = strpos(substr($field[$i], $position), ")");
						}
					}
					
					if(substr($field[$i], 0, 3) == "sd."){
						$this->name = 'scan_domains';
						$position = 3;
						
						if(strpos(substr($field[$i], $position), " ")){
							$length = strpos(substr($field[$i], $position), " ");
						}elseif(strpos(substr($field[$i], $position), ",")){
							$length = strpos(substr($field[$i], $position), ",");
						}else{
							$length = strpos(substr($field[$i], $position), substr($field[$i], -1)) + 1;
						}
					}
					if(substr($field[$i], 0, 2) == "u."){
						$this->name = 'users';
						$position = 2;
						if(strpos(substr($field[$i], $position), " ")){
							$length = strpos(substr($field[$i], $position), " ");
						}elseif(strpos(substr($field[$i], $position), ",")){
							$length = strpos(substr($field[$i], $position), ",");
						}else{
							$length = strpos(substr($field[$i], $position), substr($field[$i], -1)) + 1;
						}
					}
					if(!$this->getType(RegExp(substr($field[$i], $position, $length),  '[a-zA-Z0-9_\-\.\s]+'))){
						print("//#error: Campo " . strtoupper(substr($field[$i], $position, $length)) . " não encontrado em " . $this->name .  "!");
						die();
					}
					$fields .= $field[$i] . ",";
				}
				$fields = substr(rtrim($fields), -1) === ","? substr(rtrim($fields), 0, -1): $fields;
			}
			die(PHP_EOL . 'Stop Debug!');
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
			
			if(isset($props['clauseWhere'])){
				$where = trim(str_replace("WHERE", "AND", mysql_real_escape_string($props['clauseWhere']))) . PHP_EOL;
			}
			if(isset($props['clauseAnd'])){
				$where .= mysql_real_escape_string($props['clauseAnd']) . PHP_EOL;
			}
			
			$Pager = Pager(array(
				'select' => $fields,
				'from' => '
					gt8_scan_domains sd
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
		public function addDomains($props = array()){
			$id_user = isset($props['id_user'])? (integer)$props['id_user']: 0;
			$ftp = isset($props['ftp'])? RegExp($props['ftp'], '[a-zA-Z_\-\.]+'): null;
			$domain = isset($props['domain'])? RegExp($props['domain'], '[a-zA-Z_\-\.]+'): null;
			$login = isset($props['login'])? mysql_real_escape_string($props['login']): null;
			$pass = isset($props['pass'])? mysql_real_escape_string($props['pass']): null;
			$port = isset($props['port'])? (integer)$props['port']: 0;
			$scan_frequency = isset($props['scan_frequency'])? (integer)$props['scan_frequency']: 0;
			$creation = isset($props['creation'])? validDateTime("datetime", $props['creation'])? $props['creation']: date("Y-m-d H:i:s"): date("Y-m-d H:i:s");
			$modification = date("Y-m-d H:i:s");
			$sqlInsert = "";
			
			$sqlInsert = mysql_query("
				INSERT INTO
					gt8_scan_domains(
						id_user,
						ftp,
						domain,
						login,
						pass,
						port,
						scan_frequency,
						creation,
						modification
					)VALUES(
						$id_user,
						'$ftp',
						'$domain',
						'$login',
						'$pass',
						$port,
						$scan_frequency,
						'$creation',
						'$modification'
					)
			") or die($_SESSION['login']['level']>7? "//#error: SQL INSERT Error:". mysql_error() . PHP_EOL : '//#error: Erro ao acessar o banco de dados!'. PHP_EOL);
			$this->id = mysql_insert_id();
			
			if($this->id){
				print('//#affected rows: 1!'. PHP_EOL);
				return $this->id;
			}
		}
		public function updateDomains($id = 0, $props = array()){
			require_once( SROOT .'engine/classes/Update.php');
			
			$this->idDomain = (integer)$id;
			$field = isset($props['field'])? RegExp($props['field'], '[a-zA-Z_\-]+'): null;
			$value = isset($props['value'])? mysql_real_escape_string($props['value']): null;
			$FieldValue = isset($props['FieldValue'])? $props['FieldValue']: null;
			$format = 'OBJECT';
			
			if(!$this->idDomain || $this->idDomain==0){
				print('//#error: ID do domínio é obrigatório!'. PHP_EOL);
				die();
			}
			if($field === "id_user"){
				$value = (integer)$value;
				if(!$value || $value<1){
					print('//#error: valor para o campo ' . $field . ' não definido!');
					die();
				}
			}
			if($field === "ftp"){
				if(!$value){
					print("//#error: valor para o campo ' . $field . ' não definido!");
					die();
				}
				$value = RegExp($value, '[a-zA-Z@\-\.\_0-9]+');
			}
			if($field === "domain"){
				print("//#error: Esse campo não pode ser alterado!");
				die();
			}
			if($field === "login"){
				print("//#error: Esse campo não pode ser alterado!");
				die();
			}
			if($field === "pass"){
				if(!$value){
					print("//#error: valor para o campo ' . $field . ' não definido!");
					die();
				}
			}
			if($field === "port"){
				$value = (integer)$value;
				if(!$value || $value==0){
					$value = 21;
				}
			}
			if($field === "scan_frequency"){
				$value = (integer)$value;
				if($value || $value==0){
					$value == 4;
				}
			}
			if($field === "creation"){
				print("//#error: Esse campo não pode ser alterado!");
				die();
			}
			if($field === "modification"){
				$value = date("Y-m-d H:i:s");
			}
			$this->args['id'] = $this->idDomain;
			$this->args['field'] = $field;
			$this->args['value'] = $value;
			$this->args['format'] = 'OBJECT';
			$this->args['privilegeName'] = $this->privilegeName;
			$this->args['name'] = 'scan_domains';
			$Update = new Update($this->args);
			
			if($Update){
				print("//#message: Registro alterado com sucesso!");
				die();
			}else{
				print("//#error: Erro ao alterar registro!");
				die();
			}
		}
		public function deleteDomains($id = 0, $props = array()){
			$id = (integer)$id;
			if(!$id || $id==0){
				print('//#error: ID do domínio é obrigatório!'. PHP_EOL);
				die();
			}
			$sqlDelete = "";
			$this->idDomain = $id;
			$this->args['format'] = "OBJECT";
			$this->args['field']['id'] = "sd.id";
			$this->args['field']['domain'] = "sd.domain";
			$this->args['clauseWhere'] = "WHERE sd.id = " . $this->idDomain;
			$getDomains = $this->getDomains(
				array(
					field=>$this->args['field'],
					clauseWhere => $this->args['clauseWhere']
				)
			);
			for($i=0; $i<count($getDomains['rows']); $i++){
				if($getDomains['rows'][$i]['domain'] == $this->currentDomain){
					print('//#error: Não é permitido excluir seu próprio domínio!!');
					die();
				}	
			}
			$sqlDelete = "
				DELETE
					FROM
						gt8_scan_domains
					WHERE
						id = " . $this->idDomain . "
			";
			mysql_query($sqlDelete) or die("//#error: Erro ao excluir o domínio informado!");
			print("//#message: Domínio excluído com sucesso!");
			return;
		}
	}
?>