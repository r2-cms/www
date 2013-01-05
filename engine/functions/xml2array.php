<?php
	/*
		@name xml2array
		@description: transforma um doc XML em um array PHP
		@author: desconhecido. Modificado por Roger (jCube.com) para atender as necessidades da Girafa.com.br
		@date: 2010-09-26
	*/
	function xml2array($xml) {
		
		$xml	= simplexml_load_string($xml);
		$arr	= array();
		if ( !function_exists("_recurse")) {
			function _recurse($xml,$arr) {
				$iter	= 0;
				foreach($xml->children() as $b){
					$a	= $b->getName();
					
					if (!$b->children()) {
						$arr[$a]	= trim($b[0]);
					} else {
						//$arr[$a][]	= array();
						$arr[$a][]	= _recurse($b,$arr[$a][$iter]);
					}
					$iter++;
				}
				return $arr;
			}
		}
		
		return _recurse($xml, array());
	}
	
 ?>