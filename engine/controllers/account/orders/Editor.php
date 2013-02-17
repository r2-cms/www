<?php
	if ( !defined('SROOT') ) {
		require_once('../../connect.php');
	}
	
	require_once( SROOT ."engine/controllers/account/Account.php");
	
	class Editor extends Account {
		public function __construct() {
			global $GT8, $paths;
			
			
			$id	= $paths;
			if ( empty($id[count($id)-1])) {
				array_pop($id);
			}
			$this->checkForBoletoEmission();
			$this->id	= (integer)substr($id[count($id)-1], strlen($this->getParam('order-number-prefix','system')));
			$this->data['id']	= $this->id;
			
			$this->checkActionRequest();
			$this->setFields();
		}
		private $holidays;
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
		public function on404() {
			if ( !$this->id) {
				parent::on404();
			}
		}
		private function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				$idLogin	= (integer)$_SESSION['login']['id'];
				
			}
		}
		private function setFields() {
			global $GT8;
			
			require_once(SROOT .'engine/functions/Pager.php');
			$Pager	= Pager(array(
				'sql'		=> 'orders.list-orders',
				'addSelect'	=> ', t.type',
				'addFrom'	=> '
					INNER JOIN gt8_address_type t	ON t.id = o.a_id_type
				',
				'ids'		=> array(
					array('o.id_users', $_SESSION['login']['id']),
					array('o.id', $this->id)
				)
			));
			
			if ( count($Pager['rows']) == 0) {
				$this->id	= 0;
				$this->on404();
			}
			$this->data		= $Pager['rows'][0];
			
			//PRODUCTS
			$now	= mktime(23, 59, 0);
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
					array('o.id_users', $_SESSION['login']['id']),
					array('o.id', $this->id)
				)
			));
			$this->data['products']	= $Pager['rows'];
			$expireDate	= $this->getExpireDate($Pager['rows'][0]['creation2'], $this->getParam('order-boleto-expires', 'system', 0));
			
			//PAY
			$Pager	= Pager(array(
				'sql'		=> 'orders.list-pays',
				'ids'		=> array(
					array('p.id_orders', $this->id)
				)
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
		public function checkForBoletoEmission( ) {
			global $GT8, $paths;
			$path	= $paths;
			if ( empty($path[count($path)-1])) {
				array_pop($path);
			}
			if ( $path[count($path)-1] == 'boleto') {
				require_once( SROOT .'engine/functions/boletor/Boletor.php');
				$orderId	= RegExp($path[count($path)-2], '[a-zA-Z0-9\ \-]+');
				$id			= substr( $orderId, strlen($this->getParam('order-number-prefix', 'system')));
				
				require_once( SROOT .'engine/functions/Pager.php');
				$Order	= Pager(array(
					'sql'	=> 'orders.list-orders',
					'ids'	=> array(
						array('o.id', $id)
					)
				));
				if ( !$Order['rows'] || count($Order['rows'])==0) {
					$this->on404();
				}
				$Order	= $Order['rows'][0];
				
				$Items	= Pager(array(
					'sql'	=> 'orders.list-items',
					'ids'	=> array(
						array('i.id_orders', $Order['id'])
					)
				));
				$Items	= $Items['rows'][0];
				
				$now	= mktime(23, 59, 0);
				$Pays	= Pager(array(
					'sql'		=> 'orders.list-pays',
					'addSelect'	=> ',
						DATE_FORMAT(o.creation, "%d/%m/%Y") AS creation,
						DATE_FORMAT(DATE_ADD(o.creation, INTERVAL 3 DAY), "%d/%m/%Y") AS vencimento',
					'addFrom'	=> '
						INNER JOIN gt8_orders o	ON o.id = p.id_orders
					',
					'ids'	=> array(
						array('p.id_orders', $Order['id'])
					),
					'addWhere'	=> ' AND UNIX_TIMESTAMP(DATE_ADD(o.creation, INTERVAL '. $this->getParam('order-boleto-expires', 'system', 0) .' DAY)) > '. $now,
					'foundRows'	=> 1
				));
				
				if ( !isset($Pays['rows'][0]) || !$Pays['rows'][0]) {
					$this->data['title']	= 'Boleto vencido';
					$this->data['message']	= 'O boleto solicitado expirou e não é mais possível emiti-lo.<br /><br />Por favor, faça uma nova compra.';
					$this->printView(
						SROOT .'engine/views/error.inc',
						$this->data
					);
					die();
				}
				$Pays	= $Pays['rows'][0];
				$Data	= Boletor(array(
					'title'					=> 'Impressão de boleto bancário',
					'carteira'				=> 175,
					'nosso-numero'			=> $id,
					'numero-documento'		=> substr(str_pad($id, 8, '0', STR_PAD_LEFT), 0, 8),
					'data-processamento'	=> $Pays['creation'],
					'vencimento'			=> $Pays['vencimento'],
					'agencia'				=> '1661',
					'conta'					=> '11142',
					'conta-dv'				=> '5',
					'valor-boleto'			=> $Pays['value'],
					'descontos'				=> '',
					'outras-deducoes'		=> '',
					'valor-cobrado'			=> '',
					'outros-acrescimos'		=> '',
					'mora-multa'			=> '',
					'demonstrativo'			=> 'Referente aos produtos adquiridos no site www.salaodocalcado.com.br',
					'local-pagamento'		=> 'Pagável em qualquer banco do sistema de compensação',
					'cedente'				=> 'Salão do Calçado LTDA',
					
					'z'=>1
				));
				$Data	= array_merge($Data, $Order, $Items, $Pays);
				
				GT8::printView(
					SROOT .'engine/functions/boletor/itau.inc',
					$Data,
					null,
					$this
				);
				die();
			}
		}
	}
?>