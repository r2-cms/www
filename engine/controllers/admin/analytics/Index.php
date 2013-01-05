<?php
	if ( !defined('CROOT')) {
		require_once('../../engine/connect.php');
		//die('Undefined GT8: a13i2->a114e00->Home');
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/functions/CheckPrivileges.php");
	
	CheckPrivileges( null, null, 'analytics/', 1);
	require_once( SROOT .'engine/classes/CardLister.php');
	
	class Index extends CardLister {
		public $name		= 'analytics';
		public $orderFilter	= array(
			array("id-desc", "natural", 'a.id DESC, p.id DESC')
		);
		
		function __construct() {
			global $GT8;
			
			
			$this->checkActionRequest();
			/***************************************************************************
			*                                 CARD LISTER                              *
			***************************************************************************/
			$this->keywords	= isset($_GET["q"])? str_replace(explode(" ",'" \' % + ? \\ / ^ ~ ` ´ ! $ * '), " ", $_GET["q"]): null;
			if ( $this->keywords) {
				$this->options['search']	= isset($this->options['search'])? $this->options['search']: array();
				$this->options['search'][]	= array('a.street, a.district, a.city', utf8_decode($this->keywords));
			}
			parent::CardLister($options);
			$this->createSidebar();
			$this->createToolbar();
		}
		private function createToolbar() {
			global $GT8;
			
			$this->addToolbarItemG(array(
				array('Exibir grupos', 'groups', '', '', '', 'Grupos'),
				array('Exibir filtros', 'filters', '', CROOT .'imgs/gt8/toolbar/filter-small.png')
			));
			
			return;
			$this->addToolbarItem('Adicionar endereço', 'add-new-address', CROOT.$GT8['admin']['root'].'address/novo/', CROOT.'imgs/gt8/toolbar/file-add-small.png');
			$this->addToolbarItemG(array(
				array('Excluir endereço(s)', 'delete-address', 'delete/', CROOT.'imgs/gt8/toolbar/file-del-small.png', 'return Pager.toggleDeleteButtons()')
			), 'group-toggle');
		}
		private function createSidebar() {
			global $GT8;
			
			$groups	= isset($_GET['groups'])&&$_GET['groups']? explode(',', RegExp($_GET['groups'], '[a-zA-Z0-9\-\,]+')): array();
			$this->addSideBarItem( 'Grupos', 'groups', (CROOT.'imgs/gt8/filter-small.png'), '
				<ul class="checkbox folder" title="Agrupamentos" >
					<li><a '. (in_array('visit', $groups)? 'class="checked" ':'') .'href="?groups-visit" >Visitantes</a></li>
					<li><a '. (in_array('page', $groups)? 'class="checked" ':'') .'href="?groups-page" >Páginas</a></li>
					<li><a '. (in_array('referrer', $groups)? 'class="checked" ':'') .'href="?groups-referrer" >Origens</a></li>
					<li><a '. (in_array('ip', $groups)? 'class="checked" ':'') .'href="?groups-ip" >IPs</a></li>
					<li><a '. (in_array('user', $groups)? 'class="checked" ':'') .'href="?groups-user" >Usuários</a></li>
					<li><a '. (in_array('browser', $groups)? 'class="checked" ':'') .'href="?groups-browser" >Navegadores</a></li>
					<li><a '. (in_array('os', $groups)? 'class="checked" ':'') .'href="?groups-os" >Sistemas operacionais</a></li>
					<li><a '. (in_array('duration', $groups)? 'class="checked" ':'') .'href="?groups-duration" >Duração</a></li>
					<li><a '. (in_array('day', $groups)? 'class="checked" ':'') .'href="?groups-day" >Dias</a></li>
					<li><a '. (in_array('month', $groups)? 'class="checked" ':'') .'href="?groups-month" >Meses</a></li>
				</ul>
			');
			$this->addSideBarItem( 'Filtros', 'filters', (CROOT.'imgs/gt8/filter-small.png'), '
				<label title="Procurar por palavras chaves" ><span><input type="text" value="'. (isset($_GET['q'])? (htmlentities(utf8_decode($_GET['q']))): '') .'" name="q" class="gt8-update input-rounded-shadowed" onkeyup="Pager.searchIn(this, event); Pager.search(this, event)" /><small>busca por palavras</small></span></label>
			');
			return;
			$this->addSideBarItem('Estados', 'estados', (CROOT.'imgs/gt8/favorite-small.png'), $this->getBREstadosFilter());
			$this->addSideBarItem('Marcas', 'brands', (CROOT.'imgs/gt8/favorite-small.png'), '');
		}
		public function getServerJSVars() {
			return parent::getServerJSVars();
		}
		private function checkActionRequest() {
			if ( $_GET['opt'] == 'update') {
				die();
				require_once( SROOT .'queries/address/UpdateAddress.php');
				new UpdateAddress(array(
					"id"		=> $_GET["id"],
					"field"		=> $_GET["field"],
					"value"		=> $_GET["value"],
					'format'	=> 'JSON'
				));
				die();
			}
			$this->saveGridState();
		}
		public function getCards($template='') {
			$this->options['sql']		= 'analytics.list';
			$this->options['format']	= 'GRID';
			$this->options['grid']	= array(
					'id'	=> '<a href="?id=$value$" >#value#</a>'. PHP_EOL .'								'
			);
			//$this->options['debug']	= '';
			$this->options['group']	= '';
			$this->options['gridConf']	= array(
				//format: label|width|type|length|minwidth|maxwidth
				/*0*/ 'ID|50|integer|10|20|100',
				/*1*/ 'IP|80|string|8|20|200',
				/*2*/ 'Usuário|80|string|8|20|200',
				/*3*/ 'Navegador|80|string|8|20|200',
				/*4*/ 'Vs|20|integer|8|20|200',
				/*5*/ 'SO|80|string|8|20|200',
				/*6*/ 'Origem|80|string|8|20|200',
				/*7*/ 'Página|80|string|8|20|200',
				/*8*/ 'Duração|80|timestamp|8|20|200',
				/*9*/ 'Data|80|datetime|8|20|200',
				/*4*/ 'ID view|20|integer|8|20|200'
			);
			$this->options['select']	= '
				a.id,
				a.ip,
				u.login,
				a.browser,
				a.browser_v,
				a.os,
				a.referrer,
				p.page,
				p.delay AS duration,
				DATE_FORMAT(p.creation, "%d/%m/%Y %H:%i:%s") AS creation,
				p.id AS id_analytics
			';
			
			$groups	= isset($_GET['groups'])&&$_GET['groups']? explode(',', RegExp($_GET['groups'], '[a-zA-Z0-9\-\,]+')): array();
			if ( count($groups)) {
				$this->options['group']	= '';
				$gDaySet	= false;
				foreach ( $groups as $value) {
					switch ( $value) {
						case 'month': {
							if ( !in_array('day', $groups)) {
								$this->options['group']	.= ', YEAR(p.creation), MONTH(p.creation)';
								$this->options['select']	= str_replace('DATE_FORMAT(p.creation, "%d/%m/%Y %H:%i:%s") AS creation', 'DATE_FORMAT(p.creation, "%m/%Y") AS creation', $this->options['select']);
							}
							$gDaySet	= true;
						}
						case 'day': {
							$this->options['select']	= str_replace('"" AS pageViews', 'COUNT(*) AS pageviews', $this->options['select']);
							
							$this->options['select']		= str_replace('a.id,', 'COUNT(DISTINCT a.id) AS id,', $this->options['select']);
							$this->options['gridConf'][0]	= str_replace(array('ID|'), array('IDs|'), $this->options['gridConf'][0]);
							
							if ( !in_array('ip', $groups)) { $this->options['select']			= str_replace('a.ip,', '"" AS ip,', $this->options['select']); }
							if ( !in_array('browser', $groups)) { $this->options['select']		= str_replace('a.browser,', '"" AS browser,', $this->options['select']); }
							if ( !in_array('browser_v', $groups)) { $this->options['select']	= str_replace('a.browser_v,', '"" AS browser_v,', $this->options['select']); }
							if ( !in_array('os', $groups)) { $this->options['select']			= str_replace('a.os,', '"" AS os,', $this->options['select']); }
							if ( !in_array('duration', $groups)) { $this->options['select']		= str_replace('p.delay AS duration,', 'SUM(p.delay) AS duration,', $this->options['select']); }
							if ( !in_array('referrer', $groups)) { $this->options['select']		= str_replace('a.referrer,', '"" AS referrer,', $this->options['select']); }
							
							//users
							if ( !in_array('user', $groups))	{
								$this->options['select']		= str_replace('u.login,', 'COUNT(DISTINCT a.id_users) AS login,', $this->options['select']);
								$this->options['gridConf'][2]	= str_replace(array('Usuário|', '|string|'), array('Usuários|','|integer|'), $this->options['gridConf'][2]);
							}
							//pages
							if ( !in_array('page', $groups)) {
								$this->options['select']		= str_replace('p.page,', 'COUNT(DISTINCT p.page) AS page,', $this->options['select']);
								$this->options['gridConf'][7]	= str_replace(array('Página|', '|string|'), array('Páginas|','|integer|'), $this->options['gridConf'][7]);
							}
							
							if ( $value=='day') {
								if ( in_array('month', $groups)) {
									$this->options['group']	.= ', YEAR(p.creation), MONTH(p.creation), DAY(p.creation)';
									$this->options['select']	= str_replace('DATE_FORMAT(p.creation, "%d/%m/%Y %H:%i:%s") AS creation', 'DATE_FORMAT(p.creation, "%d/%m/%Y") AS creation', $this->options['select']);
								} else {
									$this->options['group']	.= ', DAY(p.creation)';
									$this->options['select']	= str_replace('DATE_FORMAT(p.creation, "%d/%m/%Y %H:%i:%s") AS creation', 'DATE_FORMAT(p.creation, "%d") AS creation', $this->options['select']);
								}
							}
							$gDaySet	= true;
							break;
						}
						case 'visit': {
							$this->options['group']	.= ', a.id';
							
							//page
							$this->options['select']		= str_replace('p.page,', 'COUNT(p.id) AS page,', $this->options['select']);
							$this->options['gridConf'][7]	= str_replace(array('Página|', '|string|'), array('Páginas|','|integer|'), $this->options['gridConf'][7]);
							//duration
							if ( !in_array('duration', $groups)) { $this->options['select']		= str_replace('p.delay AS duration,', 'SUM(p.delay) AS duration,', $this->options['select']); }
							break;
						}
						case 'user': {
							$this->options['group']	.= ', a.id_users';
							
							$this->options['gridConf'][0]	= str_replace(array('ID|'), array('IDs|'), $this->options['gridConf'][0]);
							$this->options['select']		= str_replace('a.id,', 'COUNT(a.id) AS id,', $this->options['select']);
							
							if ( !in_array('ip', $groups)) { $this->options['select']			= str_replace('a.ip,', '"" AS ip,', $this->options['select']); }
							if ( !in_array('browser', $groups)) { $this->options['select']		= str_replace('a.browser,', '"" AS browser,', $this->options['select']); }
							if ( !in_array('vs', $groups)) { $this->options['select']			= str_replace('a.browser_v,', '"" AS browser_v,', $this->options['select']); }
							if ( !in_array('os', $groups)) { $this->options['select']			= str_replace('a.os,', '"" AS os,', $this->options['select']); }
							if ( !in_array('page', $groups)) { $this->options['select']		= str_replace('p.page,', '"" AS page,', $this->options['select']); }
							if ( !in_array('day', $groups) && !in_array('month', $groups) ) { $this->options['select']		= str_replace('DATE_FORMAT(p.creation, "%d/%m/%Y %H:%i:%s") AS creation', '"" AS creation', $this->options['select']); }
							if ( !in_array('duration', $groups) ) { $this->options['select']	= str_replace('p.delay AS duration,', '"" AS duration,', $this->options['select']); }
							if ( !in_array('referrer', $groups) ) { $this->options['select']	= str_replace('a.referrer,', '"" AS referrer,', $this->options['select']); }
							
							break;
						}
						case 'page': {
							$this->options['group']	.= ', p.page';
							
							$this->options['select']		= str_replace('p.id AS id_analytics', 'COUNT(*) AS id_analytics', $this->options['select']);
							$this->options['gridConf'][10]	= str_replace(array('ID view'), array('Qdte'), $this->options['gridConf'][10]);
							
							if ( !in_array('login', $groups) ) {//login
								$this->options['select']	= str_replace('u.login,', 'COUNT(DISTINCT a.id_users) AS login,', $this->options['select']);
								$this->options['gridConf'][2]	= str_replace(array('Usuário|', '|string|'), array('Usuários|', '|integer|'), $this->options['gridConf'][2]);
							}
							
							$this->options['select']		= str_replace('p.delay AS duration,', 'SUM(p.delay) AS duration,', $this->options['select']);
							
							break;
						}
					}
				}
			}
			$this->options['group']	= substr($this->options['group'], 2);
			/*******************************************************************
			*                                 FILTERS                          *
			*******************************************************************/
			$this->options['where']	= '';
			if ( isset($_GET['id']) && $_GET['id']) {
				$gets	= explode(',', RegExp($_GET['id'], '[0-9\,]+'));
				$this->options['where']	.= ' AND a.id IN (';
				for( $i=0; $i<count($gets); $i++) {
					$this->options['where']	.= '"'. $gets[$i] .'",';
				}
				$this->options['where']	= substr($this->options['where'], 0, -1) . ') ';
			}
			if ( isset($_GET['ip']) && $_GET['ip']) {
				$gets	= explode(',', RegExp($_GET['ip'], '[a-zA-Z0-9\-\.\ \,]+'));
				$this->options['where']	.= ' AND a.ip IN (';
				for( $i=0; $i<count($gets); $i++) {
					$this->options['where']	.= '"'. $gets[$i] .'",';
				}
				$this->options['where']	= substr($this->options['where'], 0, -1) . ') ';
			}
			if ( isset($_GET['login']) && $_GET['login']) {
				$gets	= explode(',', RegExp($_GET['login'], '[a-zA-Z0-9\-\.\ \,]+'));
				$ids	= array();
				$result	= mysql_query('SELECT id FROM gt8_users WHERE login IN ("'. join('","', $gets) .'")');
				while(($row=mysql_fetch_array($result))) {
					$ids[]	= $row[0];
				}
				$this->options['where']	.= " AND a.id_users IN (". join(',', $ids) .")";
			}
			if ( isset($_GET['browser']) && $_GET['browser']) {
				$gets	= explode(',', RegExp($_GET['browser'], '[a-zA-Z0-9\-\.\ \,]+'));
				$this->options['where']	.= ' AND a.browser IN (';
				for( $i=0; $i<count($gets); $i++) {
					$this->options['where']	.= '"'. $gets[$i] .'",';
				}
				$this->options['where']	= substr($this->options['where'], 0, -1) . ') ';
			}
			if ( isset($_GET['os']) && $_GET['os']) {
				$gets	= explode(',', RegExp($_GET['os'], '[a-zA-Z0-9\-\.\ \,]+'));
				$this->options['where']	.= ' AND a.os IN (';
				for( $i=0; $i<count($gets); $i++) {
					$this->options['where']	.= '"'. $gets[$i] .'",';
				}
				$this->options['where']	= substr($this->options['where'], 0, -1) . ') ';
			}
			if ( isset($_GET['page']) && $_GET['page']) {
				$gets	= explode(',', RegExp($_GET['page'], '[a-zA-Z0-9\-\.\ \,\/\&\?\#\=\:]+'));
				$this->options['where']	.= ' AND p.page IN (';
				for( $i=0; $i<count($gets); $i++) {
					$this->options['where']	.= '"'. $gets[$i] .'",';
				}
				$this->options['where']	= substr($this->options['where'], 0, -1) . ') ';
			}
			if ( isset($_GET['referrer']) && $_GET['referrer']) {
				$gets	= explode(',', RegExp($_GET['referrer'], '[a-zA-Z0-9\-\.\ \,\/\&\?\#\=\:]+'));
				$this->options['where']	.= ' AND p.referrer IN (';
				for( $i=0; $i<count($gets); $i++) {
					$this->options['where']	.= '"'. $gets[$i] .'",';
				}
				$this->options['where']	= substr($this->options['where'], 0, -1) . ') ';
			}
			$gridState	= $this->getParam( 'grid-state-'.$this->name, $category='admin');
			if ( $gridState) {
				$this->options['gridState']	= $gridState;
			}
			
			require_once(SROOT.'engine/functions/Pager.php');
			
			//$this->options['debug']	= 1;
			$Pager	= Pager($this->options);
			$this->Pager	= $Pager;
			//avoid empty
			if ( count($Pager['rows']) == 0) {
				//$Pager['rows']	= array(
				//	
				//);
			}
			
			return $Pager['rows'];
			
		}
		public function getFiltersInJS() {
			$users	= $this->__getWhereFilters('u.login');
			$brows	= $this->__getWhereFilters('a.browser');
			$os		= $this->__getWhereFilters('a.os');
			$pages	= $this->__getWhereFilters('p.page');
			$refers	= $this->__getWhereFilters('a.referrer');
			
			$this->jsVars	= array();
			$this->jsVars[]	= array('ips', $this->__getWhereFilters('a.ip'), true);
			$this->jsVars[]	= array('logins', $this->__getWhereFilters('u.login'), true);
			$this->jsVars[]	= array('browsers', $this->__getWhereFilters('a.browser'), true);
			$this->jsVars[]	= array('oss', $this->__getWhereFilters('a.os'), true);
			$this->jsVars[]	= array('pages', $this->__getWhereFilters('p.page'), true);
			$this->jsVars[]	= array('referrers', $this->__getWhereFilters('a.referrer'), true);
			
			$js	= 'var Filters	= {';
			for ( $i=0; $i<count($this->jsVars); $i++) {
				$crr	= $this->jsVars[$i];
				
				if ( isset($crr[2]) && $crr[2]) {
					$js	.= PHP_EOL .'				'. $crr[0] .': '. $crr[1] .',';
				} else {
					$js	.= PHP_EOL .'				'. $crr[0] .': "'. addslashes($crr[1]) .'",';
				}
			}
			$js	.= '
				meow: null
			};';
			return $js;
		}
		private function __getWhereFilters( $column) {
			$where	= '1=1 '. $this->options['where'];
			$name	= $column=='u.login'? 'a.id_users': $column;
			
			if ( strpos('#'. $this->options['where'], $name) > 0) {
				$start	= strpos($where, 'AND '. $name .' IN (');
				$len	= strpos($where, ')', $start+strlen('AND '+$name +' IN ('))-4;
				$where	= str_replace( substr($where, $start, $len), '', $where);
			}
			
			$result	= mysql_query("
				SELECT
					DISTINCT $column,
					COUNT(*) AS total
				FROM
					gt8_analytics a
					LEFT JOIN gt8_analytics_page p ON a.id = p.id_analytics
					LEFT JOIN gt8_users u ON u.id = a.id_users
				WHERE
					$where
				GROUP BY
					$column
				ORDER BY
					total DESC
				LIMIT 20
			");
			$name	= substr( $column, strpos($column, '.')+1);
			$js	= "[". PHP_EOL;
			while( ($row=mysql_fetch_array($result))) {
				$js	.= '					["'. addslashes($row[0]) .'", '. $row[1] .'],'. PHP_EOL;
			}
			if ( strlen($js) > 4) {
				$js	= substr($js, 0, -2);
			}
			$js	.= PHP_EOL."				]";
			
			return $js;
		}
	}
?>