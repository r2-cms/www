<?php
	/*
		@name: validDateTime
		@description: Valida data e hora e retorna em formatos específicos
		@author: Robson Cândido
		@date: 16/10/2012
		@ :P X-Burguer com muita cebola = X-Bola
	*/
	function validDateTime($field, $value) {
		if (!$value){
			return false;
		}
		$result = "";
		$date = "";
		$time = "";
		$S = "";
		
		if($field == "date"){
			$value = RegExp($value, '[0-9]{4}[-|\/]{1}[0-9]{2}[-|\/]{1}[0-9]{2}');
			$x = substr($value, 4, 1);
			$value = ($x == "-")? str_replace("/", $x, $value): str_replace("-", $x, $value);
			$date = explode($x, $value);
		}
		if($field == "time"){
			$value = RegExp($value, '[0-9]{2}[:]{1}[0-9]{2}[:]{1}[0-9]{2}');
			$time = explode(":", $value);
		}
		if($field == "datetime"){
			$value = RegExp($value, '[0-9]{4}[-|\/]{1}[0-9]{2}[-|\/]{1}[0-9]{2}[a-z|A-Z|\s]{1}[0-9]{2}[:]{1}[0-9]{2}[:]{1}[0-9]{2}');
			$S = substr($value, 10, 1); //AAAA-MM-DDTHH:MM:SS
			
			if(!$S){
				return false;
			}
			
			$value = explode($S, $value);
			$x = substr($value[0], 4, 1);
			$value[0] = ($x == "-")? str_replace("/", $x, $value[0]): str_replace("-", $x, $value[0]);
			$date = explode($x, $value[0]);
			$time = explode(":", $value[1]);
		}
		
		if($date){
			
			if(checkdate($date[1], $date[2], $date[0])){
				$date = $date[0] . "-" . $date[1] . "-" . $date[2];
			}else{
				return false;
			}	
		}
		
		if($time[0]>23){
			return false;
		}elseif($time[1]>59){
			return false;
		}elseif($time[2]>59){
			return false;
		}else{
			$time = $time[0] . ":" . $time[1] . ":" . $time[2];
		}
		
		switch ($field){
			case "date":
				return $date;
				break;
			
			case "time":
				return $time;
				break;
			
			case "datetime":
				return $date . $S . $time;
				break;
		}
	}
	
 ?>