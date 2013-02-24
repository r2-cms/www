<?php
	$products	= '';
	{//ORDER
		$Order	= Pager(array(
			'sql'		=> 'orders.list-orders',
			'addSelect'	=> ', t.type',
			'addFrom'	=> '
				INNER JOIN gt8_address_type t	ON t.id = o.a_id_type
			',
			'ids'		=> array(
				array('o.id', $data['id-order'])
			),
			'foundRows'	=> 0
		));
		$Order	= $Order['rows'][0];
	}
	{//PRODUCTS
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
				array('o.id', $data['id-order'])
			)
		));
		$Products	= $Products['rows'];
		$products	= '';
		for ( $i=0; $i<count($Products); $i++) {
			$crr	= $Products[$i];
			$products	.= '
							<tr>
								<td style="padding:5px 0;" >'. $crr['title'] .'</td>
								<td style="padding:5px 0; text-align:center; " >'. $crr['qty'] .'</td>
								<td style="padding:5px 10px 5px 0; text-align:right;" >'. number_format($crr['subtotal'],2,',', '.') .'</td>
							</tr>
			';
		}
		$products	= utf8_encode($products);
	}
	{//PAY
		$Pay	= Pager(array(
			'sql'		=> 'orders.list-pays',
			'ids'		=> array(
				array('p.id_orders', $data['id-order'])
			)
		));
		$Pay	= $Pay['rows'][0];
	}
	$to	= array(GT8::getParam( 'bcc-on-mail-order', 'shopping', 0), 'Operacional');
	$data['now']			= date('Y-m-d H:i:s');
	$data['name']			= $Order['name'];
	$data['price_freight']	= $Order['price_freight'];
	$data['price_total']	= $Order['price_total'];
	$data['pay-method']		= $Pay[0]['type'];
	$orderPrefix			= GT8::getParam('order-number-prefix','system');
	$content = "
		<p>Um pedido de compra foi registrado no sistema sob o número <strong>$orderPrefix{{id-order}}</strong>, realizado em //now\\\\ e est&aacute;  aguardando an&&aacute;lise de cr&eacute;dito.</p>
		<p>
			<br>&nbsp;<br>
		</p>
		<p><strong>DADOS DO PEDIDO</strong></p>
		
		<table border='1' bordercolor='#E8E8E8' cellpadding='0' cellspacing='0' style='font-size:12px; text-indent:10px;' >
			<tr align='center' style='font-weight:bold; background:#EEE;' >
				<td width='430' style='padding:5px 0; text-indent:0px;' >Item</td>
				<td width='50' style='padding:5px 0; text-indent:0px;' >Qtde</td>
				<td width='120' style='padding:5px 0; text-indent:0px;' >Vlr Unitário</td>
			</tr>
			$products
		</table>
		<p><strong>Valor do Frete</strong>: R$ ".'$$price_freight$$'."</p>
		<p><strong>Valor total</strong>: R$ ".'$$price_total$$'."</p>
		
		<p><strong>Forma de Pagamento</strong>: {{pay-method}}</p>
		
		<p>Para visualizar o pedido, <a href='http://www.salaodocalcado.com.br/{$GT8['admin']['root']}orders/{$Order['id']}/'>clique aqui</a></p>
		
		<p>
			<br>&nbsp;<br>
		</p>
		<p style='font-weight:bold;' >DADOS DA ENTREGA</p>
			<span>Endereço: ". utf8_encode($Order['street']) .", ". $Order['number'] ."</span><span>". (!empty($Order['complement'])?'('.$Order['complement'].')':'') ."</span><br />
			<span>Bairro: ". utf8_encode($Order['district']) ."</span><br />
			<span>Cidade: ". utf8_encode($Order['city']) ." - {$Order['stt']}</span><br />
			<span>CEP: {$Order['zip']}</span><br /><br />
		</p>
		<br>
	";
	$altBody	= '';
?>