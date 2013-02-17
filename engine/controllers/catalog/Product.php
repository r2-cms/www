<?php
	require_once( SROOT .'engine/classes/GT8.php');
	
	GT8::leaveSSL();
	
	class Product extends GT8 {
		public $id;
		protected $public;
		public $fullpath;
		public $filename;
		public $dirpath;
		public $isUnavaiable	= false;
		
		public function __construct( $dirpath) {
			global $spath, $paths;
			
			$this->checkActions();
			
			$this->filename	= RegExp($paths[1], '[a-zA-Z0-9\_\-\.]+');
			
			$this->dirpath	= $dirpath;
			
			$this->data	= $this->getRow();
			$this->setThumbs();
			$this->setSizes();
			$this->setAttributes();
			$this->data['path']	= RegExp($_GET['path'], '[a-z-A-Z0-9_\-\+\.\:\/\\\|\%\&\?]+');
			$this->data['CROOT']	= CROOT;
			$this->setAdminBt();
			$this->id	= $this->data['id'];
			$this->checkSotck();
			
			$myLevel	= isset($_SESSION['login']['level'])? $_SESSION['login']['level']: 1;
			
			if ( !$this->id) {
				$this->redirect();
			} else if ( $this->data['read_privilege'] > $myLevel) {
				$this->redirect('forbidden');
			}
			$this->jsVars[]	= array('id', $this->id, true);
		}
		private function checkSotck() {
			if ( $this->isUnavaiable) {
				$this->data['no-stock']	= '1';
				
			} else {
				$this->data['no-stock']	= '';
			}
		}
		protected function getRow() {
			global $GT8, $spath;
			$paths	= explode('/', $spath);
			$filename	= RegExp($paths[3], '[a-zA-Z_\-\.]+');
			require_once( SROOT.'engine/functions/Pager.php');
			$row	=  Pager(array(
				'sql'		=> 'explorer.list',
				'addSelect'	=> '
					e.sumary,
					REPLACE(d.description, "\\\n", "<br />") as description,
					UNIX_TIMESTAMP(e.publish_up) AS tpublish_up,
					UNIX_TIMESTAMP(e.publish_down) AS tpublish_down,
					SUBSTRING_INDEX(SUBSTRING_INDEX(e.path, "/", 4), "/", -1) AS brand,
					e.price_selling / e.price_parts AS financiamento
				',
				'addFrom'	=> 'LEFT JOIN gt8_explorer_data d ON e.id = d.id',
				'required'		=> array(
					array('e.dirpath', $this->dirpath),
					array('e.approved', 1),
					array('e.filename', $filename)
				)
			));
			$row	= $row['rows'][0];
			if ( !$row) {
				//array_shift($paths);
				$path	= join('/', $paths);
				$row	= $this->getUrlHistory( $GT8['explorer']['root'] . $GT8['catalog']['explorer-root'] .'/'. $path, $redirect=false, $url200='', $url404='');
				
				if ( $row && $row['new']) {
					$row['new']	= str_replace($GT8['explorer']['root'] . $GT8['catalog']['explorer-root'] .'/', $GT8['catalog']['root'], $row['new'].'/');
					
					header('location: '. CROOT . $row['new'] . $qsa, 301);
					die();
				} else {
					$this->redirect();
				}
			}
			
			return $row;
		}
		protected function redirect( $url='') {
			if ( 0 && $url == 'forbidden') {
				
			} else if ( file_exists( SROOT.'engine/views/catalog/404.inc') ) {
				GT8::printView(
					SROOT .'engine/views/404/catalog/404.inc',
					array(
						'path'	=> RegExp($_GET['path'], '[a-z-A-Z0-9_\-\+\.\:\/\\\|\%\&\?]+'),
						'CROOT'	=> CROOT
					)
				);
				die();
			} else {
				parent::redirect('not found');
			}
		}
		private function checkActions() {
			if ( isset($_GET['format']) && $_GET['format']=='JSON') {
				if ( isset($_GET['action']) && $_GET['action']=='addToHistory' && isset($_GET['idProduct']) ) {
					global $pageUnloading, $Analytics, $duration, $scroll;

					//as variáveis que estão aqui foram definidas no analytics
					$idProduct	= (integer)$_GET['idProduct'];
					if ( $idProduct && $pageUnloading) {
						$sql	= "
							INSERT INTO
								gt8_analytics_products(
									id_analytics,
									id_product
								)
							SELECT
								{$Analytics['id']},
								$idProduct
							FROM
								gt8_analytics_products
							WHERE
								id_analytics	= {$Analytics['id']} AND
								id_product		= $idProduct
							HAVING
								COUNT(*) = 0
						";
						mysql_query($sql) or die($_SESSION['login']['level']>7? mysql_error() . PHP_EOL .'----------'. PHP_EOL . $sql: 'Erro de sistema!');
					}
				}
				die();
			}
		}
		public function setAdminBt() {
			if ( isset($_SESSION['login']['level']) && $_SESSION['login']['level'] > 5) {
				global $GT8;
				
				$fullpath	= str_replace('downloads/catalogo/', 'explorer/catalogo/', $this->data['fullpath']);
				$this->data['bt-admin']	= '<a class="bt-admin" title="'. $this->data['id'] .'" href="'. CROOT . $GT8['admin']['root'] . $fullpath .'?edit" ><img src="'. CROOT .'imgs/gt8/admin-icon.png" width="28" height="24" alt="[acesso administrativo]" /></a>';
			} else {
				$this->data['bt-admin']	= '';
			}
		}
		private function addAttributeInSelect( $attribute, $alias='') {
			$alias	= $alias? $alias: $attribute;
			$this->options['addSelect']	.= ",
				(
					SELECT
						v.value
					FROM
						gt8_explorer_attributes_value v
						JOIN gt8_explorer_attributes a ON a.id = v.id_attributes
					WHERE
						a.attribute = '$attribute' AND v.id_explorer = e.id AND v.id_attributes = a.id 
				) AS $alias
			";
		}
		public function getDBARow() {
			return ("<pre class='padding-h' style='background:transparent;' >". utf8_encode(print_r($this->data, 1)) ."</pre>");
		}
		private function setThumbs() {
			$Pager	= Pager(array(
				'sql'		=> 'explorer.list',
				'addSelect'	=> ', SUBSTRING_INDEX(e.path, "/", -2) AS varname, e.filename AS imgname',
				'addWhere'	=> ' AND e.dirpath RegExp "^'. $this->data['dirpath'].$this->data['id'].'/[0-9]+/$"',
				'format'	=> 'TEMPLATE',
				'template'	=> $template
			));
			$this->data['thumbs']	= $Pager['rows'];
		}
		private function setSizes() {
			global $spath;
			$paths	= explode('/', $spath);
			$filename	= RegExp($paths[3], '[a-zA-Z_\-\.]+');
			
			$Pager	= Pager(array(
				'sql'		=> 'explorer.list',
				'addSelect'	=> ',
					SUBSTRING_INDEX(e.path, "/", -2) AS varname,
					e.filename AS imgname,
					(
						SELECT
							v.value
						FROM
							gt8_explorer_attributes_value v
							JOIN gt8_explorer_attributes a ON a.id = v.id_attributes
						WHERE
							a.attribute = "tamanho" AND v.id_explorer = e.id AND v.id_attributes = a.id 
					) AS crr_size
				',
				'addWhere'	=> ' AND e.dirpath RegExp "^'. $this->data['dirpath'] . $this->data['id'] .'/$" AND e.stock > 0 AND e.approved = 1',
				'order'		=> 'crr_size'
			));
			$this->data['sizes']	= $Pager['rows'];
			if ( empty($Pager['rows'])) {
				$this->data['sizes']	= array();
				$this->isUnavaiable	= true;
			}
		}
		private function setAttributes() {
			$Pager	= Pager(array(
				'sql'	=> 'explorer.list-attributes-value',
				'addSelect'	=> 'LOWER(REPLACE(a.attribute, " ", "-")) AS attr_name',
				'where'	=> '
					AND a.id_dir IN ('. str_replace('/', ',', substr($this->data['dirpath'], 0, -1)) .')
				',
				'replace'	=> array(
					array('vIn.id_explorer = vIn.id_explorer', 'vIn.id_explorer = '. $this->data['id'])
				),
				'format'	=> 'OBJECT',
				'template'	=> $template,
				'foundRows'	=> 1,
				'limit'	=> 100
			));
			$this->data['attributes']	= $Pager['rows'];
		}
	}
?>