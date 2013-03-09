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
	$data['delivery_expected']		= $Order['delivery_expected'];
	$orderPrefix			= GT8::getParam('order-number-prefix','system');
	$phoneNumber			= GT8::getParam('phone-comercial') .", ". GT8::getParam('opening-hours');
	$content = "
		<p style='padding:10px; padding-left:20px; ' >
			##message##
		</p>
		<p>Prezado(a) ##name##,</p>
		<p>Seu pedido, conforme registrado em nosso sistema sob o número <strong>{$orderPrefix}{{id-order}}</strong>, realizado em //creation\\\\, foi aprovado e liberado para entrega.</p>
		<p>A data prevista do seu pedido está prevista para o dia //delivery_expected:%d/%m/%Y\\\\.</p>
		<p>É necessário que haja alguém no endereço no dia da entrega. As entregas são realizadas de segunda à sexta-feira, das 8:00 às 21:00h.</p>
		&nbsp;
		<p>Para visualizar seu pedido, <a href='http://www.salaodocalcado.com.br/{$GT8['account']['root']}{$GT8['account']['orders']['root']}{$orderPrefix}{$Order['id']}/'>clique aqui</a></p>
		<br>
		<p style='font-weight:bold;' >DADOS DA ENTREGA</p>
			<span>Endereço: ". utf8_encode($Order['street']) .", ". $Order['number'] ."</span><span>". (!empty($Order['complement'])?'('.$Order['complement'].')':'') ."</span><br />
			<span>Bairro: ". utf8_encode($Order['district']) ."</span><br />
			<span>Cidade: ". utf8_encode($Order['city']) ." - {$Order['stt']}</span><br />
			<span>CEP: {$Order['zip']}</span><br /><br />
		</p>
		<br>
		<p>Em caso de dúvida, estamos à disposição para lhe ajudar pelo número $phoneNumber, pelo e-mail <a href='mailto:atendimento@salaodocalcado.com.br' >atendimento@salaodocalcado.com.br</a> ou pelo atendimento online no site <a href='http://www.salaodocalcado.com.br/{$GT8['atendimento']['root']}' >www.salaodocalcado.com.br</a>.</p>
		<p>
			<br>&nbsp;<br>
		</p>
		<p>Obrigado por comprar no <a href='http://www.salaodocalcado.com.br/' >Salão do Calçado</a></p>
	";
	$altBody	= '';
?>