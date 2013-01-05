<?php
	if ( !defined('CROOT')) {
		require_once('../../engine/connect.php');
		//die('Undefined GT8: a13i2->a114e00->Home');
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/functions/CheckPrivileges.php");
	
	CheckPrivileges( null, null, 'orders/', 1);
	require_once( SROOT .'engine/classes/CardLister.php');
	$_SESSION['login']['level']=6;
	class Orders extends CardLister {
		public $name		= 'orders';
		public $orderFilter	= array(
			array("id-desc", "natural", 'o.id DESC')
		);
		
		function __construct() {
			global $GT8;
			
			
			$this->checkActionRequest();
			/***************************************************************************
			*                                 CARD LISTER                              *
			***************************************************************************/
			$this->keywords	= isset($_GET["q"])? str_replace(explode(" ",'" \' % + ? \\ / ^ ~ ` ´ ! $ * '), " ", $_GET["q"]): null;
			if ( $this->keywords) {
				//$this->options['search']	= isset($this->options['search'])? $this->options['search']: array();
				//$this->options['search'][]	= array('o.street, a.district, a.city', utf8_decode($this->keywords));
			}
			parent::CardLister();
			$this->options['addFrom']	= '';
			$this->createSidebar();
			$this->createToolbar();
		}
		private function createToolbar() {
			global $GT8;
			
			$this->addToolbarItemG(array(
				array('Grupos', 'filter-levels', '', '', '', 'Usuários'),
				array('Exibir filtros', 'filter-fields', '', '', '', 'Campos'),
				array('Filtros por data', 'filter-date', '', '', '', 'Datas'),
				array('Filtros por status', 'filter-status', '', '', '', 'Status')
			));
		}
		private function createSidebar() {
			global $GT8;
			
			$groups	= isset($_GET['level'])&&$_GET['level']? explode(',', RegExp($_GET['level'], '[a-zA-Z0-9\-\,\ ]+')): array();
			require_once( SROOT .'engine/functions/CreateComboLevels.php');
			$Levels = CreateComboLevels(200, 'OBJECT', false, true);
			
			$slevel	= '';
			for ($i=0; $i<count($Levels); $i++) {
				$slevel	.= '<li><a class="pager-click'. (in_array($Levels[$i][1], $groups)? ' checked':'') .'" href="?level-'. $Levels[$i][1] .'" >'. $Levels[$i][1] .'</a></li>'. PHP_EOL;
			}
			$this->addSideBarItem( 'Tipos de usuário', 'filter-levels', (CROOT.'imgs/gt8/filter-small.png'), '
				<ul class="checkbox folder" title="Níveis de acesso" >
					'. $slevel .'
				</ul>
			');
			$this->addSideBarItem( 'Filtros', 'filter-fields', (CROOT.'imgs/gt8/filter-small.png'), '
				<label title="Número de pedido" ><span><input id="eFilter-order" type="text" value="'. (isset($_GET['order'])? (htmlentities(utf8_decode($_GET['order']))): '') .'" name="order" class="gt8-update input-rounded-shadowed" /><small>Número de pedido</small></span></label>
				<label title="Nome do cliente" ><span><input id="eFilter-name" type="text" value="'. (isset($_GET['name'])? (htmlentities(utf8_decode($_GET['name']))): '') .'" name="name" class="gt8-update input-rounded-shadowed" /><small>Nome</small></span></label>
				<label title="Estado da entrega" ><span><input id="eFilter-stt" type="text" value="'. (isset($_GET['stt'])? (htmlentities(utf8_decode($_GET['stt']))): '') .'" name="stt" class="gt8-update input-rounded-shadowed" /><small>Estado</small></span></label>
				<label title="Cidade da entrega" ><span><input id="eFilter-city" type="text" value="'. (isset($_GET['city'])? (htmlentities(utf8_decode($_GET['city']))): '') .'" name="city" class="gt8-update input-rounded-shadowed" /><small>Cidade</small></span></label>
				<label title="Login do cliente" ><span><input id="eFilter-login" type="text" value="'. (isset($_GET['login'])? (htmlentities(utf8_decode($_GET['login']))): '') .'" name="login" class="gt8-update input-rounded-shadowed" /><small>Login</small></span></label>
				<label title="CPF do cliente" ><span><input id="eFilter-cpf" type="text" value="'. (isset($_GET['cpf'])? (htmlentities(utf8_decode($_GET['cpf']))): '') .'" name="cpf" class="gt8-update input-rounded-shadowed" /><small>CPF</small></span></label>
				<label title="CPF do cliente" ><span><input id="eFilter-cnpj" type="text" value="'. (isset($_GET['cnpj'])? (htmlentities(utf8_decode($_GET['cnpj']))): '') .'" name="cnpj" class="gt8-update input-rounded-shadowed" /><small>CNPJ</small></span></label>
			');
			$date		= isset($_GET['date'])&&$_GET['date']? RegExp($_GET['date'], '[a-zA-Z0-9\-\,\ \/]+'): '';
			$dateFrom	= isset($_GET['date-from'])&&$_GET['date-from']? RegExp($_GET['date-from'], '[0-9]{2}\/[0-9]{2}\/[0-9]{4}'): '';
			$dateTo		= isset($_GET['date-to'])&&$_GET['date-to']? RegExp($_GET['date-to'], '[0-9]{2}\/[0-9]{2}\/[0-9]{4}'): '';
			$this->addSideBarItem( 'Data', 'filter-date', (CROOT.'imgs/gt8/filter-small.png'), '
				<ul class="checkbox folder" title="Filtro por data" >
					<li><a class="pager-click '. ($date=='today'? 'checked': '') .'" href="?date-today" >Hoje</a></li>
					<li><a class="pager-click '. ($date=='yesterday'? 'checked': '') .'" href="?date-yesterday" >Ontem</a></li>
					<li><a class="pager-click '. ($date=='week'? 'checked': '') .'" href="?date-week" >Esta semana</a></li>
					<li><a class="pager-click '. ($date=='month'? 'checked': '') .'" href="?date-month" >Este mês</a></li>
					<li><a class="pager-click '. ($date=='last7'? 'checked': '') .'" href="?date-last7" >7 dias</a></li>
					<li><a class="pager-click '. ($date=='last30'? 'checked': '') .'" href="?date-last30" >30 dias</a></li>
				</ul>
				<label title="A partir desta data" ><span><input id="eFilter-date-from" type="text" value="'. ($dateFrom? $dateFrom: '') .'" name="date-from" class="gt8-update input-rounded-shadowed" /><small>A partir desta data</small></span></label>
				<label title="Até esta data" ><span><input id="eFilter-date-to" type="text" value="'. ($dateTo? $dateTo: '') .'" name="date-to" class="gt8-update input-rounded-shadowed" /><small>Até esta data</small></span></label>
			');
			$status	= isset($_GET['status'])&&$_GET['status']? explode(',', RegExp($_GET['status'], '[0-9\,]+')): array();
			$this->addSideBarItem( 'Status', 'filter-status', (CROOT.'imgs/gt8/filter-small.png'), '
				<ul class="checkbox folder" title="Status do pedido" >
					<li><a class="pager-click '. (in_array('20', $status)? 'checked': '') .'" href="?status-20" >Confirmação de compra</a></li>
					<li><a class="pager-click '. (in_array('21', $status)? 'checked': '') .'" href="?status-21" >Aguardando pagamento de boleto</a></li>
					<li><a class="pager-click '. (in_array('22', $status)? 'checked': '') .'" href="?status-22" >Pagamento não concluído</a></li>
					<li><a class="pager-click '. (in_array('23', $status)? 'checked': '') .'" href="?status-23" >Em análise de crédito</a></li>
					<li><a class="pager-click '. (in_array('24', $status)? 'checked': '') .'" href="?status-24" >Liberado para entrega</a></li>
					<li><a class="pager-click '. (in_array('25', $status)? 'checked': '') .'" href="?status-25" >Pedido entregue</a></li>
					<li><a class="pager-click '. (in_array('30', $status)? 'checked': '') .'" href="?status-30" >Pedido extraviado</a></li>
					<li><a class="pager-click '. (in_array('31', $status)? 'checked': '') .'" href="?status-31" >Entrega em atraso</a></li>
					<li><a class="pager-click '. (in_array('40', $status)? 'checked': '') .'" href="?status-40" >Atendimento especial</a></li>
				</ul>
			');
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
			$this->options['sql']		= 'orders.list-orders';
			$this->options['format']	= 'GRID';
			$this->options['grid']	= array(
					'id'	=> '<a href="$value$/" >#value#</a>'. PHP_EOL .'								',
					'login'	=> '<a href="?user-$value$" class="pager-click" >#value#</a>'. PHP_EOL .'								'
			);
			//$this->options['debug']	= '';
			$this->options['group']	= '';
			$this->options['gridConf']	= array(
				//format: label|width|type|length|minwidth|maxwidth
				'ID|50|integer',
				'Status|80|string',
				'Data|80|datetime',
				'Valor|80|currency',
				
				'Frete|80|currency',
				'Estado|20|string',
				'Cidade|80|string',
				'Bairro|80|string',
				
				'Usuário|80|string',
				'Login|80|string',
				
				'CPF ou CNPJ|80|string'
			);
			$this->options['select']	= '
				o.id,
				s.ttl AS status,
				DATE_FORMAT(o.creation, "%d%/%m/%Y %H:%i") AS creation,
				o.price_total,
				o.price_freight,
				
				o.a_stt AS stt,
				o.a_city AS city,
				o.a_district AS district,
				
				u.name,
				u.login,
				
				u.cpfcnpj
			';
			
			/*******************************************************************
			*                                 FILTERS                          *
			*******************************************************************/
			$this->options['where']	= '';
			if ( isset($_GET['user']) && $_GET['user']) {
				$gets	= explode(',', RegExp($_GET['user'], '[0-9\,\@\.\-\_a-zA-Z]+'));
				$this->options['where']	.= ' AND u.login IN (';
				for( $i=0; $i<count($gets); $i++) {
					$this->options['where']	.= '"'. $gets[$i] .'",';
				}
				$this->options['where']	= substr($this->options['where'], 0, -1) . ') '. PHP_EOL;
			}
			if ( isset($_GET['level']) && $_GET['level']) {
				$this->options['addFrom']	.= ' INNER JOIN gt8_levels l	ON l.id = u.level'. PHP_EOL;
				$gets	= explode(',', RegExp($_GET['level'], '[0-9\,\@\.\-\_a-zA-Z\ ]+'));
				$this->options['where']	.= ' AND l.name IN (';
				for( $i=0; $i<count($gets); $i++) {
					$this->options['where']	.= '"'. $gets[$i] .'",';
				}
				$this->options['where']	= substr($this->options['where'], 0, -1) . ') '. PHP_EOL;
			}
			if ( isset($_GET['order']) && $_GET['order']) {
				$get	= (integer)$_GET['order'];
				$this->options['where']	.= ' AND o.id = '. $get .' '. PHP_EOL;
			}
			if ( isset($_GET['cpf']) && $_GET['cpf']) {
				$get	= RegExp($_GET['cpf'], '[0-9\.\/\-]{1,18}');
				$this->options['where']	.= ' AND u.cpfcnpj like "'. $get .'%" '. PHP_EOL;
			}
			if ( isset($_GET['name']) && $_GET['name']) {
				$get	= mysql_real_escape_string(substr($_GET['name'], 0, 32));
				$this->options['where']	.= ' AND u.name like "'. $get .'%" '. PHP_EOL;
			}
			if ( isset($_GET['login']) && $_GET['login']) {
				$get	= RegExp(substr($_GET['login'], 0, 32), '[a-zA-Z0-9\_\-\ \@\.]+');
				$this->options['where']	.= ' AND u.login like "%'. $get .'%" '. PHP_EOL;
			}
			if ( isset($_GET['stt']) && $_GET['stt']) {
				$get	= mysql_real_escape_string(substr($_GET['stt'], 0, 2));
				$this->options['where']	.= ' AND o.a_stt like "'. $get .'" '. PHP_EOL;
			}
			if ( isset($_GET['city']) && $_GET['city']) {
				$get	= mysql_real_escape_string(substr($_GET['city'], 0, 32));
				$this->options['where']	.= ' AND o.a_city like "%'. utf8_decode($get) .'%" '. PHP_EOL;
			}
			if ( isset($_GET['cnpj']) && $_GET['cnpj']) {
				$get	= RegExp($_GET['cnpj'], '[0-9\.\/\-]{1,18}');
				$this->options['where']	.= ' AND u.cpfcnpj like "'. $get .'%" '. PHP_EOL;
			}
			if ( isset($_GET['date-from']) && $_GET['date-from']) {
				$get	= explode('/', RegExp($_GET['date-from'], '[0-9]{2}\/[0-9]{2}\/[0-9]{4}'));
				$get	= $get[2].'-'.$get[1].'-'.$get[0];
				$this->options['where']	.= ' AND o.creation >= "'. $get .' 0:0:0" '. PHP_EOL;
			}
			if ( isset($_GET['date-to']) && $_GET['date-to']) {
				$get	= explode('/', RegExp($_GET['date-to'], '[0-9]{2}\/[0-9]{2}\/[0-9]{4}'));
				$get	= $get[2].'-'.$get[1].'-'.$get[0];
				$this->options['where']	.= ' AND o.creation <= "'. $get .' 23:59:59" '. PHP_EOL;
			}
			if ( isset($_GET['date']) && $_GET['date'] == 'today') {
				$this->options['where']	.= ' AND DATE(o.creation) = DATE(NOW())'. PHP_EOL;
			}
			if ( isset($_GET['date']) && $_GET['date'] == 'yesterday') {
				$this->options['where']	.= ' AND YEAR(o.creation) = YEAR(NOW()) AND MONTH(o.creation) = MONTH(NOW()) AND DAY(o.creation) = DAY(DATE_ADD(NOW(), INTERVAL -1 DAY))'. PHP_EOL;
			}
			if ( isset($_GET['date']) && $_GET['date'] == 'month') {
				$this->options['where']	.= ' AND YEAR(o.creation)=YEAR(NOW()) AND MONTH(o.creation)=MONTH(NOW())'. PHP_EOL;
			}
			if ( isset($_GET['date']) && $_GET['date'] == 'week') {
				$this->options['where']	.= '
					AND o.creation BETWEEN
						DATE_FORMAT(NOW() - INTERVAL (DAYOFWEEK(NOW())+5+(1*-7)) DAY, "%Y-%m-%d")
							AND
						DATE_FORMAT(NOW() - INTERVAL (DAYOFWEEK(NOW())-2+(1*-7)) DAY, "%Y-%m-%d")
				'. PHP_EOL;
			}
			if ( isset($_GET['date']) && $_GET['date'] == 'last7') {
				$this->options['where']	.= '
					AND o.creation BETWEEN
						DATE(DATE_ADD(NOW(), INTERVAL -7 DAY)) AND DATE(NOW())
				'. PHP_EOL;
			}
			if ( isset($_GET['date']) && $_GET['date'] == 'last30') {
				$this->options['where']	.= '
					AND o.creation BETWEEN
						DATE(DATE_ADD(NOW(), INTERVAL -30 DAY)) AND DATE(NOW())
				'. PHP_EOL;
			}
			
			if ( isset($_GET['status']) && $_GET['status']) {
				$get	= explode(',', RegExp($_GET['status'], '[0-9\,]{2,32}'));
				$this->options['where']	.= ' AND o.id_stts IN ('. join(',', $get) .') '. PHP_EOL;
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
	}
?>