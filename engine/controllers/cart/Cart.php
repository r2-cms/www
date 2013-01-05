<?php
	require_once(SROOT .'engine/classes/GT8.php');
	
	GT8::enterSSL();
	
	class Cart extends GT8 implements ICart {
		public $includeCustomView	= '';
		public function __construct() {
			global $spath;
			
			if ( !isset($_SESSION['shopping']['cart'])) {
				$_SESSION['shopping']['cart']	= array();
				
				if ( isset($_COOKIE['cart-items'])) {
					$rows	= explode('|', $_COOKIE['cart-items']);
					for( $i=0; $i<count($rows); $i++) {
						$cols		= explode(',', $rows[$i]);
						$id			= (integer)$cols[0];
						$qty		= (integer)$cols[1];
						if ( $id && $qty ) {
							$_SESSION['shopping']['cart'][]	= array( $id, $qty);
						}
					}
				}
			}
			//fix cart session
			$items	= array();
			for ( $i=0; $i<count($_SESSION['shopping']['cart']); $i++) {
				if ( gettype($_SESSION['shopping']['cart'][$i]) == 'array' ) {
					$id		= $_SESSION['shopping']['cart'][$i][0];
					$qty	= $_SESSION['shopping']['cart'][$i][1];
					if ( $id && $qty) {
						$items[]	= array($id, $qty);
					}
				}
			}
			$_SESSION['shopping']['cart']	= $items;
			$_SESSION['shopping']['total-items']	= count($items);
			
			
			$this->cookieCart();
			$this->checkActionRequest();
			$this->setProducts();
			
			parent::GT8();
		}
		public function checkLogin() {
			require_once( SROOT .'engine/controllers/account/Login.php');
			Login::$includeCustomView	= SROOT .'engine/views/cart/login.inc';
			require_once( SROOT .'engine/functions/CheckLogin.php');
		}
		protected function cookieCart() {
			$items	= '';
			if ( isset($_SESSION['shopping']['cart']) && $_SESSION['shopping']['cart']) {
				for ( $i=0; $i<count($_SESSION['shopping']['cart']); $i++) {
					$crr	= $_SESSION['shopping']['cart'][$i];
					if ( gettype($crr) === 'array') {
						$items	.= '|'. join(',', $crr);
					}
				}
				$items	= substr($items, 1);
			}
			setcookie('cart-items', $items, time()+86400*30,'/');
		}
		protected function checkActionRequest() {
			if ( isset($_GET['action'])) {
				
				if ( $_GET['action'] == 'adicionar-produto' ) {
					$this->addItem($_GET['produto']);
					parent::GT8();
					die();
				}
				
				if ( $_GET['action'] == "remover-produto" ) {
					if ( $this->removeItem($_GET['produto']) ) {
						if ( isset($_GET['format']) && $_GET['format'] == 'JSON') {
							print('//#affected rows: 1'. PHP_EOL);
							print('//#message: Item removido com sucesso!'. PHP_EOL);
						}
					}
					parent::GT8();
					die();
				}
				
				if ( $_GET['action'] == "definir-quantidade" ){
					if ( $this->setQty($_GET['produto'], $_GET['quantidade'])) {
						if ( isset($_GET['format']) && $_GET['format'] == 'JSON') {
							print('//#affected rows: 1'. PHP_EOL);
							print('//#message: Quantidade alterada com sucesso!'. PHP_EOL);
						}
					}
					parent::GT8();
					die();
				}
			}
		}
		public function addItem($idProduct){
			$idProduct	= (integer)$idProduct;
			$qtde = 1;
			$checkProd = false;
			for($i=0; $i<count($_SESSION['shopping']['cart']); $i++){
				if($_SESSION['shopping']['cart'][$i][0] == $idProduct){
					$checkProd = true;
					break;
				}
			}
			
			$this->cookieCart();
			
			if($checkProd == false){
				if ( $idProduct) {
					$_SESSION['shopping']['cart'][]	= array($idProduct, $qtde);
				}
				return true;
			}
		}
		public function setQty($idProduct, $qty){
			//Se o item não existir, processar a função addItem, passando o id do produto.
			$idProduct = (integer)$idProduct;
			$qty = (integer)$qty;
			
			$checkProd = false;
			for($i=0; $i<count($_SESSION['shopping']['cart']); $i++){
				if($_SESSION['shopping']['cart'][$i][0] == $idProduct){
					$_SESSION['shopping']['cart'][$i][1] = $qty;
					$checkProd = true;
					break;
				}
			}
			$this->cookieCart();
			if(isset($_GET['format']) && $_GET['format'] == "JSON"){
				print("//#affected rows:1");
			}
			return $checkProd;
		}
		public function removeItem($idProduct) {
			$items	= array();
			for($i=0; $i<count($_SESSION['shopping']['cart']); $i++){
				if ($_SESSION['shopping']['cart'][$i][0] != $idProduct){
					$items[]	= $_SESSION['shopping']['cart'][$i];
				}
			}
			$_SESSION['shopping']['cart']	= $items;
			unset($_SESSION['shopping']['last-order-failed']);
			$this->cookieCart();
			return true;
		}
		public function setCoupon($key){
			return true;
		}
		public function cleanCart() {
			unset($_SESSION['shopping']);
			print("<h1>". 1123 ."</h1>".PHP_EOL);
			print("<pre>". print_r($_SESSION, 1) ."</pre>". PHP_EOL);
			die();
		}
		public function setProducts(){
			require_once(SROOT .'engine/functions/Pager.php');
			
			$ids = array();
			$cart	= array();
			sort($_SESSION['shopping']['cart']);
			
			for($i=0; $i<count($_SESSION['shopping']['cart']); $i++){
				if((integer)$_SESSION['shopping']['cart'][$i][0]>0){
					array_push($ids, (integer)$_SESSION['shopping']['cart'][$i][0]);
				}
			}
			
			if ( count($ids)>0 ){
				$options	= array(
					'sql'		=> 'explorer.list',
					'addSelect'	=> ',
						SUBSTRING_INDEX(e.path, "/", -2) AS varname,
						e.filename AS imgname,
						e.stock
					',
					'ids'		=> array(
						array(
							'e.id', implode(",", $ids)
						)
					),
					"format"	=> 'OBJECT'
				);
				$options['addSelect']	.= ', SUBSTRING_INDEX(SUBSTRING_INDEX(e.path, "/", 4), "/", -1) AS brand';
				$options['addSelect']	.= ', SUBSTRING_INDEX(e.path, "/", -4) AS varname, e.filename AS imgname';
				$options['addSelect']	.= ', SUBSTRING(e.path, 10) AS l_path';
				$options['addSelect']	.= ",
					(
						SELECT
							v.value
						FROM
							gt8_explorer_attributes_value v
							JOIN gt8_explorer_attributes a ON a.id = v.id_attributes
						WHERE
							a.attribute = 'tamanho' AND v.id_explorer = e.id AND v.id_attributes = a.id 
					) AS tamanho
				";
				$options['addSelect']	.= ",
					(
						SELECT
							price_selling
						FROM
							gt8_explorer e2
						WHERE
							e2.id = e.id_dir
					) AS price_selling
				";
				$options['addSelect']	.= ",
					(
						SELECT
							price_parts
						FROM
							gt8_explorer e2
						WHERE
							e2.id = e.id_dir
					) AS price_parts
				";
				$options['addSelect']	.= ",
					(
						SELECT
							title
						FROM
							gt8_explorer e3
						WHERE
							e3.id = e.id_dir
					) AS title
				";
				$options['addSelect']	.= ",
					(
						SELECT
							v.value
						FROM
							gt8_explorer_attributes_value v
							JOIN gt8_explorer_attributes a ON a.id = v.id_attributes
						WHERE
							a.attribute = 'cor' AND v.id_explorer = e.id_dir AND v.id_attributes = a.id 
					) AS cor
				";
				$Pager	= Pager($options);
				$Pager	= $Pager['rows'];
				
				//BOLETO
				$baseDirPath	= explode('/', $Pager[0]['dirpath']);
				$Boleto	= Pager(array(
					'sql'	=> 'shopping.boleto',
					'where'	=> '
						AND e.dirpath REGEXP "'. $baseDirPath[0] .'/([0-9]+/)?([0-9]+/)?([0-9]+/)?$"
					'
				));
				$Boleto	= $Boleto['rows'];
				
				//PRICES
				$totalQty	= 0;
				$totalPrice	= 0;
				$totalBoleto= 0;
				for ( $i=0; $i<count($Pager); $i++) {
					for ( $j=0; $j<count($_SESSION['shopping']['cart']); $j++) {
						if ( $Pager[$i]['id'] == $_SESSION['shopping']['cart'][$j][0] ) {
							$Pager[$i]['qty']		= $_SESSION['shopping']['cart'][$j][1];
							$Pager[$i]['subtotal']	= $Pager[$i]['qty'] * $Pager[$i]['price_selling'];
						}
					}
					$totalQty	+= $Pager[$i]['qty'];
					$totalPrice	+= $Pager[$i]['subtotal'];
					
					$dirpaths	= explode('/', $Pager[$i]['dirpath']);
					$off		= 0;
					$path		= '';
					$lstJ		= 0;
					$crr		= '';
					for ( $j=0; $j<count($dirpaths)-1; $j++) {
						$crr	.= $dirpaths[$j] .'/';
						for ( $k=$lstJ; $k<count($Boleto); $k++) {
							
							if ( $crr == $Boleto[$k]['dirpath'] && $Boleto[$k]['id']) {
								$lstJ	= $j;
								$off	= $Boleto[$k]['off'];
								break;
							}
						}
						
					}
					$Pager[$i]['price_boleto']	= $Pager[$i]['price_selling'] - $Pager[$i]['price_selling'] * $off;
					$totalBoleto	+= $Pager[$i]['price_boleto'] * $Pager[$i]['qty'];
				}
				$this->data['total_qty']	= $totalQty;
				$this->data['total_price']	= $totalPrice;
				$this->data['total_boleto']	= $totalBoleto;
				$this->data['desconto_boleto_percentual']	= 100 - (100*$totalBoleto) / $totalPrice;
				$this->data['products']		= $Pager;
				$this->data['has-products']	= 1;
				$this->data['no-products']	= 0;
			} else {
				$this->data['total_qty']	= 0;
				$this->data['total_price']	= 0;
				$this->data['total_boleto']	= 0;
				$this->data['desconto_boleto_percentual']	= 0;
				$this->data['products']		= array();
				$this->data['has-products']	= 0;
				$this->data['no-products']	= 1;
			}
		}
	}
	interface ICart {
		public function addItem($idProduct);
		public function setQty($idProduct, $qty);
		public function removeItem($idProduct);
		public function setCoupon($key);
		public function cleanCart();
	}
	interface IGE {
		public function setGE($idProduct, $idGE);
	}
	interface IAddress {
		public function setAddress($id);
	}
	interface IPay {
		public function insertDB();
		public function updateDB();
		public function sendToGateway();
		public function checkPayMethod();
		public function getCartItems();
	}
	interface IConfirm {
		public function getOrder($id);
		public function getAddress($id);
		public function getPay($idOrder);
	}
?>