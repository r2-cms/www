<?php
	if ( !defined('CROOT')) {
		die('Undefined connection! (Q40->A114e00::U91a1e)');
	}
	global $GT8;
	require_once( SROOT .'engine/functions/CheckLogin.php');
	require_once( SROOT .'engine/functions/CheckPrivileges.php');
	/*
		Update
			id:			Required address id
			field:		[a-zA-Z0-9_]+
			value:		all
			print
	*/
	class Update extends GT8 {
		public $privilegeName;
		
		public $name;
		public $logName;
		public $logId;
		public $logField;
		protected $field;
		protected $id;
		
		public $affectedRows	= -1;
		
		public function Update($options) {
			$this->id	= isset($options["id"])?(integer)($options["id"]): $this->id;
			$field	= RegExp($options["field"], "[a-zA-Z0-9_]+");
			$value	= $options["value"];
			$format	= isset($options["format"])? $options["format"]: 'OBJECT';
			
			if ( isset($options['privilegeName']) && $options['privilegeName'] ) {
				$this->privilegeName	= $options['privilegeName'];
			}
			if(isset($options['name']) && $options['name']){
				$this->name = $options['name'];
			}
			if ( !$this->id) {
				die( PHP_EOL ."//#error: ID ausente!". PHP_EOL);
			} else if ( !$field) {
				die( PHP_EOL ."//#error: nome da coluna ausente!". PHP_EOL);
			} else if ( !$this->privilegeName) {
				die('//#error: propriedade requerida em Update::privilegeName não definida!'. PHP_EOL);
			}
			
			$this->checkWritePrivileges( $this->privilegeName, $field, $format, 2);
			
			$Field	= $this->getType($field);
			
			//fields
			if ( !$Field) {
				die('//#error: campo não encontrado!'. PHP_EOL);
			}
			$this->field	= $field;
			
			$value	= $this->getValue($field, $value);
			
			if ( strpos(strtolower($Field['Type']), 'int(') > -1) {
				$value	= (integer)$value;
			} else if ( strpos(strtolower($Field['Type']), 'float') > 0) {
				$value	= (float)(str_replace(",", ".", $value));
			} else if ( strpos(strtolower($Field['Type']), 'double') > -1) {
				$value	= (float)(str_replace(",", ".", $value));
			} else if ( strpos(strtolower($Field['Type']), 'char(') > -1 ) {
				$value	= mysql_real_escape_string($value);
			} else if ( strpos(strtolower($Field['Type']), 'blob') > -1 ) {
				$value	= mysql_real_escape_string($value);
			} else if ( strpos(strtolower($Field['Type']), 'text') > -1 ) {
				$value	= mysql_real_escape_string($value);
			} else if ( $Field['Type'] ==  'date' ) {
				preg_match( "/([0-9]{2}).([0-9]{2}).([0-9]{4})/", $value, $date);
				
				if ( $date[1] && $date[2] && $date[3]) {
					$value	= $date[3] .'-'. $date[2] .'-'. $date[1];
				} else {
					if ( $format == "JSON") {
						print(PHP_EOL ."//#error: Data inválida!". PHP_EOL);
					}
					return false;
				}
			} else {
				$value	= mysql_real_escape_string($value);
			}
			
			mysql_query("
				UPDATE
					gt8_". $this->name ."
				SET
					`$field` = '$value'
				WHERE
					id = {$this->id}
				LIMIT
					1
			") or die("SQL UPDATE address Error (1)");
			
			if ( $format == "JSON") {
				print(PHP_EOL ."//#message: Campo atualizado com sucesso!". PHP_EOL);
				print(PHP_EOL ."//#affected rows: ". mysql_affected_rows() . PHP_EOL);
			}
			$this->affectedRows	= mysql_affected_rows();
			if ( $this->affectedRows) {
				require_once( SROOT .'engine/functions/LogAdmActivity.php');
				LogAdmActivity( array(
					"action"	=> "update",
					"page"		=> ($this->logName? $this->logName: $this->name) ."/",
					"name"		=> ($this->logField? $this->logField: $field),
					"value"		=> $value,
					"idRef"		=> ($this->logId? $this->logId: $this->id)
				));
			}
			return true;
		}
		public function getValue( $field, $value) {
			return $value;
		}
		public function getType( $field) {
			//type
			$result	= mysql_query('
				DESCRIBE `gt8_'. $this->name .'`
			') or die('Can not get table data type in SQL Update');
			
			$Field	= array();
			while( $row = mysql_fetch_assoc($result)) {
				$Field[]	= $row;
			}
			$fieldFound	= false;
			for ($i=0; $i<count($Field); $i++) {
				if ( $Field[$i]['Field'] == $field ) {
					$Field	= $Field[$i];
					$fieldFound	= true;
					break;
				}
			}
			if ( !$fieldFound) {
				$Field	= null;
			}
			return $Field;
		}
	}

?>