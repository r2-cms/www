<?php
	class Distatic extends GT8 {
		public function __construct() {
			global $paths;
			
			if ( !isset($paths[1])) {
				die();
			}
			$file	= RegExp( $paths[1], '[a-zA-Z0-9\.\_\-]+');
			
			if ( file_exists( SROOT .'engine/views/distatic/internal/'. $file)) {
				require_once( SROOT .'engine/functions/Pager.php');
				if ( $file === 'info.js') {
					$this->getInfo();
				}
				$this->printView( SROOT .'engine/views/distatic/internal/'. $file);
			} else {
				die();
			}
			
			die();
		}
		private function getInfo() {
			require_once( SROOT .'engine/functions/Pager.php');
			$Pager	= Pager(array(
				'sql'	=> 'products.families',
				'addSelect'	=> 'd.description',
				'addFrom'	=> 'INNER JOIN gt8_explorer_data d ON e.id = d.id'
			));
			for( $i=0; $i<count($Pager['rows']); $i++){
				$Pager['rows'][$i]['description']	= utf8_encode(str_replace(array('\n', '\r\n', '\r', chr(10)), '', nl2br(addslashes($Pager['rows'][$i]['description']))));
			}
			$this->data['familiesInfo']	= $Pager['rows'];
			
			require_once( SROOT .'engine/functions/Pager.php');
			$Pager	= Pager(array(
				'sql'	=> 'products.brands-info',
				'limit'		=> '20'
			));
			for( $i=0; $i<count($Pager['rows']); $i++){
				$Pager['rows'][$i]['description']	= utf8_encode(str_replace(array('\n', '\r\n', '\r', chr(10)), '', nl2br(addslashes($Pager['rows'][$i]['description']))));
			}
			$this->data['brandsTop10']	= $Pager['rows'];
		}
	}
?>