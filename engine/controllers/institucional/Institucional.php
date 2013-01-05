<?php
	require_once(SROOT .'engine/classes/GT8.php');
	
	class Institucional extends GT8 {
		public function __construct() {
			global $GT8;
			
			parent::GT8();
		}
		public function getCrrContent() {
			
			$dirs	= explode('/institucional/', $_GET['path']);
			$dirs[0]	= str_replace('institucional/', '', $dirs[0]);
			$dirs	= explode('/', $dirs[0]);
			if ( !$dirs[ count($dirs)-1]) {
				array_pop($dirs);
			}
			$contents	= '';
			if ( isset($dirs[0])) {
				//security
				if ( strlen($dirs[0]) != strlen(RegExp($dirs[0], '[a-zA-Z0-9\-_]+'))) {
					$this->redirect(404);
				} else if ( file_exists(SROOT.'engine/views/institucional/'. $dirs[0] .'.inc')) {
					$contents	= file_get_contents(SROOT.'engine/views/institucional/'. $dirs[0] .'.inc');
				} else {
					parent::on404();
				}
			} else {
				$contents	= file_get_contents(SROOT.'engine/views/institucional/sumary.inc');
			}
			return $contents;
		}
		public function on404() {
			//ignore 404
		}
	}
?>