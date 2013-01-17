<?php
	if ( !defined('CROOT')) {
		die('Undefined GT8: a1i32->a114e00->e1i1o4->Home');
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/functions/CheckPrivileges.php");
	require_once( SROOT ."engine/classes/Editor.php");
	
	class AdminEditor extends Editor {
		public $name	= 'orders';
		public $tableType	= null;
		
		function __construct() {
			global $GT8;
			$spath	= $this->getSPath('orders/');
			$id		= (integer)RegExp($spath[0], '[0-9]+');
			parent::Editor();
			
			$Pager	= Pager(array(
				'sql'		=> 'orders.list-orders',
				'addSelect'	=> ', t.type, o.a_stt, o.a_city, o.a_district, o.a_street, o.a_number, o.a_zip, o.id_analytics, o.creation AS creation2',
				'addFrom'	=> '
					INNER JOIN gt8_address_type t	ON t.id = o.a_id_type
				',
				'ids'		=> array(
					array('o.id', $id)
				)
			));
			$this->Pager	= $Pager;
			$this->data	= $Pager['rows'][0];
			$this->id	= $id;
			$this->setFields();
			$this->checkActionRequest();
			$this->checkReadPrivileges('orders/');
		}
		public function on404() {
			if ( !$this->id ) {
				parent::on404();
			}
		}
		private function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				switch($_GET['action']) {
					case 'change-status': {
						require_once( SROOT .'engine/queries/orders/UpdateOrders.php');
						$_GET	= array_merge($_GET, array(
							'field'		=> 'id_stts',
							'value'		=> $_GET['status'],
							'id'		=> $this->id,
							'format'	=> isset($_GET['format'])? $_GET['format']: null
						));
						new UpdateOrders($_GET);
						break;
					}
				}
			}
			if ( isset($_GET['format']) && $_GET['format']=='JSON') {
				die();
			}
		}
		public function getLevelArray( $table, $field) {
			require_once( SROOT .'engine/functions/CreateComboLevels.php');
			return CreateComboLevels($this->Pager['rows'][0]['level'], 'OBJECT');
		}
		public function getComboLevel($allow=0) {
			require_once( SROOT.'engine/functions/CreateComboLevels.php');
			return CreateComboLevels($allow);
		}
		public function getServerJSVars() {
			global $GT8;
			$this->jsVars[]	= array('contactsAPath', CROOT.$GT8['admin']['root'].$GT8['admin']['contacts']['root']);
			$this->jsVars[]	= array('accountAPath', CROOT.$GT8['admin']['root'].$GT8['admin']['account']['root']);
			$this->jsVars[]	= array('token', $GT8['account']['token']);
			return parent::getServerJSVars();
		}
		private function setFields() {
			global $GT8;
			
			{//CUSTOMER
				$Pager	= Pager(array(
					'sql'	=> 'users.list',
					'addSelect'	=> '
						, l.pt AS level_pt, u.birth, YEAR(NOW()) - YEAR(birth) AS age, u.document AS rg
					',
					'addFrom'	=> '
						INNER JOIN gt8_levels l ON l.id = u.level
					',
					'ids'	=> array(
						array('u.id', $this->data['id_users'])
					)
				));
				$this->data['customer']	= $Pager['rows'][0];
			}
			{//PRODUCTS
				$Pager	= Pager(array(
					'sql'		=> 'orders.list-orders',
					'addSelect'	=> ',
						i.id_explorer, e.path, e.filename, e.title, i.id_orders, SUBSTRING(e.path, 10) AS l_path,
						i.qty, i.price, (i.price * i.qty) AS subtotal,
						(
							SELECT
								title
							FROM
								gt8_explorer e3
							WHERE
								e3.id = e.id_dir
						) AS title,
						e.filename AS imgname,
						(
							SELECT
								v.value
							FROM
								gt8_explorer_attributes_value v
								JOIN gt8_explorer_attributes a ON a.id = v.id_attributes
							WHERE
								a.attribute = "tamanho" AND v.id_explorer = e.id AND v.id_attributes = a.id 
						) AS tamanho,
						(
							SELECT
								v.value
							FROM
								gt8_explorer_attributes_value v
								JOIN gt8_explorer_attributes a ON a.id = v.id_attributes
							WHERE
								a.attribute = "cor" AND v.id_explorer = e.id_dir AND v.id_attributes = a.id 
						) AS cor,
						SUBSTRING_INDEX(SUBSTRING_INDEX(e.path, "/", 4), "/", -1) AS brand,
						o.creation AS creation2
					',
					'addFrom'	=> '
						INNER JOIN gt8_orders_items i	ON o.id = i.id_orders
						INNER JOIN gt8_explorer e		ON e.id = i.id_explorer
					',
					'ids'		=> array(
						array('o.id', $this->id)
					),
					'foundRows'	=> 1
				));
				$this->data['products']	= $Pager['rows'];
				$expireDate	= $this->getExpireDate($Pager['rows'][0]['creation2'], $GT8['order-boleto-expires']);
			}
			{//PAY
				$Pager	= Pager(array(
					'sql'		=> 'orders.list-pays',
					'ids'		=> array(
						array('p.id_orders', $this->id)
					),
					'foundRows'	=> 1
				));
				$this->data['pays']	= $Pager['rows'];
				$this->data['boleto-vencido']	= '';
				$this->data['need-pay-boleto']	= '0';
				for ( $i=0; $i<count($this->data['pays']); $i++) {
					$this->data['pays'][$i]['condition']	= $this->data['pays'][$i]['parts']>1? 'Parcelado em '. $this->data['pays'][$i]['parts'] .'x sem juros': 'à vista';
					
					if ( $this->data['pays'][$i]['type']=='boleto' && $this->data['id_stts'] == 21) {//21: aguardando pagamento do boleto
						$this->data['need-pay-boleto']	= '1';
						if ( mktime(0,0,0, (integer)date('m', $expireDate), (integer)date('d', $expireDate), (integer)date('Y', $expireDate)) < time()) {
							$this->data['boleto-vencido']	= '1';
							$this->data['need-pay-boleto']	= '1';
						}
					}
				}
			}
			{//CONTACT
				$Pager	= Pager(array(
					'sql'	=> 'users.list-contact',
					'ids'	=> array(
						array('uc.id_users', $this->data['id_users'])
					),
					'order'	=> 'uc.channel, uc.type, uc.value',
					'foundRows'	=> 1
				));
				$this->data['contacts']	= $Pager['rows'];
			}
			{//ANALYTICS
				$Pager	= Pager(array(
					'sql'	=> 'analytics.list-pages',
					'ids'	=> array(
						array('a.id', $this->data['id_analytics'])
					),
					'limit'		=> 100,
					'foundRows'	=> 1
				));
				$this->data['analytics']	= $Pager['rows'];
			}
			{//ORDERS
				$Pager	= Pager(array(
					'sql'		=> 'orders.list-orders',
					'addSelect'	=> ', i.id_explorer, e.path, e.filename, e.title, i.id_orders, SUBSTRING(e.path, 10) AS l_path',
					'addFrom'	=> '
						INNER JOIN gt8_orders_items i	ON o.id = i.id_orders
						INNER JOIN gt8_explorer e		ON e.id = i.id_explorer
					',
					'ids'		=> array(
						array('o.id_users', $this->Pager['rows'][0]['id_users'])
					),
					'group'	=> 'o.id'
				));
				$this->data['orders']	= $Pager['rows'];
				$this->data['orders-total']		= count($Pager['rows']);
				$totalSucceed	= 0;
				for ( $i=0; $i<count($Pager['rows']); $i++) {
					if ( $Pager['rows'][$i]['id_stts'] < 20 || $Pager['rows'][$i]['id_stts'] > 29) {
						$totalSucceed++;
					}
				}
				$this->data['orders-succeed']	= $totalSucceed;
			}
		}
		protected function isHoliday( $date='yyyy/mm/dd') {
			$holidays;
			if ( !$this->holidays) {
				$Pager	= Pager(array(
					'select'	=> 'h.date',
					'from'		=> 'gt8_holidays h',
					'order'		=> 'h.date DESC',
					'foundRows'	=> 1
				));
				$this->holidays	= array();
				foreach( $Pager['rows'] AS $name=>$value) {
					$this->holidays[]	= $value['date'];
				}
			}
			$holidays	= $this->holidays;
			
			$date	= str_replace('/','-', substr($date, 0, 10));
			foreach( $holidays AS $i=>$value) {
				if ( $value === $date) {
					return true;
					break;
				}
			}
			
			return false;
		}
		protected function getExpireDate( $startDate='yyyy-mm-dd', $days) {
			preg_match('/([0-9]{4}).([0-9]{2}).([0-9]{2})/', $startDate, $startDate);
			
			$tstart	= mktime( 0,0,0, $startDate[2], $startDate[3], $startDate[1]);
			for ( $i=0; $i<$days; $i++) {
				$tcrr	= ($tstart+(($i+1)*86400));
				if ( date("w", $tcrr) == 6 || date("w", $tcrr) == 0 || $this->isHoliday(date('Y-m-d', $tcrr))) {
					$days++;
				}
				
			}
			$time		= $tstart + 86400 * $days;
			
			return date("Y-m-d", $time);
		}
	}
?>