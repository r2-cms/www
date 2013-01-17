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
	$to	= array($Order['login'], $Order['name']);
	$data['name']			= $Order['name'];
	$data['creation']		= $Order['creation'];
	$content = "
		<p style='padding:10px; padding-left:20px; ' >
			##message##
		</p>
		<p>Prezado(a) ##name##,</p>
		<p>Seu pedido, conforme registrado em nosso sistema sob o número <strong>{$GT8['order-number-prefix']}{{id-order}}</strong>, realizado em //creation\\\\, teve a entrega confirmada.</p>
		<p>Foi um grande prazer para nós servi-lo. Esperamos que tenha grande satisfação com seu novo produto.</p>
		&nbsp;
		<p>Para visualizar seu pedido, <a href='http://www.salaodocalcado.com.br/meu-cadastro/pedidos/{$GT8['order-number-prefix']}{$Order['id']}/'>clique aqui</a></p>
		<br>
		<p>Em caso de dúvida, reclamações, elogio, estamos à disposição para lhe atender pelo número {{phone-number}}, pelo e-mail <a href='mailto:atendimento@salaodocalcado.com.br' >atendimento@salaodocalcado.com.br</a> ou pelo atendimento online no site <a href='http://www.salaodocalcado.com.br/{$GT8['atendimento']['root']}' >www.salaodocalcado.com.br</a>.</p>
		<p>
			<br>&nbsp;<br>
		</p>
		<p>Obrigado por comprar no <a href='http://www.salaodocalcado.com.br/' >Salão do Calçado</a></p>
	";
	$altBody	= '';
?>