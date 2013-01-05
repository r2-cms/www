<?php
	/*
		@name: returnDataFormatted
		@description: retorna Array nos formatos: TABLE | XML | JSON | OBJECT
		@author: Robson Cândido
		@date: 02/12/2012
		@args: Os argumentos devem ser passados com a combinação: Chave e Valor, sendo que Valor pode ser composto de no máximo mais um ARRAY
		
	*/
	
	function returnArrayFormatted($format = "OBJECT", &$array = array()){
		if(!isset($array)){
			return false;
		}elseif(count($array)<1){
			return false;
		}
		
		$returnFormat = "";
		if($format == "TABLE"){
			$returnFormat = '<table border="1" cellpadding="0" cellspacing="0" style="margin:20px auto 0; color:#343434;" >';
		}elseif($returnFormat == "XML"){
			$returnFormat = '<?xml version="1.0" encoding="utf-8"?>';
		}elseif($returnFormat == "JASON"){
			$returnFormat = '';
		}
		
		$tables = array();
		
		foreach($array as $matrizes=>$values){
			$tables[] = $matrizes; //chave das matrizes principais
		}
		
		switch($format){
			case "TABLE":
				for($i=0; $i<count($tables); $i++){
					$returnFormat .= '<table border="1" cellpadding="0" cellspacing="0" style="margin:20px auto 0; color:#343434;" >';
					$returnFormat .= "<caption>" . $tables[$i] . "</caption>";
					$returnFormat .="<tr>";
					foreach($array[$tables[$i]] as $key=>$value){
						$returnFormat .= '
							<th>' . $key . '</th>
						';
					}
					$returnFormat .="</tr><tr>";
					$returnFormat .="<tr>";
					foreach($array[$tables[$i]] as $key=>$value){
						$returnFormat .= '
							<td>' . $value . '</td>
						';
					}
					$returnFormat .="</tr>";
					$returnFormat .="</table>";
				}
				return $returnFormat;
				break;
				
			case "XML":
				$returnFormat .="<Tables>";
				for($i=0; $i<count($tables); $i++){
					$returnFormat .="<" . $tables[$i] . ">";
					foreach($array[$tables[$i]] as $key=>$value){
						$returnFormat .="<" . utf8_encode($key) . ">" . utf8_encode($value) . "</" . utf8_encode($key) . ">";
					}
					$returnFormat .="</" . $tables[$i] . ">";
				}
				$returnFormat .="</Tables>";
				header("content-type: application/xml");
				return $returnFormat;
				
				break;
			case "JSON":
				break;
			default:
				break;
		}
	}
?>