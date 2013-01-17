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
	$data['name']			= $Order['name'];
	$content = "
		<p style='padding:10px; padding-left:20px; ' >&nbsp;</p>
		<p>Prezado(a) ##name##,</p>
		<p>
			Acabamos de ser notificados que sua encomenda foi extraviada enquanto estava em rota para ser entregue.
		</p>
		<p>
			Em virtude desse ocorrido, teremos que prorrogar o prazo de entrega do seu produto. Você receberá um novo e-mail notificando o novo prazo de entrega do produto.
		</p>
		<p>
			Embora este tipo de incidente esteja fora do alcance da empresa Salão do Calçado, sentimo-nos responsáveis pelo ocorrido e providenciaremos um novo pedido, com os mesmos produtos, e o encaminharemos ao seu endereço.
		</p>
		<p>Estamos com nossa atenção concentrada em sua entrega e providenciaremos que seu novo pedido seja entregue o quanto antes.</p>
		<p>Pedimos desculpas pelo ocorrido e colocamo-nos à disposição para dirimir quaisquer dúvidas, que você talvez tenha, pelo número {{phone-number}}, pelo e-mail <a href='mailto:atendimento@salaodocalcado.com.br' >atendimento@salaodocalcado.com.br</a> ou pelo atendimento online no site <a href='http://www.salaodocalcado.com.br/{$GT8['atendimento']['root']}' >www.salaodocalcado.com.br</a>.</p>
		<p>
			<br>&nbsp;<br>
		</p>
		<p>Obrigado por comprar no <a href='http://www.salaodocalcado.com.br/' >Salão do Calçado</a>.</p>
	";
	$altBody	= '';
?>