<?php
	$products	= '';
	{//ORDER
		$Order	= Pager(array(
			'sql'		=> 'orders.list-orders',
			'addSelect'	=> ', t.type, o.delivery_expected',
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
	$to		= array($Order['login'], $Order['name']);
	$data['now']			= date('Y-m-d H:i:s');
	$data['name']			= $Order['name'];
	$data['price_freight']	= $Order['price_freight'];
	$data['price_total']	= $Order['price_total'];
	$data['pay-method']		= $Pay[0]['type'];
	$data['delivery_expected']		= $Order['delivery_expected'];
	$phoneNumber			= GT8::getParam('phone-comercial') .", ". GT8::getParam('opening-hours');
	$content = "
		<p style='padding:10px; padding-left:20px; ' >&nbsp;</p>
		<p>Prezado(a) ##name##,</p>
		<p>Infelizmente teremos que prorrogar o prazo de entrega do seu pedido. A nova data de entrega prevista &eacute; //delivery_expected\\\\.</p>
		<p>
			Apenas 3% das entregas sofrem este tipo de prorrogação. Os motivos podem envolver extravio do produto, endereço não encontrado, ninguém na casa e até roubo.
		</p>
		<p>Estamos com nossa atenção concentrada em sua entrega e providenciaremos que seu pedido ainda seja entregue o quanto antes.</p>
		<p>Pedimos desculpas pelo ocorrido e mas colocamo-nos à disposição para dirimir quaisquer dúvidas, que você talvez tenha, pelo número $phoneNumber, pelo e-mail <a href='mailto:atendimento@salaodocalcado.com.br' >atendimento@salaodocalcado.com.br</a> ou pelo atendimento online no site <a href='http://www.salaodocalcado.com.br/{$GT8['atendimento']['root']}' >www.salaodocalcado.com.br</a>.</p>
		<p>
			<br>&nbsp;<br>
		</p>
		<p>Obrigado por comprar no <a href='http://www.salaodocalcado.com.br/' >Salão do Calçado</a>.</p>
	";
	$altBody	= '';
?>