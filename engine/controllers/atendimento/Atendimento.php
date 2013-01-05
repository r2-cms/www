<?php
	require_once(SROOT .'engine/classes/GT8.php');
	
	class Atendimento extends GT8 {
		public function __construct() {
			global $GT8;
			
			parent::GT8();
		}
		public function getCrrContent() {
			global $GT8;
			$name	= substr( $GT8['atendimento']['root'], 0, -1);
			
			$dirs	= explode("$name/", $_GET['path']);
			if ( empty($dirs[0])) {
				array_shift($dirs);
			}
			$dirs	= explode('/', $dirs[0]);
			if ( !$dirs[ count($dirs)-1]) {
				array_pop($dirs);
			}
			$contents	= '';
			if ( isset($dirs[0])) {
				//security
				if ( strlen($dirs[0]) != strlen(RegExp($dirs[0], '[a-zA-Z0-9\-_]+'))) {
					$this->redirect(404);
				} else if ( file_exists(SROOT."engine/views/atendimento/". $dirs[0] .'.inc')) {
					$contents	= $this->printView(SROOT."engine/views/atendimento/". $dirs[0] .'.inc', null, null, null, false);
				} else {
					parent::on404();
				}
			} else {
				$contents	= $this->printView(SROOT."engine/views/atendimento/sumary.inc", null, null, null, false);
			}
			return $contents;
		}
		public function on404() {
			//ignore 404
		}
	}
?>