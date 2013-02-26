<?php
	if ( !defined('SROOT')) {
		die('Not allowed in Qu34ie0.A114e00!');
	}
	/*
		Integer InsertOrder( options)
			Options:
				id_user
				id_address
				price_total
				freight
				status
				items	= [
					[
						idExplorer,
						qty,
						value
					]
				]
				pays	= [
					[
						type			enum(boleto, visa, master, etc)
						value
						parts
					]
				]
				format		OBJECT,JSON
				analytics	true,false
	*/
	require_once( SROOT .'engine/functions/CheckLogin.php');
	function InsertOrder( $options) {
		
		$idUser		= isset($options['id_user'])? (integer)$options['id_user']: $_SESSION['login']['id'];
		$idAddress	= (integer)$options['id_address'];
		$type		= RegExp($options['type'], '[a-zA-Z0-9\ ]+');
		$status		= (integer)$options['status'];
		//$delivery	= RegExp($options['delivery'], '[0-9\-\+\/]+');
		$items		= $options['items'];
		$pays		= $options['pays'];
		$priceTotal	= (float)$options['price_total'];
		$freight	= (float)$options['freight'];
		$format		= isset($options['format'])? $options['format']: 'OBJECT';
		$analytics	= (isset($options['analytics']) && $options['analytics']) || !isset($options['analytics'])? (integer)$_SESSION['analytics']['id']: 0;
		
		if ( !$idUser) {
			if ( $options['format'] == 'JSON') {
				print('//#error: Não foi possível identificar o usuário! Por favor, tente mais tarde.'. PHP_EOL);
			}
			return 'ERROR: invalid user id!';
			
		} else if ( !$idAddress) {
			if ( $format=='JSON') {
				print('//#error: Nenhum endereço foi especificado. Por favor, informe um.'. PHP_EOL);
			}
			return 'ERROR: missing address id';
		
		} else if ( !$items || count($items)==0) {
			if ( $options['format'] == 'JSON') {
				print('//#error: Os produtos do carrinho não estão disponíveis. Por favor, retorne ao carrinho e tente novamente.'. PHP_EOL);
			}
			return 'ERROR: missing street field!';
			
		} else if ( !$pays || count($pays)==0) {
			if ( $options['format'] == 'JSON') {
				print('//#error: Os produtos do carrinho não estão disponíveis. Por favor, retorne ao carrinho e tente novamente.'. PHP_EOL);
			}
			return 'ERROR: missing street field!';
			
		} else if ( !$status) {
			if ( $options['format'] == 'JSON') {
				print('//#error: Especifique o status inicial do pedido!'. PHP_EOL);
			}
			return 'ERROR: missing status id';
			
		}
		
		//delivery date
		//preg_match( "/([0-9]{2}).([0-9]{2}).([0-9]{4})/", $delivery, $delivery);
		//if ( $delivery[1] && $delivery[2] && $delivery[3]) {
		//	$value	= $delivery[3] .'-'. $delivery[2] .'-'. $delivery[1];
		//} else {
		//	if ( $format == "JSON") {
		//		print(PHP_EOL ."//#error: Data de entrega no formato incorreto!". PHP_EOL);
		//	}
		//	return 'ERROR: invalid date format for delivery field!';
		//}
		$payMethod	= 'others';
		if ( $pays[0][0] == 'boleto') {
			$payMethod	= 'boleto';
		} else if ( in_array($pays[0][0], array('visa', 'master', 'mastercard', 'amex', 'sorocred', 'jcb', 'aura', 'diners', 'dinersclub', 'hiper', 'hipercard'))) {
			$payMethod	= 'card';
		} else if ( in_array($pays[0][0], array('pay', 'paypal'))) {
			$payMethod	= 'paypal';
		} else if ( count($pays) > 1) {
			$payMethod	= 'cards';
		}
		
		//ADDRESS
		require_once( SROOT .'engine/functions/Pager.php');
		$Address	= Pager(array(
			'sql'	=> 'address.list',
			'addSelect'	=> ', a.id_type',
			'ids'	=> array(
				array('a.id', $idAddress)
			)
		));
		$Address	= $Address['rows'][0];
		
		//ORDERS
		mysql_query("
			INSERT INTO
				gt8_orders(
					id_users,
					price_total, price_freight,
					id_stts,
					id_analytics,
					
					pay_method,
					
					a_zip, a_stt, a_city,
					a_district, a_street, a_number,
					a_complement, a_reference, a_id_type,
					
					creation, modification
				) VALUES(
					$idUser,
					'$priceTotal', '$freight',
					$status,
					$analytics,
					
					'$payMethod',
					
					'{$Address['zip']}', '{$Address['stt']}', '{$Address['city']}',
					'{$Address['district']}', '{$Address['street']}', '{$Address['number']}',
					'{$Address['complement']}', '{$Address['reference']}', {$Address['id_type']},
					
					NOW(), NOW()
		)") or die( $_SESSION['login']['level']>7? '//#error: Erro na inserção: '. mysql_error().PHP_EOL: '//#error: Erro interno no banco de dados!<br />Por favor, aguarde alguns minutos e tente novamente.'. PHP_EOL);
		
		$idOrder	= mysql_insert_id();
		if ( !$idOrder) {
			if ( $options['format'] == 'JSON') {
				print('//#error: Erro de sistema! Não foi possível registrar o pedido agora.<br />Por favor, aguarde alguns minutos e tente novamente.<br />Se preferir, entre em contato através do atendimento telefônico.'. PHP_EOL);
			}
			return 'ERROR: could not insert into orders!';
		}
		
		//ORDERS_ITEMS
		for ( $i=0; $i<count($items); $i++) {
			$idExplorer	= (integer)$items[$i][0];
			$price		= (float)$items[$i][2];
			$qty		= (integer)$items[$i][1];
			mysql_query("
				INSERT INTO
					gt8_orders_items(
						id_orders,
						id_explorer,
						price,
						qty
					) VALUES(
						$idOrder,
						$idExplorer,
						'$price',
						$qty
			)") or die( $_SESSION['login']['level']>7? '//#error: Erro na inserção do produto no pedido: '. mysql_error().PHP_EOL: '//#error: Erro ao registrar os produtos adquiridos!<br />Por favor, contate o serviço de atendimento ao consumidor.'. PHP_EOL);
		}
		
		//ORDERS_PAY
		for ( $i=0; $i<count($pays); $i++) {
			$type	= RegExp($pays[$i][0], '[a-zA-Z0-9\ ]+');
			$value	= (float)$pays[$i][1];
			$parts	= (integer)$pays[$i][2];
			
			if ( strpos('#'.strtolower($type), 'master') > 0) {
				$type	= 'master';
			} else if ( strpos('#'.strtolower($type), 'visa') > 0) {
				$type	= 'visa';
			} else if ( strpos('#'.strtolower($type), 'diners') > 0) {
				$type	= 'diners';
			} else if ( strpos('#'.strtolower($type), 'hiper') > 0) {
				$type	= 'hiper';
			} else if ( strpos('#'.strtolower($type), 'amex') > 0) {
				$type	= 'amex';
			} else if ( strpos('#'.strtolower($type), 'american') > 0) {
				$type	= 'amex';
			} else if ( strpos('#'.strtolower($type), 'pay') > 0) {
				$type	= 'pay';
			} else if ( strpos('#'.strtolower($type), 'sorocred') > 0) {
				$type	= 'sorocred';
			} else if ( strpos('#'.strtolower($type), 'jcb') > 0) {
				$type	= 'jcb';
			} else if ( strpos('#'.strtolower($type), 'aura') > 0) {
				$type	= 'aura';
			}
			
			mysql_query("
				INSERT INTO
					gt8_orders_pay(
						id_orders,
						type,
						value,
						parts
					) VALUES(
						$idOrder,
						'$type',
						'$value',
						$parts
			)") or die( $_SESSION['login']['level']>7? '//#error: Erro ao inserir pagamento no pedido: '. mysql_error().PHP_EOL: '//#error: Erro ao registrar o pagamento da compra!<br />Por favor, contate o serviço de atendimento ao consumidor.'. PHP_EOL);
		}
		
		if ( $options['format'] === 'JSON') {
			print('//#affected rows: 1!'. PHP_EOL);
			print('//#insert id: '. $idOrder .PHP_EOL);
			print('//#message: Pedido de compra efetuado com sucesso!'. PHP_EOL);
		}
		if ( $_SESSION['login']['level'] > 3 ) {
			require_once( SROOT ."engine/functions/LogAdmActivity.php");
			LogAdmActivity( array(
				"action"	=> "insert",
				"page"		=> "orders/",
				"name"		=> '',
				"value"		=> '',
				"idRef"		=> $idOrder
			));
		}
		
		return $idOrder;
	}
?>