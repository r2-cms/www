<?php
	require_once( SROOT .'engine/classes/GT8.php');
	
	class Security  extends GT8{
		public function __construct(){
			
		}
		public function validField($field, $table, $alias){
			$position = 0;
			$length = 0;
			$this->name = $table;
			$Field = null;
			
			if(count(explode(",", $field))> 0){
				$field = explode(",", $field);
				$position = 0;
				$length = 0;
				
				for($i=0; $i<count($field); $i++){
					$field[$i] = ltrim($field[$i]);
					
					if(substr($field[$i], 0, 6) == "COUNT("){
						$position = 6 + strlen(RegExp($alias, '[a-zA-Z0-9]+'))+1;
						$length = strpos(substr($field[$i], $position), ")");
					//elseif(Conforme a necessidade pode-se adicionar mais condicionais para encontrar campos dentro de funções do mysql)
					}else{
						$position = strlen(RegExp($alias, '[a-zA-Z0-9]+'))+1;
						if(strpos(substr($field[$i], $position), " ")){
							$length = strpos(substr($field[$i], $position), " ");
						}elseif(strpos(substr($field[$i], $position), ",")){
							$length = strpos(substr($field[$i], $position), ",");
						}else{
							$length = strrpos(substr($field[$i], $position), substr($field[$i], -1)) + 1;
						}
					}
					
					$Field = $this->getType(RegExp(substr($field[$i], $position, $length),  '[a-zA-Z0-9_\-\.\s]+'));
					if(!$Field){
						print("//#error: Campo " . strtoupper(substr($field[$i], $position, $length)) . " não encontrado em " . $this->name .  "!");
					}
				}
			}
			return $Field;
		}
	}
	interface IScanDomain{
		public function getDomains($props = array());
		public function addDomains($props = array());
		public function updateDomains($id = 0, $props = array());
		public function deleteDomains($id = 0, $props = array());
	}
	interface IScanFilesDomain{
		public function getFilesDomains($props = array());
		public function addFilesDomains($props = array());
		public function updateFilesDomains($id = 0, $props = array());
		public function deleteFilesDomains($id = 0, $props = array());
	}	
?>