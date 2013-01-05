<?php
	require_once( SROOT .'engine/controllers/cart/Cart.php');
	require_once( SROOT .'engine/functions/CheckLogin.php');
	
	class Receipt extends Cart {
		public function __construct() {
			global $spath, $GT8;
			
			if ( !isset($_SESSION['shopping']['last-order'])) {
				header('location: '. CROOT . $GT8['account']['root'] . $GT8['account']['orders']['root']);
				die();
			}
			unset($_SESSION['shopping']['last-order-failed']);
			$this->checkLogin();
			$this->checkActionRequest();
			$this->setFields();
			
			parent::GT8();
		}
		protected function checkActionRequest() {
			if ( isset($_GET['action'])) {
				
				parent::checkActionRequest();
			}
		}
		private function setFields() {
			global $GT8;
			
			$this->data	= array_merge( $this->data, $_SESSION['shopping']['last-order']);
			
			//PRODUCTS
			//obtenha os produtos do objeto Cart ao invés de fazer uma nova consulta por aqui
			$this->data['products']	= array();
			if ( !count($_SESSION['shopping']['last-order']) && count($_SESSION['shopping']['last-order'])==0) {
				header('location: '. CROOT . $GT8['cart']['root']);
				die();
			}
			//->setProducts usa as referências contidas em session.shopping.cart. Portanto, finja que shopping.last-order é shopping.cart ;)
			if ( !isset($_SESSION['shopping']['cart']) || count($_SESSION['shopping']['cart'])==0 ) {
				$_SESSION['shopping']['cart']	= $_SESSION['shopping']['last-order']['items'];
				$this->setProducts();
				unset($_SESSION['shopping']['cart']);
				
			}
			for ( $i=0; $i<count($this->data['products']); $i++) {
				$this->data['products'][$i]['subtotal']	= $this->data['products'][$i]['qty'] * $this->data['products'][$i]['price_boleto'];
			}
			
			//ADDRESS
			require_once( SROOT .'engine/functions/Pager.php');
			$Pager	= Pager(array(
				'sql'	=> 'address.list',
				'ids'	=> array(
					array('a.id', $_SESSION['shopping']['last-order']['id_address'])
				)
			));
			$Pager	= $Pager['rows'][0];
			foreach ( $Pager AS $name=>$value) {
				if ( !isset($this->data[$name]) || empty($this->data[$name])) {
					$this->data[$name]	= $value;
				}
			}
			$this->data['freight']	= $_SESSION['shopping']['last-order']['freight'];
			
			//PAGAMENTO
			$this->data['pays']	= array();
			for( $i=0; $i<count($_SESSION['shopping']['last-order']['pays']); $i++) {
				$this->data['pays'][]	= array(
					'type'		=> $_SESSION['shopping']['last-order']['pays'][$i][0],
					'value'		=> $_SESSION['shopping']['last-order']['pays'][$i][1],
					'parts'		=> $_SESSION['shopping']['last-order']['pays'][$i][2],
					'condition'	=> $_SESSION['shopping']['last-order']['pays'][$i][2]>1? 'Parcelado em '. $_SESSION['shopping']['last-order']['pays'][$i][2] .'x sem juros' :'à vista' 
				);
			}
			for( $i=0; $i<count($this->data['pays']); $i++) {
				switch ( $this->data['pays'][$i]['type']) {
					case 'master': {
						$this->data['pays'][$i]['type']	= 'Master Card';
						break;
					}
					case 'hiper': {
						$this->data['pays'][$i]['type']	= 'Hippercard';
						break;
					}
					case 'dinners': {
						$this->data['pays'][$i]['type']	= 'Dinners Club';
						break;
					}
				}
			}
		}
	}
?>