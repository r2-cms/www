<?php
	require_once( SROOT .'engine/controllers/cart/Cart.php');
	
	class Delivery extends Cart {
		public function __construct() {
			global $spath;
			
			if ( !isset($_SESSION['shopping']['cart']) || count($_SESSION['shopping']['cart'])==0) {
				header('location: ../');
				die();
			}
			
			parent::__construct();
			$this->checkLogin();
			$this->checkActionRequest();
			$this->setFields();
			
			parent::GT8();
		}
		protected function checkActionRequest() {
			if ( isset($_GET['action'])) {
				
				if ( $_GET['action'] == 'definir-endereco') {
					global $GT8;
					$this->setFields();
					$_SESSION['shopping']['address']	= (integer)$_GET['id'];
					
					if ( !$_SESSION['shopping']['address']) {
						if ( isset($_GET_['format']) && $_GET_['format']=='JSON') {
							die('//#error: Endereço inválido. Por favor, corrija quaisquer erros contidos no endereço ou entre em contato com a central de atendimento.'. PHP_EOL);
						}
						$this->data['message']	= '
							Endereço inválido. Por favor, corrija quaisquer erros contidos no endereço ou entre em contato com a central de atendimento para obter auxílio: '. $this->getParam('phone-comercial') .", ". $this->getParam('opening-hours') .'.
						';
						$this->data['title']	= 'Endereço inválido (!ID)';
						$this->printView(
							SROOT .'engine/views/error.inc',
							$this->data
						);
						die();
					}
					
					for ($i=0; $i<count($this->data['addresses']); $i++) {
						if ( $this->data['addresses'][$i]['id'] == $_GET['id']) {
							$_SESSION['shopping']['freight']		= $this->data['addresses'][$i]['freight'];
							$_SESSION['shopping']['deliveryTime']	= $this->data['addresses'][$i]['deliveryTime'];
							break;
						}
					}
					
					header('location: ../'. $GT8['cart']['pay']['root']);
					die();
				}
				parent::checkActionRequest();
			}
		}
		private function setFields() {
			
			require_once(SROOT .'engine/functions/Pager.php');
			$Pager	= Pager(array(
				'sql'	=> 'address.list',
				'ids'	=> array(
					array('a.id_users', $_SESSION['login']['id'])
				)
			));
			$this->data['addresses']	= $Pager['rows'];
			
			$weight	= 0;
			$width	= 0;
			$height	= 0;
			$length	= 0;
			$price	= $this->data['total_price'];
			$price	= 100;
			
			for ( $i=0; $i<count($this->data['products']); $i++) {
				$weight		+= isset($this->data['products'][$i]['weight'])? $this->data['products'][$i]['weight']: 1000;
				$height		+= isset($this->data['products'][$i]['height'])? $this->data['products'][$i]['height']: 20;
			}
			//para não sobrecarregar com várias consultas idênticas, separe os CEPs diferentes
			require_once( SROOT .'engine/queries/address/getCifByZip.php');
			$zips	= array();
			$Zips	= array();
			for ( $i=0; $i<count($this->data['addresses']); $i++) {
				$crrZip	= substr($this->data['addresses'][$i]['zip'], 0,5);
				if ( !in_array($crrZip, $zips)) {
					$zips[]	= $crrZip;
					$Zips[$crrZip]	= getCifByZip(array(
						'zip'			=> $crrZip,
						'price'			=> $price,
						'weight'		=> $weight,
						'width'			=> $width,
						'height'		=> $height,
						'length'		=> $length
					));
					
				}
			}
			for ( $i=0; $i<count($this->data['addresses']); $i++) {
				$this->data['addresses'][$i]['freight']			= $Zips[substr($this->data['addresses'][$i]['zip'], 0, 5)]['freight'];
				$this->data['addresses'][$i]['deliveryTime']	= $Zips[substr($this->data['addresses'][$i]['zip'], 0, 5)]['deliveryTime'];
			}
		}
	}
?>