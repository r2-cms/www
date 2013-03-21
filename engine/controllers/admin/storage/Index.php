<?php
	if ( !defined('CROOT')) {
		require_once('../../engine/connect.php');
	}
	require_once( SROOT .'engine/functions/Pager.php');
	require_once( SROOT .'engine/classes/CardLister.php');
	
	class Index extends CardLister {
		public $name		= 'storage';
		public $orderFilter	= array(
			array("name", "name", 'e.path, e.filename')
		);
		
		function Index() {
			global $GT8;
			
			$this->options['addWhere']	= '';
			
			$this->checkReadPrivileges('storage/');
			
			$this->checkActionRequest();
			/***************************************************************************
			*                                 CARD LISTER                              *
			***************************************************************************/
			$this->keywords	= isset($_GET["q"])? str_replace(explode(" ",'" \' % + ? \\ / ^ ~ ` ´ ! $ * '), " ", $_GET["q"]): null;
			if ( $this->keywords) {
				//$this->options['search']	= isset($this->options['search'])? $this->options['search']: array();
				//$this->options['search'][]	= array('o.street, a.district, a.city', utf8_decode($this->keywords));
			}
			
			$this->checkReadPrivileges('storage/');
			parent::CardLister();
		}
		public function on404() {
			
		}
		private function checkActionRequest() {
			if ( isset($_GET['action']) ) {
				switch ( $_GET['action']) {
					case 'get-products-in': {
						
						$result	= $this->getProducts();
						
						if ( isset($_GET['format']) && $_GET['format'] === 'JSON') {
							print($result);
							die();
						}
						break;
					}
					case 'save-product': {
						$this->checkWritePrivileges( 'storage/', 2);
						
						$idProduct	= (integer)$_GET['idProduct'];
						$idStorage	= (integer)$_GET['idStorage'];
						$Storage	= Pager(array(
							'sql'	=> 'storage.list',
							'where'	=> ' AND e.id='. $idProduct
						));
						$Storage	= $Storage['rows'];
						$affected	= 0;
						$insertId	= 0;
						if ( !$Storage || !$Storage[0]) {
							mysql_query("
								INSERT INTO
									gt8_storage(
										id_explorer_storage,
										id_explorer_product,
										creation
									)
									VALUES(
										$idStorage,
										$idProduct,
										NOW()
									)
							") or die('SQL Insert Error on Adm.Storage!');
							
							$insertId	= mysql_insert_id();
							
							require_once( SROOT .'engine/functions/LogAdmActivity.php');
							LogAdmActivity( array(
								"action"	=> "insert",
								"page"		=> "storage/",
								"name"		=> 'product/storage',
								"value"		=> $idProduct .'/'. $idStorage,
								"idRef"		=> $idStorage
							));
						} else {
							mysql_query("
								UPDATE
									gt8_storage
								SET
									id_explorer_storage	= $idStorage
								WHERE
									id_explorer_product	= $idProduct
							") or die('SQL Update Error on Adm.Storage!'. mysql_error());
							
							$affected	= mysql_affected_rows();
							
							require_once( SROOT .'engine/functions/LogAdmActivity.php');
							LogAdmActivity( array(
								"action"	=> "update",
								"page"		=> "storage/",
								"name"		=> 'product/storage',
								"value"		=> $idProduct .'/'. $idStorage,
								"idRef"		=> $idStorage
							));
						}
						
						if ( isset($_GET['format']) && $_GET['format'] === 'JSON') {
							if ( $insertId) {
								print('//#insert id:'. $insertId. PHP_EOL);
								print('//#message: Produto registrado em depósito com sucesso!'. PHP_EOL);
							} else if ( $affected) {
								print('//#affected rows: ('. $affected .')'. PHP_EOL);
								print('//#message: Produto registrado em depósito com sucesso!'. PHP_EOL);
							}
							die();
						}
						break;
					}
					case 'remove-product': {
						$this->checkWritePrivileges( 'storage/');
						
						$idProduct	= (integer)$_GET['idProduct'];
						mysql_query("
							DELETE FROM
								gt8_storage
							WHERE
								id_explorer_product	= $idProduct
						") or die('SQL Delete Error on Adm.Storage!');
						
						$affected	= mysql_affected_rows();
						
						require_once( SROOT .'engine/functions/LogAdmActivity.php');
						LogAdmActivity( array(
							"action"	=> "delete",
							"page"		=> "storage/",
							"name"		=> 'product',
							"value"		=> $idProduct,
							"idRef"		=> $idStorage
						));
						
						if ( isset($_GET['format']) && $_GET['format'] === 'JSON') {
							print('//#affected rows: ('. mysql_affected_rows() .')'. PHP_EOL);
							print('//#message: Produto removido do depósito com sucesso!'. PHP_EOL);
							die();
						}
						break;
					}
				}
			}
		}
		public function getProducts() {
			$path	= $this->getSPath('storage/');
			
			if ( count($path) === 4) {
				array_pop($path);
			}
			$storagePath	= $path;
			
			$path			= mysql_real_escape_string(join('/', $path));
			$storagePath	= mysql_real_escape_string(join('/', $storagePath));
			
			$this->options['sql']		= 'storage.list';
			$this->options['addWhere']	.= " AND s.path = 'storage/$storagePath/'";
			$this->options['format']	= isset($_GET['format'])? $_GET['format']: 'OBJECT';
			$this->options['addSelect']	= ',
				(
					SELECT
						v.value
					FROM
						gt8_explorer_attributes_value v
						JOIN gt8_explorer_attributes a ON a.id = v.id_attributes
					WHERE
						a.attribute = "tamanho" AND 
						v.id_explorer = e.id AND
						v.id_attributes = a.id 
				) AS tamanho
			';
			$this->options['limit']		= '600';
			
			//$this->options['debug']	= 1;
			$Pager	= Pager($this->options);
			$this->Pager	= $Pager;
			$this->data['product-rows']	= $Pager['rows'];
			return $Pager['rows'];
		}
		public function getServerJSVars() {
			return parent::getServerJSVars();
		}
		public function setCards() {
			$this->options['sql']		= 'explorer.list';
			$this->options['addWhere']	= '
				AND e.path REGEXP "^storage/"
			';
			$this->options['limit']		= '600';
			
			//$this->options['debug']	= 1;
			$Pager	= Pager($this->options);
			$this->Pager	= $Pager;
			//avoid empty
			if ( count($Pager['rows']) == 0) {
				//$Pager['rows']	= array(
				//	
				//);
			}
			
			$this->data['storage-rows']	= $Pager['rows'];
			return $Pager['rows'];
		}
		public function getFooter($data=array()) {
			return '';
		}
	}
?>