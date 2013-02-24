<?php
    /**
	 *
     * @author: Robson Cândido
     * @version: 1.0
     * @package: Esta classe faz parte do sistema R2-CMS
     * @method: getDomains()
     * 		@Paramss:
     * 			- $props['field']
     * 				Nesta chave os campos devem ser passados separados por vírgula;
     * 				Como essa query faz um JOIN entre as tabelas gt8_scan_domains e gt8_users é necessário utilizar os alias:
	 * 					gt8_scan_domains = sd;
     * 					gt8_users = u;
     * 				Caso o parâmetro $props['field'] venha indefinido a propriedade $sqlFull será passada como padrão;
     *
     * 			- $props['searchLike']
     * 				Function: retorna resultados que contenham TODAS as procuras coincidentes
     * 				Parameters: prefix.field%value. Ex.: 'sd.domain%www.r2-cms.com.br'
     * 				Obs.: Se houver mais de uma procura elas deverão ser passadas separadas por vírgulas.
     * 					ex.: 'sd.domain%www.r2cms.com.br, sd.ftp%ftp@r2cms.com.br'
     *
     * 	@method: updateDomains()
     * 		@params:
     * 			- $id (id do domínio à ser atualizado)
     * 			- $field (campo ou campos a serem atualizados)
     * 				Obs.: caso haja mais de um campo FIELD a ser atualizado os campos deverão ser passados separados por vírgulas,
     * 				e os valores para VALUE deverão ser também separados por vírgulas, e com a quantidade equivalente a quantidade de campos informados.
     * 			- $value (valor ou valores de field. Valores dos campos a serem atualizados)
     * 				Ex.: 	$props['field'] = 'campo1, campo2, campo3, campo4'
     * 						$props['value']	= 'valor1, valor2, campo3, campo4'
     * 			- $format (formato de retorno [OBJECT,TABLE,CARD,JSON,GRID,TEMPLATE])
    **/

	require_once( SROOT ."engine/queries/security/Security.php");
	require_once( SROOT ."engine/functions/Pager.php");
	require_once( SROOT ."engine/functions/validDateTime.php");
	
	class ScanDomain extends Security implements IScanDomain{
		private $id_domain = 0;
		private $args = array();
		private $currentDomain = "";
		public $privilegeName	= 'security/scanner/';
		private $sqlFull = "
			sd.id AS idDomain, sd.id_user, sd.ftp, sd.domain, sd.login AS domainLogin, sd.pass AS domainPass, sd.port, sd.scan_frequency,
			sd.creation AS domainCreation, sd.modification AS domainModification,
			u.id AS idUser, u.natureza, u.name, u.cpfcnpj, u.document, u.genre, u.birth, u.login AS userLogin, u.hlogin AS userhLogin,
			u.pass AS userPass, u.level, u.enabled, u.approval_level_required, u.creation AS userCreation, u.modification AS userModification,
			u.last_access, u.access_counter, u.agent, u.sign, u.remarks
		";
		
		function __construct(){
			$this->currentDomain = $_SERVER['SERVER_NAME'];
		}
		public function getDomains($props = array()){
			$format = in_array($props['format'], explode(',', 'OBJECT,TABLE,CARD,JSON,GRID,TEMPLATE'))? $props['format']: 'OBJECT';
			$field = isset($props['fields']) && $props['fields']? $props['fields']: $this->sqlFull;
			$like = isset($props['searchLike']) && $props['searchLike']? $props['searchLike']: null;
			$searchLike = array();
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
					
					if(substr($field[$i], 0, 6) == "COUNT("){
						if(!isset($group) || !$group){
							die("//#error: COUNT necessita da cláusula GROUP definida!");
						}
						if(substr($field[$i], 6, 3) == "sd."){
							$table = "scan_domains";
							$alias = "sd";
						}elseif(substr($field[$i], 6, 2) == "u."){
							$table = 'users';
							$alias = "u";
						}else{
							$table = "scan_domains";
							$alias = "sd";
						}
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
			
			if(isset($props['clauseWhere'])){
				$where = trim(str_replace("WHERE", "AND", mysql_real_escape_string($props['clauseWhere']))) . PHP_EOL;
			}
			if(isset($props['clauseAnd'])){
				$where .= mysql_real_escape_string($props['clauseAnd']) . PHP_EOL;
			}
			if(isset($like) && $like){
				$like = explode(',', $like);
				$slike = null;
				$sfield = null;
				$svalue = null;
				
				if(count($like)>0){
					for($i=0; $i<count($like); $i++){
						$slike = explode('%', $like[$i]);
						
						if(count($slike)>1){
							if(substr($slike[0], 0, 3) == "sd."){
								$table = "scan_domains";
								$alias = "sd";
							}
							if(substr($slike[0], 0, 2) == "u."){
								$table = "users";
								$alias = "u";
							}
							if($this->validField($slike[0], $table, $alias)){
								$sfield .= $slike[0] . ", ";
								$svalue .= $slike[1] . PHP_EOL;
							}
						}
					}
					
					//Obs.: a considerar com o Roger:
					//Na function Page option [search], na primeira chave do array, quando os campos
					//ou os campos são enviados entre aspas simples ocorre um erro conforme exemplificado abaixo:
					/*SELECT
						sd.id,sd.domain
					FROM	
							gt8_scan_domains sd
								INNER JOIN
									gt8_users u
										ON
											u.id = sd.id_user				
					WHERE
						1 = 1
						AND (
							1 = 0
							OR 'sd.domain' LIKE '%'192.168.0.10'%'
						)
					LIMIT
						0, 50
					ERROR:You have an error in your SQL syntax; check the manual that corresponds to your
					MySQL server version for the right syntax to use near 	'192.168.0.10'%' ) LIMIT 0, 50' at line 15
					*/
					//OR 'sd.domain' LIKE '%'192.168.0.10'%': o campo fica entre aspas sendo encarado como uma string, dando erro
					//
					
					//print(count(explode(",", substr(trim($sfield), 0, -1)))>1? "YES":"NO");
					//die();
					
					$searchLike[] = array(substr(trim($sfield), 0, -1), trim($svalue));
				}
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
				'search' => $searchLike,
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
			")or die($_SESSION['login']['level']>7? "//#error: SQL INSERT Error:". mysql_error() . PHP_EOL : '//#error: Erro ao acessar o banco de dados!'. PHP_EOL);
			$id = mysql_insert_id();
			
			if($id){
				print('//#affected rows: '. mysql_affected_rows() . PHP_EOL);
				return $id;
			}
		}
		public function updateDomains($id = 0, $props = array()){
			require_once( SROOT .'engine/classes/Update.php');
			
			$this->id_domain = (integer)$id;
			$field = isset($props['field'])? explode(",", $props['field']): false;
			$value = isset($props['value'])? explode(",", $props['value']): false;
			$format = in_array($props['format'], explode(',', 'OBJECT,TABLE,CARD,JSON,GRID'))? $props['format']: 'OBJECT';
			$fieldValue = array();
			
			if(!$this->id_domain || $this->id_domain==0){
				print('//#error: ID do domínio é obrigatório!'. PHP_EOL);
				die();
			}
			
			if($field && count($field)>0){
				if(count($value) < count($field) || count($value) > count($field)){
					print("#error: Quantidade de valores incompatíveis com a quantidade de campos informados!");
					die();
				}
				$fieldValue = array_combine($field, $value);
				
				foreach($fieldValue as $key=>$vl){
					$field = RegExp($key, '[a-zA-Z_\-]+');
					$value = mysql_real_escape_string($vl);
					
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
						if(!$value || $value==0){
							$value == 4;
						}
					}
					if($field === "creation"){
						//print("//#error: Esse campo não pode ser alterado!");
						//die();
					}
					if($field === "modification"){
						if(!$value){
							$value = date("Y-m-d H:i:s");
						}
					}
					$this->args['id'] = $this->id_domain;
					$this->args['field'] = $field;
					$this->args['value'] = $value;
					$this->args['format'] = $format;
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
			}else{
				print("#error: Nenhum campo foi informado para ser atualizado!");
				die();
			}
		}
		public function deleteDomains($id = 0, $props = array()){
			$id = (integer)$id;
			$format = in_array($props['format'], explode(',', 'OBJECT,TABLE,CARD,JSON,GRID'))? $props['format']: 'OBJECT';
			
			if(!$id || $id==0){
				print('//#error: ID do domínio é obrigatório!'. PHP_EOL);
				die();
			}
			$sqlDelete = "";
			$this->id_domain = $id;
			$this->args['format'] = $format;
			$this->args['fields'] = "sd.id, sd.domain";
			$this->args['clauseWhere'] = "WHERE sd.id = " . $this->id_domain;
			$getDomains = $this->getDomains(
				array(
					fields=>$this->args['fields'],
					clauseWhere => $this->args['clauseWhere']
				)
			);
			for($i=0; $i<count($getDomains['rows']); $i++){
				if($getDomains['rows'][$i]['domain'] == $this->currentDomain){
					print('//#error: Não é permitido excluir seu próprio domínio!!');
					die();
				}	
			}
			$sqlDeleteDomain = "
				DELETE
					FROM
						gt8_scan_domains
					WHERE
						id = " . $this->id_domain . "
			";
			$sqlDeleteFilesDomain = "
				DELETE
					FROM
						gt8_scan_files
					WHERE
						id_scan_domain = " . $this->id_domain . "
			";
			mysql_query($sqlDeleteDomain) or die("//#error: Erro ao excluir o domínio informado!");
			mysql_query($sqlDeleteFilesDomain) or die("//#error: Erro ao excluir os arquivos do domínio!");
			print("//#message: Domínio excluído com sucesso!");
			return true;
		}
	}
?>