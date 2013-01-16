<?php
	$products	= '';
	require_once( SROOT .'engine/functions/Pager.php');
	$Address	= Pager(array(
		'sql'	=> 'address.list',
		'ids'	=> array(
			array('a.id', $data['id-address'])
		)
	));
	$Address	= $Address['rows'][0];
	
	for ( $i=0; $i<count($data['products']); $i++) {
		$crr	= $data['products'][$i];
		$products	.= '
						<tr>
							<td style="padding:5px 0;" >'. $crr['title'] .'</td>
							<td style="padding:5px 0; text-align:center; " >'. $crr['qty'] .'</td>
							<td style="padding:5px 10px 5px 0; text-align:right;" >'. number_format($crr['subtotal'],2,',', '.') .'</td>
						</tr>
		';
	}
	$content = "
		<p style='padding:10px; padding-left:20px; ' >
			##message##
		</p>
		<p>Prezado(a) ##name##,</p>
		<p>Seu pedido foi registrado em nosso sistema sob o número <strong>{$GT8['order-number-prefix']}{{id-order}}</strong>, realizado em //now\\\\ e está em análise de crédito junto à operadora do seu cartão de.</p>
		<p>Após aprovação, seu pedido será liberado para entrega.</p>
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
		<p><strong>Valor do Frete</strong>: R$ ".'$$freight$$'."</p>
		<p><strong>Valor total</strong>: R$ ".'$$total_price$$'."</p>
		
		<p><strong>Forma de Pagamento</strong>: Cartão de crédito {{pay-method}}</p>
		
		<p>Para visualizar seu pedido, <a href='http://www.salaodocalcado.com.br/meu-cadastro/pedidos/{$GT8['order-number-prefix']}{$data['id-order']}/'>clique aqui</a></p>
		
		<p>
			<br>&nbsp;<br>
		</p>
		<p style='font-weight:bold;' >DADOS DA ENTREGA</p>
				<span>Endereço: ". $Address['street'] .", ". $Address['number'] ."</span><span>". (!empty($Address['complement'])?'('.$Address['complement'].')':'') ."</span><br />
				<span>Bairro: ". $Address['district'] ."</span><br />
				<span>Cidade: {$Address['city']} - {$Address['stt']}</span><br />
				<span>CEP: ". $Address['zip'] ."</span><br /><br />
				
				<p>As entregas do Salão do Calçado são realizadas de segunda à sexta, das  8h à s 21h. É necessário que haja alguém no endereço escolhido para entrega.</p>
				<p>Em caso de dúvida, estamos à disposição para lhe ajudar pelo número {{phone-number}}, pelo e-mail <a href='mailto:atendimento@salaodocalcado.com.br' >atendimento@salaodocalcado.com.br</a> ou pelo atendimento online no site <a href='http://www.salaodocalcado.com.br/{$GT8['atendimento']['root']}' >www.salaodocalcado.com.br</a>.</p>
		<p>
			<br>&nbsp;<br>
		</p>
		<p>Obrigado por comprar no <a href='http://www.salaodocalcado.com.br/' >Salão do Calçado</a></p>
	";
	$altBody	= '';
?>