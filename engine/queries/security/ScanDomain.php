<?php
	require_once( SROOT ."engine/queries/security/Security.php");
	require_once( SROOT ."engine/functions/Pager.php");
	require_once( SROOT ."engine/functions/validDateTime.php");
	
	class ScanDomain extends Security implements IScanDomain{
		function __construct(){
			
		}
		public function getDomains($props = array(full=> "*")){
			$format = isset($props['format'])? strtoupper($props['format']): "OBJECT";
			$props['full'] = $props['full'] != null? $props['full']: $props['full'] = "*";
			$fields = isset($props['field']['id'])? RegExp($props['field']['id'], '[a-zA-Z_\-]+') . ", ": "";
			$fields .= isset($props['field']['id_user'])? RegExp($props['field']['id_user'], '[a-zA-Z_\-]+') . ", ": "";
			$fields .= isset($props['field']['ftp'])? RegExp($props['field']['ftp'], '[a-zA-Z_\-]+') . ", ": "";
			$fields .= isset($props['field']['domain'])? RegExp($props['field']['domain'], '[a-zA-Z_\-]+') . ", ": "";
			$fields .= isset($props['field']['login'])? RegExp($props['field']['login'], '[a-zA-Z_\-]+') . ", ": "";
			$fields .= isset($props['field']['pass'])? RegExp($props['field']['pass'], '[a-zA-Z_\-]+') . ", ": "";
			$fields .= isset($props['field']['port'])? RegExp($props['field']['port'], '[a-zA-Z_\-]+') . ", ": "";
			$fields .= isset($props['field']['scan_frequency'])? RegExp($props['field']['scan_frequency'], '[a-zA-Z_\-]+') . ", ": "";
			$fields .= isset($props['field']['creation'])? RegExp($props['field']['creation'], '[a-zA-Z_\-]+') . ", ": "";
			$fields .= isset($props['field']['modification'])? RegExp($props['field']['modification'], '[a-zA-Z_\-]+'): "";
			$fields = count(explode(",", rtrim($fields)))>0 && $fields != ""? substr(rtrim($fields), -1) === ","? substr(rtrim($fields), 0, -1): $fields: $props['full'];
			$where = "";
			$limit = isset($props['limit'])? (integer)$props['limit']: 50;
			$index = isset($props['index'])? (integer)$props['index']: 0;
			$group = isset($props['group'])? mysql_real_escape_string($props['group']): "";
			
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
				'from' => 'gt8_scan_domains',
				'where' => $where,
				'index' => $index,
				'limit' => $limit,
				'group' => $group,
				'format' => $format
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
			$creation = isset($props['creation'])? validDateTime("date", $props['creation']): "NOW()";
			$modification = "NOW()";
			$sqlInsert = "";
			
			return $creation;
			
		}
		public function updateDomains($props = array()){
			
		}
		public function deleteDomains($props = array()){
			
		}
	}
?>