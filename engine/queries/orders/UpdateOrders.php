<?php
	if ( !defined('CROOT')) {
		die('Undefined connection! (Q40->A114e00::U91a1e)');
	}
	require_once( SROOT .'engine/classes/Update.php');
	
	class UpdateOrders extends Update {
		public $name	= 'orders';
		public $privilegeName	= 'orders/';
		private $isContact	= false;
		private $options	= null;
		
		public function __construct($options) {
			$this->options	= $options;
			$this->id		= $options['id'];
			
			$this->checkWritePrivileges('orders/', '', (isset($_GET['format'])? $_GET['format']: NULL));
			
			if ( $options['field'] === 'id_stts') {
				$idStatus	= (integer)$options['value'];
				
				$Order	= $this->getOrder();
				$this->Update($options);
				
				if ( file_exists( SROOT .'engine/mail/status/'. $idStatus .'.inc')) {
					
					if ( $idStatus == 40) {//liberado para entrega, calcule a data
						$Products	= $this->getProducts();
						$weight		= 0;
						$width		= 0;
						$length		= 0;
						for ( $i=0; $i<count($Products); $i++) {
							$weight		+= isset($Products[$i]['weight'])&&$Products[$i]['weight']? $Products[$i]['weight']: 1000;
							$height		+= isset($Products[$i]['height'])&&$Products[$i]['height']? $Products[$i]['height']: 20;
						}
						//calcule o prazo de entrega
						require_once( SROOT .'engine/queries/address/getCifByZip.php');
						$Zip	= getCifByZip(array(
							'zip'			=> $Order['a_zip'],
							'price'			=> $Order['price_total'],
							'weight'		=> $weight,
							'width'			=> $width,
							'height'		=> $height,
							'length'		=> $length
						));
						$deliveryDate	= $this->getDeliveryDate(null, $Zip['deliveryTime']);
						$options['field']	= 'delivery_expected';
						$options['value']	= $deliveryDate;
						$this->Update($options);
					}
					if ( $idStatus == 29) {//Cancelar pedido
						$Products	= $this->getProducts();
						
						if ( $Order['id_stts'] != 29) {
							for ( $i=0; $i<count($Products); $i++) {
								$crr	= $Products[$i];
								$b	= mysql_query("
									UPDATE
										gt8_explorer
									SET
										stock = stock + {$crr['qty']}
									WHERE
										id	= {$crr['id_explorer']}
								");
								//print("//#message: Estoque atualizado!".PHP_EOL);
							}
						}
					}
					
					
					if ( $idStatus == 21 ) {//aguardando pagamento do boleto
						$Products	= $this->getProducts();
						
						if ( $Order['id_stts'] != 21) {
							for ( $i=0; $i<count($Products); $i++) {
								$crr	= $Products[$i];
								$b	= mysql_query("
									UPDATE
										gt8_explorer
									SET
										stock = stock - {$crr['qty']}
									WHERE
										id	= {$crr['id_explorer']}
								");
								//print("//#message: Estoque atualizado!".PHP_EOL);
							}
						}
					}
					
					
					require_once( SROOT .'engine/mail/Mail.php');
					$m	= new Mail($idStatus, 'OBJECT');
					$m->printAfterSending	= false;
					$m->copyOnDb	= true;
					$this->data['id-order']	= $this->id;
					$this->data['to']		= array( $_SESSION['login']['login'], $_SESSION['login']['name']);
					$m->send($this->data);
				}
			}
		}
		protected function getOrder() {
			$Order	= Pager(array(
				'sql'		=> 'orders.list-orders',
				'addSelect'	=> ', t.type, o.a_zip',
				'addFrom'	=> '
					INNER JOIN gt8_address_type t	ON t.id = o.a_id_type
				',
				'ids'		=> array(
					array('o.id', $this->id)
				),
				'foundRows'	=> 0
			));
			$Order	= $Order['rows'][0];
			
			return $Order;
		}
		protected function getProducts() {
			$Products	= Pager(array(
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
					o.creation AS creation2
				',
				'addFrom'	=> '
					INNER JOIN gt8_orders_items i	ON o.id = i.id_orders
					INNER JOIN gt8_explorer e		ON e.id = i.id_explorer
				',
				'ids'		=> array(
					array('o.id', $this->id)
				)
			));
			return $Products['rows'];
		}
		public function getValue( $field, $value) {
			if ( $field == 'stt') {
				$value	= strtoupper(RegExp($value, '[A-Za-z]{2}'));
			}
			return $value;
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
		protected function getDeliveryDate( $startDate='yyyy-mm-dd', $days) {
			preg_match('/([0-9]{4}).([0-9]{2}).([0-9]{2})/', $startDate, $startDate);
			
			if ( !$startDate || $startDate === 'yyyy-mm-dd') {
				$startDate	= date('Y-m-d');
			}
			
			$startDate	= explode('-', $startDate);
			$hour		= date('G', time());
			
			//pedidos analisados após as 12:00 terá acréscimo de 1 dia
			if ( $hour > 12) {
				$days++;
			}
			$tstart	= mktime( 0,0,0, $startDate[1], $startDate[2], $startDate[0]);
			for ( $i=0; $i<$days; $i++) {
				$tcrr	= ($tstart+(($i+1)*86400));
				if ( date("w", $tcrr) == 6 || date("w", $tcrr) == 0 || $this->isHoliday(date('Y-m-d', $tcrr))) {
					$days++;
				}
				
			}
			$time		= $tstart + 86400 * $days;
			
			return date("d-m-Y", $time);
		}
	}
?>