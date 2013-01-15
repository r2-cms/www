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
			$this->setFAQ();
			
			return $contents;
		}
		public function on404() {
			//ignore 404
		}
		protected function setFAQ() {
			require_once( SROOT .'engine/functions/Pager.php');
			$Pager	= Pager(array(
				'sql'	=> 'explorer.list',
				'addSelect'	=> 'd.description AS article',
				'addFrom'	=> 'INNER JOIN gt8_explorer_data d ON d.id = e.id',
				'addWhere'	=> ' AND e.path = "pages/FAQ/" AND e.type != "directory"'
			));
			print("<pre>". print_r($Pager['rows'], 1) ."</pre>". PHP_EOL);
			die();
			$this->data['articles-faq']	= $Pager['rows'];
		}
	}
?>