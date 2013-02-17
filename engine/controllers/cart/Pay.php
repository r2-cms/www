<?php
	require_once( SROOT .'engine/controllers/cart/Cart.php');
	
	class Pay extends Cart {
		public function __construct() {
			global $spath, $GT8;
			
			if ( !isset($_SESSION['shopping']['cart']) || count($_SESSION['shopping']['cart'])==0) {
				header('location: ../');
				die();
			}
			
			if ( !isset($_SESSION['shopping']['address']) || !$_SESSION['shopping']['address']) {
				header('location: ../'. $GT8['shopping']['address']['root']);
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
				global $GT8;
				
				if ( $_GET['action'] == 'pay-boleto') {
					require_once( SROOT .'engine/queries/orders/InsertOrder.php');
					
					$this->setProducts();
					$items	= array();
					for ( $i=0; $i<count($this->data['products']); $i++) {
						$items[]	= array( $this->data['products'][$i]['id'], $this->data['products'][$i]['qty'], $this->data['products'][$i]['price_boleto']);
					}
					$options	= array(
						'id_user'		=> $_SESSION['login']['id'],
						'id_address'	=> $_SESSION['shopping']['address'],
						'items'			=> $items,
						'freight'		=> $_SESSION['shopping']['freight'],
						'price_total'	=> $this->data['total_boleto'] + $_SESSION['shopping']['freight'],
						'status'		=> 21,
						'pays'			=> array(
							array('boleto', $this->data['total_boleto'] + $_SESSION['shopping']['freight'],1)
						)
					);
					$idOrder	= InsertOrder($options);
					$options['id-order']	= $idOrder;
					
					if ( !$idOrder) {
						if ( isset($_GET_['format']) && $_GET_['format']=='JSON') {
							die('//#error: Não foi possível fechar o pedido neste momento. Aguarde alguns instantes e tente novamente ou contate o serviço de atendimento ao consumidor: '. $this->getParam('phone-comercial') .", ". $this->getParam('opening-hours') . PHP_EOL);
						}
						$this->data['message']	= '
							Não foi possível fechar o pedido neste momento!<br /><br />Aguarde alguns instantes e tente novamente ou contate o serviço de atendimento ao consumidor: '. $this->getParam('phone-comercial') .", ". $this->getParam('opening-hours') .'.
						';
						$this->data['title']	= 'Erro desconhecido';
						$this->printView(
							SROOT .'engine/views/error.inc',
							$this->data
						);
						die();
					}
					
					
					unset($_SESSION['shopping']);
					$this->cookieCart();
					setcookie('cart-items', '', time()+10, '/');
					$_SESSION['shopping']	= array(
						'last-order'	=> $options
					);
					header('location: ../'. $GT8['cart']['receipt']['root']);
					
					require_once( SROOT .'engine/mail/Mail.php');
					$m	= new Mail(21, 'OBJECT');
					$m->printAfterSending	= true;
					$m->copyOnDb	= true;
					$this->data['id-order']	= $idOrder;
					$this->data['to']		= array( $_SESSION['login']['login'], $_SESSION['login']['name']);
					$m->send($this->data);
					
					$m	= new Mail(24, 'OBJECT');
					$m->printAfterSending	= false;
					$this->data['pay-method']	= 'boleto';
					$m->copyOnDb	= false;
					$m->send($this->data);
					
					die();
				}
				
				if ( $_GET['action'] == 'pay-card') {
					require_once( SROOT .'engine/queries/orders/InsertOrder.php');
					
					$_GET['parts']			= (integer)$_GET['parts'];
					$_POST['expire-month']	= RegExp($_POST['expire-month'], '[0-9]{1,2}');
					$_POST['expire-year']	= RegExp($_POST['expire-year'], '[0-9]{2,4}');
					$_POST['card-number']	= RegExp(str_replace('.', '', $_POST['card-number']), '[0-9]{12,19}');
					$_POST['security-code']	= RegExp($_POST['security-code'], '[0-9]{2,4}');
					$_POST['card-name']		= substr(mysql_real_escape_string($_POST['card-name']), 0, 32);
					$_GET['card-type']		= substr(RegExp($_GET['card-type'], '[a-zA-Z\ ]+'), 0, 24);
					
					/***********************************************************
					 *                                                         *
					 *                    INSERT INTO DB.orders                *
					 *                                                         *
					 ***********************************************************/
					$idOrder	= 0;
					$this->setProducts();
					$items	= array();
					for ( $i=0; $i<count($this->data['products']); $i++) {
						$items[]	= array( $this->data['products'][$i]['id'], $this->data['products'][$i]['qty'], $this->data['products'][$i]['price_selling']);
					}
					$options	= array(
						'id_user'		=> $_SESSION['login']['id'],
						'id_address'	=> $_SESSION['shopping']['address'],
						'items'			=> $items,
						'freight'		=> $_SESSION['shopping']['freight'],
						'price_total'	=> $this->data['total_price'] + $_SESSION['shopping']['freight'],
						'status'		=> 22,//pagamento não concluído. Após confirmação (na captura), altere para o próximo passo
						'pays'			=> array(
							array($_GET['card-type'], $this->data['total_price'] + $_SESSION['shopping']['freight'],1)
						)
					);
					if ( isset($_SESSION['shopping']['last-order-failed']) && $_SESSION['shopping']['last-order-failed']) {
						$idOrder	= $_SESSION['shopping']['last-order-failed']['id-order'];
						$options['price_total']	= $_SESSION['shopping']['last-order-failed']['price_total'];
					} else {
						$idOrder	= InsertOrder($options);
						$options['id-order']	= $idOrder;
						$_SESSION['shopping']['last-order-failed']	= $options;
					}
					$options['id-order']	= $idOrder;
					
					if ( $idOrder) {
						require_once( SROOT ."engine/classes/cURL.php");
						/***********************************************************
						 *                                                         *
						 *                    ENVIO DO CARTÃO                      *
						 *                                                         *
						 ***********************************************************/
						$c	= new cURL();
						$response	= $c->post(
							$this->getParam('payment-apfw-path'),
							'&NumeroDocumento='.	$idOrder .
							'&ValorDocumento='.		$options['price_total'] .
							'&QuantidadeParcelas='.	substr('0000'.$_GET['parts'], -2) .
							'&NumeroCartao='. 		$_POST['card-number'] .
							'&MesValidade='. 		$_POST['expire-month'] .
							'&AnoValidade='. 		$_POST['expire-year'] .
							'&CodigoSeguranca='.	$_POST['security-code'] .
							'&EnderecoIPComprador='.$_SERVER['REMOTE_ADDR'] .
							'&NomePortadorCarta='.	$_POST['card-name'] .
							'&Bandeira='. 			strtoupper($_GET['card-type'])
							,
							false
						);
						preg_match_all('/\<([a-z0-9]+)\>([^\<]+)?\<\/[a-z0-9]+\>/i', $response, $matches);
						//print("<h1>Starting...</h1>".PHP_EOL);
						//print("$response". PHP_EOL);
						$response	= array_combine( $matches[1], $matches[2]);
						//print("<pre>". print_r($response, 1) ."</pre>". PHP_EOL);
						if ( $response['TransacaoAprovada'] == 'True') {
							//print("<h1>FIM 1</h1>".PHP_EOL);
							/***********************************************************
							 *                                                         *
							 *                    GATEWAY CONFIRM                      *
							 *                                                         *
							 ***********************************************************/
							$c	= new cURL();
							$response	= $c->post(
								$this->getParam('payment-cap-path') ."?NumeroDocumento=$idOrder",
								'',
								false
							);
							preg_match_all('/\<([a-z0-9]+)\>([^\<]+)?\<\/[a-z0-9]+\>/i', $response, $matches);
							//print("$response". PHP_EOL);
							$response	= array_combine( $matches[1], $matches[2]);
							//print("<pre>". print_r($response, 1) ."</pre>". PHP_EOL);
							if ( strpos('#'.strtolower($response['ResultadoSolicitacaoConfirmacao']), 'confirmado') > 0 ) {
								//altere o status para pedido em análise de crédito
								mysql_query("
									UPDATE
										gt8_orders
									SET
										id_stts = 23
									WHERE
										id = $idOrder
								") or die($_SESSION['login']['level']>7? "//#error: SQL INSERT Error:". mysql_error() . PHP_EOL: '//#error: Erro ao processar a consulta!<br />Por favor, contate o administrador do site.'. PHP_EOL);
								
								//caso haja erros, mesmo assim a página será redirecionada para o recibo
								unset($_SESSION['shopping']);
								$this->cookieCart();
								setcookie('cart-items', '', time()+10, '/');
								$_SESSION['shopping']	= array(
									'last-order'	=> $options
								);
								//die('OK 1');
								header('location: ../'. $GT8['cart']['receipt']['root']);
								
								require_once( SROOT .'engine/mail/Mail.php');
								$m	= new Mail(23, 'OBJECT');
								$m->copyOnDb	= true;
								$this->data['id-order']	= $idOrder;
								$this->data['to']		= array( $_SESSION['login']['login'], $_SESSION['login']['name']);
								$m->send($this->data);
								
								$m	= new Mail(24, 'OBJECT');
								$this->data['pay-method']	= 'cartão de crédito';
								$m->copyOnDb	= false;
								$m->send($this->data);
								
								die();
							}
						}
						//die("<h1>FIM 2</h1>".PHP_EOL);
					}
					/***********************************************************
					 *                                                         *
					 *                    MENSAGENS DE ERRO                    *
					 *                                                         *
					************************************************************/
					if ( isset($_GET_['format']) && $_GET_['format']=='JSON') {
						die('//#error: No momento, não foi possível efetuar o pagamento para esta compra. Confira as informações do cartão e tente novamente ou contate o serviço de atendimento ao consumidor: '. $this->getParam('phone-comercial') .", ". $this->getParam('opening-hours') . PHP_EOL);
					}
					header('location: ./?erro=1');
					die();
				}
				parent::checkActionRequest();
			}
		}
		private function setFields() {
			
			$items			= $_SESSION['shopping']['cart'];
			$totalBoleto	= 0;
			parent::setProducts();
			
			$maxParts	= 0;
			for ( $i=0; $i<count($this->data['products']); $i++) {
				$maxParts	= max( $maxParts, (integer)$this->data['products'][$i]['price_parts']);
			}
			
			$this->data['card-parts']	= array();
			for ( $i=1; $i<$maxParts+1; $i++) {
				$this->data['card-parts'][]	= array( $i, $i, 0);
			}
			//não se esqueça do frete!
			$this->data['total_price']	+= $_SESSION['shopping']['freight'];
			$this->data['total_boleto']	+= $_SESSION['shopping']['freight'];
		}
	}
?>