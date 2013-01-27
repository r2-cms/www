<?php
    /**
     * @author: Robson Cândido
     * @version: 1.0
     * @package: Esta classe faz parte do sistema R2-CMS
     * @method: getDomains()
     * 		Params: $props['field']['@campo']:
     * 			Dentro da chave @campo pode ser passado o campo no qual se deseja o retorno;
     * 			Caso o campo desejado não esteja na lista pode-se usar o parâmetro 'others' em @campo;
     * 			Como essa query faz um JOIN entre as tabelas gt8_scan_domains e gt8_users é necessário utilizar os prefixos:
     * 				sd.campo para a tabela gt8_scan_domains e u.campo para a tabela gt8_users;
    **/

	require_once( SROOT ."engine/queries/security/Security.php");
	require_once( SROOT ."engine/functions/Pager.php");
	require_once( SROOT ."engine/functions/validDateTime.php");
	
	class ScanDomain extends Security implements IScanDomain{
		private $idDomain = 0;
		private $args = array();
		private $currentDomain = "";
		public $privilegeName	= 'security/scanner/';
		
		//Params para ajax:
		//#message: Campo atualizado com sucesso!
		//#affected rows: 1
		//#affected
		//#error:
		//#message:
		
		function __construct(){
			$this->currentDomain = $_SERVER['SERVER_NAME'];
		}
		public function getDomains($props = array(full=> "*")){
			$format = isset($props['format'])? strtoupper($props['format']): "OBJECT";
			$sqlFull = "
				sd.id AS idDomain, sd.id_user, sd.ftp, sd.domain, sd.login AS domainLogin, sd.pass AS domainPass, sd.port, sd.scan_frequency,
				sd.creation AS domainCreation, sd.modification AS domainModification,
				u.id AS idUser, u.natureza, u.name, u.cpfcnpj, u.document, u.genre, u.birth, u.login AS userLogin, u.hlogin AS userhLogin,
				u.pass AS userPass, u.level, u.enabled, u.approval_level_required, u.creation AS userCreation, u.modification AS userModification,
				u.last_access, u.access_counter, u.agent, u.sign, u.remarks
			";
			$fields = isset($props['field']['id'])? RegExp($props['field']['id'], '[a-zA-Z_\-\.\s]+') . ", ": "";
			$fields .= isset($props['field']['id_user'])? RegExp($props['field']['id_user'], '[a-zA-Z_\-\.\s]+') . ", ": "";
			$fields .= isset($props['field']['ftp'])? RegExp($props['field']['ftp'], '[a-zA-Z_\-\.\s]+') . ", ": "";
			$fields .= isset($props['field']['domain'])? RegExp($props['field']['domain'], '[a-zA-Z_\-\.\s]+') . ", ": "";
			$fields .= isset($props['field']['login'])? RegExp($props['field']['login'], '[a-zA-Z_\-\.\s]+') . ", ": "";
			$fields .= isset($props['field']['pass'])? RegExp($props['field']['pass'], '[a-zA-Z_\-\.\s]+') . ", ": "";
			$fields .= isset($props['field']['port'])? RegExp($props['field']['port'], '[a-zA-Z_\-\.\s]+') . ", ": "";
			$fields .= isset($props['field']['scan_frequency'])? RegExp($props['field']['scan_frequency'], '[a-zA-Z_\-\.\s]+') . ", ": "";
			$fields .= isset($props['field']['creation'])? RegExp($props['field']['creation'], '[a-zA-Z_\-\.\s]+') . ", ": "";
			$fields .= isset($props['field']['modification'])? RegExp($props['field']['modification'], '[a-zA-Z_\-\.\s]+') . ", ": "";
			$fields .= isset($props['field']['others'])? RegExp($props['field']['others'], '[a-zA-Z_\-\.\s]+'): "";
			$fields = count(explode(",", rtrim($fields)))>0 && $fields != ""? substr(rtrim($fields), -1) === ","? substr(rtrim($fields), 0, -1): $fields: $sqlFull;
			$where = "";
			$limit = isset($props['limit'])? (integer)$props['limit']: 50;
			$index = isset($props['index'])? (integer)$props['index']: 0;
			$group = isset($props['group'])? mysql_real_escape_string($props['group']): null;
			$template = isset($props['template']) && $props['template']? $props['template']: null;
			
			if(substr($props['field']['others'], 0, 5) == "COUNT"){
				if(!isset($group) || !$group){
					die("//#error: COUNT necessita da cláusula GROUP definida!");
				}
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
			$ftp = isset($props['ftp'])? mysql_real_escape_string($props['ftp']): null;
			$domain = isset($props['domain'])? mysql_real_escape_string($props['domain']): null;
			$login = isset($props['login'])? mysql_real_escape_string($props['login']): null;
			$pass = isset($props['pass'])? mysql_real_escape_string($props['pass']): null;
			$port = isset($props['port'])? (integer)$props['port']: 0;
			$scan_frequency = isset($props['scan_frequency'])? (integer)$props['scan_frequency']: 0;
			$creation = isset($props['creation'])? $props['creation']=="NOW()"? "NOW()": validDateTime("datetime", $props['creation']): "NOW()";
			$modification = "NOW()";
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
			$FieldValue = isset($props['FIeldValue'])? $props['FIeldValue']: null;
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
				$value = "NOW()";
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
			$this->idDomain = (integer)$id;
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