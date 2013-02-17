<?php
	if ( !defined('CROOT')) {require_once( "engine/connect.php");}
	
	GT8::leaveSSL();
	
	require_once(SROOT."engine/classes/CardLister.php"); 
	require_once(SROOT."engine/functions/Pager.php");
	
	class Index extends CardLister {
		public $name	= 'categories';
		public $genre	= '';
		public $defaultLimit	= 30;
		protected $filters	= array();
		protected $crrDirPath	= '474/';
		private $pathRegExp		= '^474/[0-9]+/$';
		private $isSearch	= false;
		public $orderFilter	= array(
			array("mais-vendidos", "mais vendidos", 'ev.vtotal DESC'),
			array("mais-visualizados", "mais populares", 'ev.vtotal DESC'),
			array("menos-visualizados", "menos populares", 'ev.vtotal ASC'),
			array("maior-valor", "maior valor", 'ez.price_selling DESC'),
			array("menor-valor", "menor valor", 'ez.price_selling ASC'),
			array("nome", "nome", 'e.title')
		);
		public $colors	= array('amarelo','azul','azul-marinho','bege','branco','bronze','caramelo','cinza','coral','grafite','laranja','marrom','marfim','preto','rosa','roxo','verde','oliva','vermelho','vinho','cobra','dourado','floral','listra','multicolorido','onca','onca-escuro','ouro','poas','prata','xadrez','zebra');
		public function __construct() {
			if ( !isset($_SESSION['shopping'])) {
				$_SESSION['shopping']	= array();
			}
			if ( !isset($_SESSION['shopping']['total-items'])) {
				$_SESSION['shopping']['total-items']	= 0;
			}
			if ( isset($_GET['logout'])) {
				require_once( SROOT .'engine/functions/CheckLogin.php');
				die();
			}
			$this->setDirPath();
			$this->checkActionRequest();
			$this->options['addSelect']	= '';
			$this->options['addFrom']	= 'INNER JOIN gt8_explorer ez	ON ez.id = e.id_dir';
			$this->options['where']		= ' AND e.stock > 0';
			$this->options['ids']		= array();
			$this->prepareFilters();
			
			$this->checkCategories();
		}
		protected function setDirPath() {//this method also checks if url exists
			global $GT8, $paths;
			$dirs	= $paths;
			if ( count($dirs)) {
				$this->isSearch	= $dirs[0].'/' == $GT8['search']['root'];
				
				$row		= mysql_fetch_array(mysql_query("SELECT id FROM gt8_explorer WHERE filename = '{$GT8['catalog']['explorer-root']}' AND id_dir = 0"));
				$crrPath	= $row[0] .'/';
				$lastId		= $row[0] .'';
				foreach( $dirs AS $name) {
					if ( $name) {
						$row	= mysql_fetch_array(mysql_query("SELECT id, filename FROM gt8_explorer WHERE id_dir = $lastId AND filename = '$name'"));
						$lastId	= $row[0];
						$crrPath	.= $row[0].'/';
						if ( empty($row[0])) {
							if ( $this->isSearch) {
								break;
							} else {
								$this->on404();
							}
						}
					} else {
						break;
					}
				}
				$this->crrDirPath	= $crrPath;
				$array	= explode('/', $crrPath);
				$pathRegExp	= '^474/';
				for ( $i=1; $i<4; $i++) {
					if ( isset($array[$i]) && $array[$i]) {
						$pathRegExp	.= $array[$i] .'/';
					} else {
						$pathRegExp	.= '[0-9]+/';
					}
				}
				$this->pathRegExp	= $pathRegExp .'$';
			}
		}
		private function checkCategories() {
			global $paths, $GT8;
			
			$spath	= $paths;
			
			if ( $spath[count($spath)-1] == '') {
				unset($spath[count($spath)-1]);
			} else {
				header('location: '.$spath[count($spath)-1].'/');
				die();
			}
			
			$myLevel	= isset($_SESSION['login']['level'])?$_SESSION['login']['level']: 0;
			$this->options['where']	.= " AND e.approved = 1 AND e.read_privilege <= $myLevel";
			
			if ( isset($spath[4]) || (!isset($spath[4]) && isset($_GET['translate-img'])&&$_GET['translate-img']=1)) {//variações
				//este arquivo, consulta o caminho de GT8.explorer.root para assegurar que a intenção é realmente carregar uma imagem. Portanto, simularemos esse caminho
				$_GET['path']	= $GT8['explorer']['root'].'catalogo/'. $_GET['path'];
				global $sizeRequested;
				require( SROOT .'engine/controllers/admin/explorer/LoadPhisic.php');
			}
			if ( isset($spath[3]) && !$this->isSearch) {//produto
				require_once( SROOT.'engine/controllers/catalog/Product.php');
				//envie como argumento o path menos o último diretório
				$Product	= new Product(substr($this->crrDirPath, 0, -strpos(strrev($this->crrDirPath), '/', 1)));
				$Product->printView(
					SROOT .'engine/views/catalog/product.inc',
					$Product->data,
					$Product
				);
				die();
			}
			if ( isset($spath[2]) && !$this->isSearch) {//MARCA
				$spath[0]	= RegExp($spath[0], '[a-zA-Z0-9\-\_]+');
				$spath[1]	= RegExp($spath[1], '[a-zA-Z0-9\-\_]+');
				$spath[2]	= RegExp($spath[2], '[a-zA-Z0-9\-\_]+');
				
				//$this->genre	= $spath[0];
				$this->options['where']	.= ' AND ez.dirpath REGEXP "'. $this->pathRegExp .'"';//catalogo/line/family/brand/
				
				$this->data['category-title']	= strtoupper(substr($spath[0], 0, 1)) . substr($spath[0], 1);
				
				$this->getCategories();
				
				foreach( $this->data['categories'] AS $i=>$row) {
					if ( $row['filename']==$spath[1]) {
						$this->data['categories'][$i]['selected']	= 'selected';
					}
				}
				
				self::CardLister();
				$this->printView(
					SROOT .'engine/views/catalog/index.inc',
					$this->data,
					$this
				);
				die();
			}
			if ( isset($spath[1]) && !$this->isSearch) {//FAMÍLIAS
				$spath[0]	= RegExp($spath[0], '[a-zA-Z0-9\-\_]+');
				$spath[1]	= RegExp($spath[1], '[a-zA-Z0-9\-\_]+');
				
				//$this->genre	= $spath[0];
				$this->options['where']	.= ' AND ez.dirpath REGEXP "'. $this->pathRegExp .'"';//catalogo/line/family/
				
				$this->data['category-title']	= strtoupper(substr($spath[0], 0, 1)) . substr($spath[0], 1);
				
				$this->getCategories();
				
				foreach( $this->data['categories'] AS $i=>$row) {
					if ( $row['filename']==$spath[1]) {
						$this->data['categories'][$i]['selected']	= 'selected';
					}
				}
				
				self::CardLister();
				$this->printView(
					SROOT .'engine/views/catalog/index.inc',
					$this->data,
					$this
				);
				die();
			}
			if ( isset($spath[0]) || $this->isSearch) {//LINHAS
				$spath[0]	= RegExp($spath[0], '[a-zA-Z0-9\-\_]+');
				
				$this->genre	= $spath[0];
				
				if ( $this->isSearch) {
					if (isset($spath[1])) {
						$this->options['search']	= array(
							array('e.title,e.sumary,e.path', mysql_real_escape_string($spath[1]))
						);
						$GT8['search-key-words']	= htmlspecialchars($spath[1]);
					}
				}
				
				$this->options['where']	.= ' AND ez.dirpath REGEXP "'. $this->pathRegExp .'"';//catalogo/line/
				$this->addAttributeInSelect('genero');
				
				$this->data['category-title']	= strtoupper(substr($spath[0], 0, 1)) . substr($spath[0], 1);
				$this->getCategories();
				
				self::CardLister();
				$this->printView(
					SROOT .'engine/views/catalog/index.inc',
					$this->data,
					$this
				);
				die();
			}
			{//HOME
				$this->printView(
					SROOT .'engine/views/index.inc',
					$this->data,
					$this
				);
				die();
			}
		}
		private function prepareFilters() {
			if ( isset($_GET['preco-minimo'])) {//MIN PRICE
				$this->filters[]	= array('preco-minimo', (integer)$_GET['preco-minimo'], true);
				$this->options['where']	.= ' AND ez.price_selling > '. (integer)$_GET['preco-minimo'];
			}
			if ( isset($_GET['preco-maximo'])) {//MAX PRICE
				$this->filters[]	= array('preco-maximo', (integer)$_GET['preco-maximo'], true);
				$this->options['where']	.= ' AND ez.price_selling < '. (integer)$_GET['preco-maximo'];
			}
			{//SIZES
				$tamanhos	= explode(',', isset($_GET['tamanhos'])?$_GET['tamanhos']:'');
				$this->data['filter-sizes']	= array();
				for ( $i=28; $i<=43; $i++) {
					$this->data['filter-sizes'][]	= array(
						'size'		=> $i,
						'selected'	=> in_array( $i, $tamanhos)? 'href-button-orange': ''
					);
				}
				if ( isset($_GET['tamanhos'])) {
					$tamanhos	= array();
					$_GET['tamanhos']	= explode(',', $_GET['tamanhos']);
					for ($i=0; $i<count($_GET['tamanhos']); $i++) {
						$tamanhos[]	= (integer)$_GET['tamanhos'][$i];
					}
					
					$this->options['addFrom']	.= '
						LEFT JOIN gt8_explorer_attributes_value v2 	ON e.id = v2.id_explorer
						LEFT JOIN gt8_explorer_attributes a2 		ON a2.id = v2.id_attributes
					';
					$this->options['where']	.= '
						AND (a2.attribute="tamanho" AND v2.value IN ('. join(',', $tamanhos) .')) 
					';
				}
			}
			{//COLORS
				$cores	= explode(',', $_GET['cores']);
				$this->data['filter-colors']	= array();
				for ( $i=0, $len=count($this->colors); $i<$len; $i++) {
					$this->data['filter-colors'][]	= array(
						'color'		=> $this->colors[$i],
						'selected'	=> in_array( $this->colors[$i], $cores)? 'selected': ''
					);
				}
				if ( isset($_GET['cores']) && $_GET['cores']) {
					$cores	= explode(',', RegExp($_GET['cores'], '[a-zA-Z0-9çáéíóúãõâê\-\_\,]+'));
					
					$this->options['addFrom']	.= '
						LEFT JOIN gt8_explorer_attributes_value v 	ON ez.id = v.id_explorer
						LEFT JOIN gt8_explorer_attributes a 		ON a.id = v.id_attributes
					';
					$this->options['where']		.= ' AND (a.attribute="cor" AND v.value IN ("'. join('","', $cores) .'")) '. PHP_EOL;
				}
			}
			{//ATTRIBUTES
				$onlyAttribs;
				$attrWhere	= $this->options['where'];
				$attribsGet	= array();
				$optionsWhere	= $this->options['where'];
				
				if ( isset($_GET['atributos'])&& $_GET['atributos']) {
					$attribsGet	= explode(',', strtolower(mysql_real_escape_string($_GET['atributos'])));
					sort($attribsGet);
					$array		= array();
					$lastValue	= '';
					
					if ( !isset($_GET['cores']) || !$_GET['cores']) {
						$this->options['addFrom']	.= '
							LEFT JOIN gt8_explorer_attributes_value v 	ON ez.id = v.id_explorer
							LEFT JOIN gt8_explorer_attributes a 		ON a.id = v.id_attributes
						';
					}
					for($i=0,$len=count($attribsGet); $i<$len; $i++) {
						$crr	= explode('-', $attribsGet[$i]);
						if ( !isset($array[$crr[0]])) {
							$array[$crr[0]]	= array();
							$onlyAttribs[]	= $crr[0];
						}
						$array[$crr[0]][]	= $crr[1];
					}
					foreach ( $array AS $name=>$crr) {
						$name	= utf8_decode($name);
						$optionsWhere	.= '
							AND (a.attribute="'. $name .'" AND v.value IN ("'. utf8_decode(join('","', $crr)) .'"))
						';
						$attrWhere	.= '
							AND (SELECT
									COUNT(*)
								FROM
									gt8_explorer e2
									JOIN gt8_explorer_attributes_value v	ON v.id_explorer = e2.id
									JOIN gt8_explorer_attributes a			ON a.id = v.id_attributes
								WHERE
									1 = 1
									AND dirpath REGEXP "'. $this->pathRegExp .'"
									AND (a.attribute="'. $name .'" AND v.value IN ("'. utf8_decode(join('","', $crr)) .'"))
									AND e2.id = e.id_dir
							) > 0
						';
					}
					
				}
				if ( 1||!isset($_GET['atributos']) && !$_GET['atributos']) {
					$Pager	= Pager(array(
						'select'	=> '
							e.id, e.attribute, e.value, stock, COUNT(*) AS total, "" AS selected
						',
						'from'		=> "
							(
								SELECT
									e.id, 
									e.id_dir, 
									a.attribute,
									v.value,
									e.dirpath,
									e.path,
									e.filename,
									1 AS stock
								FROM
									gt8_explorer e
									INNER JOIN gt8_explorer ez					ON ez.id = e.id_dir
									LEFT JOIN gt8_explorer_attributes_value v	ON ez.id = v.id_explorer
									JOIN gt8_explorer_attributes a				ON a.id = v.id_attributes
									
									LEFT JOIN gt8_explorer_attributes_value v2 	ON e.id = v2.id_explorer
									LEFT JOIN gt8_explorer_attributes a2 		ON a2.id = v2.id_attributes
								WHERE
									1 = 1
									AND ez.dirpath REGEXP '{$this->pathRegExp}'
									AND e.stock > 0
									AND a.attribute NOT IN('cor', 'desconto')
									$attrWhere
								GROUP BY
									attribute, VALUE, ez.id
								
								ORDER BY
									attribute, VALUE
								) e
						",
						'where'		=> '
							
						',
						'group'		=> '
							e.attribute, e.value
						',
						'order'		=> '
							attribute, value
						',
						'foundRows'=>1,
						'-debug'=>1
					));
				}
			}
			//o objetivo deste bloco é tornar visível somente os atributos que tem mais de 1 valor.
			$attributes	= array();
			$prv		= '';
			$attribsSet	= array();
			for ($i=0; $i<count($Pager['rows']); $i++) {
				$nxt	= isset($Pager['rows'][$i+1])? $Pager['rows'][$i+1]['attribute']: '';
				
				$isSelected	= $onlyAttribs? in_array(strtolower($Pager['rows'][$i]['attribute']), $onlyAttribs): false;
				if ( $isSelected || $prv===$Pager['rows'][$i]['attribute'] || $nxt===$Pager['rows'][$i]['attribute']) {
					
					//neste subselect, é feito uma consulta somente para exibir os features do atributo atual, enviado via GET.
					if ( $onlyAttribs && in_array( strtolower($Pager['rows'][$i]['attribute']), $onlyAttribs)) {
						$otherAttribs	= '';
						$lastAttr	= '';
						$crrValues	= array();
						//print("<h1>=========================================</h1>".PHP_EOL);
						//print("<h1>". $Pager['rows'][$i]['attribute'] ."</h1>".PHP_EOL);
						for ( $k=0; $k<count($attribsGet); $k++) {
							if ( $attribsGet[$k]) {
								$name	= explode('-', $attribsGet[$k]);
								$value	= $name[1];
								$name	= $name[0];
								//print("<div>{$Pager['rows'][$i]['attribute']} - {$name}</div>".PHP_EOL);
								if ( strtolower($Pager['rows'][$i]['attribute']) != strtolower($name)) {
									for ( $l=$k; $l<count($attribsGet); $l++) {
										if ( $attribsGet[$l]) {
											$name2	= explode('-', $attribsGet[$l]);
											$value	= $name2[1];
											$name2	= $name2[0];
											$attribsGet[$l]	= '';
											if ( $name == $name2) {
												$crrValues[]	= $value;
											} else {
												break;
											}
										}
									}
									$otherAttribs	.= '
										AND (SELECT
												COUNT(*)
											FROM
												gt8_explorer e2
												JOIN gt8_explorer_attributes_value v	ON v.id_explorer = e2.id
												JOIN gt8_explorer_attributes a			ON a.id = v.id_attributes
											WHERE
												1 = 1
												AND (a.attribute="'. $name .'" AND v.value IN ("'. utf8_decode(join('","', $crrValues)) .'"))
												AND e2.id = e.id_dir
										) > 0
									';
									$crrValues	= array();
									break;
								}
							}
						}
						if ( !in_array($Pager['rows'][$i]['attribute'], $attribsSet)) {
							$CrrAttribute	= Pager(array(
								'select'	=> '
									id, attribute, value, stock, COUNT(*) AS total, "" AS selected
								',
								'from'		=> "
									(
										SELECT
											e.id, 
											e.id_dir, 
											a.attribute,
											v.value,
											e.dirpath,
											e.path,
											e.filename,
											1 AS stock
										FROM
											gt8_explorer e
											INNER JOIN gt8_explorer ez					ON ez.id = e.id_dir
											LEFT JOIN gt8_explorer_attributes_value v	ON v.id_explorer = ez.id
											JOIN gt8_explorer_attributes a				ON a.id = v.id_attributes
											
											LEFT JOIN gt8_explorer_attributes_value v2 	ON e.id = v2.id_explorer
											LEFT JOIN gt8_explorer_attributes a2 		ON a2.id = v2.id_attributes
										WHERE
											1 = 1
											AND ez.dirpath REGEXP '{$this->pathRegExp}'
											AND e.stock > 0
											AND a.attribute NOT IN('cor', 'desconto')
											AND a.attribute IN('{$Pager['rows'][$i]['attribute']}')
											
											$otherAttribs
											
											{$this->options['where']}
										GROUP BY
											attribute, value, ez.id
										
										ORDER BY
											attribute, value
										) e
								",
								'where'		=> '
									
								',
								'group'		=> '
									attribute, value
								',
								'order'		=> '
									attribute, value
								',
								'foundRows'	=>0,
								'-debug'=>1
							));
							$attribsSet[]	= $Pager['rows'][$i]['attribute'];
							//$attributes[]	= $CrrAttribute['rows'][$i];
							for ( $j=0; $j<count($CrrAttribute['rows']); $j++) {
								$attributes[]	= $CrrAttribute['rows'][$j];
							}
						}
						//print("<pre>". print_r($CrrAttribute, 1) ."</pre>". PHP_EOL);
						//print("<h1>#############################################</h1>".PHP_EOL);
					} else {
						$attributes[]	= $Pager['rows'][$i];
					}
				}
				$prv	= $Pager['rows'][$i]['attribute'];
			}
			//print("<pre>". print_r(22222, 1) ."</pre>". PHP_EOL);
			//print("<pre>". print_r($attributes, 1) ."</pre>". PHP_EOL);
			//die();
			
			$this->data['attributes']	= $attributes;
			if ( isset($_GET['atributos']) && $_GET['atributos']) {
				$attributes	= explode(',', strtolower(utf8_decode($_GET['atributos'])));
				foreach( $this->data['attributes'] AS $i=>$row) {
					if ( in_array(strtolower($row['attribute'].'-'.$row['value']), $attributes)) {
						$this->data['attributes'][$i]['selected']	= 'selected';
					}
				}
			}
			$this->options['where']	= $attrWhere;
		}
		public function on404() {
			parent::on404();
		}
		private function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				switch($_GET['action']) {
					
				}
				die();
			}
		}
		public function addAttributeInSelect( $attribute, $alias='', $options=null, $dbColumn='e') {
			$alias	= $alias? $alias: $attribute;
			$options	= $options? $options: $this->options;
			$options['addSelect']	.= ",
				(
					SELECT
						v.value
					FROM
						gt8_explorer_attributes_value v
						JOIN gt8_explorer_attributes a ON a.id = v.id_attributes
					WHERE
						a.attribute = '$attribute' AND v.id_explorer = $dbColumn.id AND v.id_attributes = a.id 
				) AS `$alias`
			";
			
			return $options;
		}
		public function getCategories() {
			if ( !isset($this->data['categories']) && count($this->data['categories'])==0) {
				global $paths;
				
				$dir2	= substr($this->pathRegExp, 0, strpos($this->pathRegExp, '/', 5)) .'/$';
				$Pager	= Pager(array(
					'sql'	=> 'explorer.list',
					'addSelect'	=> ", '' AS selected",
					'where'	=> ' AND e.dirpath REGEXP "'.$dir2.'"',
					'order'	=> 'e.title'
				));
				
				//correct category's name
				switch($this->data['category-title']) {
					case 'Calcados': $this->data['category-title']	= 'Calçados'; break;
				}
				
				//replace fullpath
				foreach( $Pager['rows'] AS $i=>$row) {
					$Pager['rows'][$i]['fullpath']	= CROOT . str_replace(
						array('downloads/catalogo/'),
						array(''),
						$Pager['rows'][$i]['fullpath']
					);
				}
				$this->data['categories']	= $Pager['rows'];
			}
			
			return $this->data['categories'];
		}
		public function setCards() {
			$this->options['sql']	= 'explorer.list';
			$this->options['addSelect']	.= ', SUBSTRING_INDEX(SUBSTRING_INDEX(e.path, "/", 4), "/", -1) AS brand';
			$this->options['addSelect']	.= ', SUBSTRING_INDEX(e.path, "/", -5) AS varname, e.filename AS imgname';
			$this->options['addSelect']	.= ', SUBSTRING(e.path, 10) AS l_path';
			$this->options['addSelect']	.= ',
				ez.title,
				ez.filename,
				ez.price_suggested,
				ez.price_cost,
				ez.price_selling, 
				ez.price_parts, 
				ez.price_selling / ez.price_parts AS price_finantial
			';
			$this->options['group']		= 'e.dirpath';
			$this->addAttributeInSelect( 'off');
			//$this->options['debug']	= 1;
			
			if ( isset($_GET)) {
				
			}
			
			if ( isset($_SESSION['login']) && isset($_SESSION['login']['level']) && $_SESSION['login']['level'] > 3) {
				$this->options['addSelect']	.= ', 1 AS bt_admin';
			} else {
				$this->options['addSelect']	.= ', 0 AS bt_admin';
			}
			
				
			require_once( SROOT .'engine/functions/Pager.php');
			$this->Pager	= Pager($this->options);
			$this->data['foundRows']	= $this->Pager['foundRows'];
			
			//$this->Pager['rows']	= str_replace(
			//	array('0x0', 'privilege-r', '/type=directory',	'/type=file'),
			//	array('@', 'semi-invisible', '/',				'?edit'),
			//	$this->Pager['rows']
			//);
			$this->data['page']	= $this->Pager['page'];
			$this->data['found-0-rows']	= $this->data['foundRows'] > 0? '0': '1';
			$this->data['rows']	= $this->Pager['rows'];
		}
		public function getSpecialOffersField( $page, $field) {
			if ( !isset($this->data['special-offers-'. $page])) {
				$this->setSpecialOffersData( $page, 'special-offers-'. $page, 'special-offers-'.$page.'-rows');
			}
			return $this->data['special-offers-'.$page][$field];
		}
		public function setSpecialOffersData( $page, $dataName, $dataRowsName) {
			$Config	= Pager(array(
				'sql'		=> 'offers-config.list',
				'required'	=> array(
					array('oc.page', $page)
				)
			));
			$Config	= $Config['rows'][0];
			$this->data[$dataName]	= $Config;
			
			$ids	= explode(',', $Config['source']);
			$options	= array();
			$options['sql']	= 'explorer.list';
			$options['required']	= array(
				array('ez.id', join(',', $ids))
			);
			$options['addFrom']	.= ' INNER JOIN gt8_explorer ez	ON ez.id = e.id_dir';
			$options['addFrom']	.= ' INNER JOIN gt8_explorer eb	ON eb.id = ez.id_dir';
			$options['addSelect']	.= ', SUBSTRING_INDEX(SUBSTRING_INDEX(e.path, "/", 4), "/", -1) AS brand';
			$options['addSelect']	.= ', SUBSTRING_INDEX(e.path, "/", -5) AS varname, e.filename AS imgname';
			$options['addSelect']	.= ', SUBSTRING(e.path, 10) AS l_path';
			$options['addSelect']	.= ',
				ez.sumary,
				ez.title,
				eb.title AS brand
			';
			$options['addSelect']	.= ',
				ez.title,
				ez.filename,
				ez.price_suggested,
				ez.price_cost,
				ez.price_selling, 
				ez.price_parts, 
				ez.price_selling / ez.price_parts AS price_finantial
			';
			$options['group']		= 'e.dirpath';
			$options['limit']		= $Config['limit'];
			$options['foundRows']		= count($ids);
			//$options['debug']	= 1;
			
			$Pager	= Pager($options);
			$Pager['rows']	= str_replace(
				array('0x0', 'privilege-r', '/type=directory',	'/type=file'),
				array('@', 'semi-invisible', '/',				'?edit'),
				$Pager['rows']
			);
			$rows	= array();
			for( $i=0; $i<count($ids); $i++) {
				$id	= $ids[$i];
				for( $j=0; $j<count($Pager['rows']); $j++) {
					if ( $Pager['rows'][$j]['id_dir'] == $id) {
						$rows[]	= $Pager['rows'][$j];
						break;
					}
				}
			}
			$this->data[$dataRowsName]	= $rows;
		}
		public function getBanners( $page, $field, $index=-1) {
			$index	= (integer)$index;
			if ( !isset($this->data['banners-'. $page])) {
				$this->setBannersData( $page, 'banners-'. $page, 'banners-'.$page.'-rows');
			}
			if ( $index > -1) {
				return $this->data['banners-'.$page.'-rows'][$index][$field];
			}
			
			return $this->data['banners-'.$page][$field];
		}
		public function setBannersData( $page, $dataName, $dataRowsName) {
			$Config	= Pager(array(
				'sql'		=> 'banners-config.list',
				'required'	=> array(
					array('bc.page', $page)
				)
			));
			$Config	= $Config['rows'][0];
			$this->data[$dataName]	= $Config;
			
			$ids	= explode(',', $Config['source']);
			$options	= array();
			$options['sql']	= 'explorer.list';
			$options['required']	= array(
				array('ez.id', join(',', $ids))
			);
			$options['addFrom']	.= ' INNER JOIN gt8_explorer ez	ON ez.id = e.id_dir';
			$options['addFrom']	.= ' INNER JOIN gt8_explorer eb	ON eb.id = ez.id_dir';
			$options['addSelect']	.= ', SUBSTRING_INDEX(e.path, "/", -5) AS varname, e.filename AS imgname';
			$options['addSelect']	.= ', SUBSTRING_INDEX(e.path, "/", 4) AS l_path, ez.sumary, ez.title, e.filename AS bg';
			$options['group']		= 'e.dirpath';
			$options['foundRows']		= count($ids);
			$options['where']		= str_replace('AND e.stock > 0', '', $options['where']);
			//$options['debug']	= 1;
			
			$options	= $this->addAttributeInSelect('link', '', $options, 'ez');
			$options	= $this->addAttributeInSelect('style-banner', '', $options, 'ez');
			$Pager	= Pager($options);
			$Pager['rows']	= str_replace(
				array('0x0', 'privilege-r', '/type=directory',	'/type=file'),
				array('@', 'semi-invisible', '/',				'?edit'),
				$Pager['rows']
			);
			$rows	= array();
			for( $i=0; $i<count($ids); $i++) {
				$id	= $ids[$i];
				for( $j=0; $j<count($Pager['rows']); $j++) {
					if ( $Pager['rows'][$j]['id_dir'] == $id) {
						$rows[]	= $Pager['rows'][$j];
						break;
					}
				}
			}
			$this->data[$dataRowsName]	= $rows;
		}
		public function getServerJSVars() {
			$this->jsVars[]	= array('id', 0);
			$this->jsVars[]	= array('d', $this->idDir);
			$this->jsVars[]	= array('u', utf8_encode(addslashes($_SESSION['login']['name'])));
			$this->jsVars[]	= array('priceMin', isset($_GET['preco-minimo'])? (integer)$_GET['preco-minimo']: 0, true);
			$this->jsVars[]	= array('priceMax', isset($_GET['preco-maximo'])? (integer)$_GET['preco-maximo']: 1000, true);
			
			return parent::getServerJSVars();
		}
	}
?>