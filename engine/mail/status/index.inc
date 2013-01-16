<?php
	$content = "
		<p style='padding:10px; padding-left:20px; ' >
			##message##
		</p>
					<p>Prezado(a) ##name##,</p>
					<p>Seu pedido que foi registrado em nosso sistema sob o número {{id-order}} realizado em //now\\, está em análise de crédito junto à operadora do seu cartão de crédito.</p>
					<p>Após aprovação, seu pedido será liberado para entrega.</p>
					
					<p><strong>DADOS DO PEDIDO</strong></p>
					
					<table width='500' border='1' bordercolor='#E8E8E8' cellpadding='0' cellspacing='0'>
						<tr align='center' style='font-weight:bold;' >
							<td width='339'>Item</td>
							<td width='40'>Qtde</td>
							<td width='75'>Vlr Unitário</td>
						</tr>
						{{FOREACH:products}}
						<tr>
							<td>{{title}}</td>
							<td>{{qty}}</td>
							<td>{{price_selling}}</td>
						</tr>
						{{/FOREACH}}
					</table>
					<p><strong>Valor do Frete: R$ ".'$$freight$$'."</strong></p>
					<p><strong>Valor total: R$ ".'$$price$$'."</strong></p>
					
					<p>Forma de Pagamento: $mail_pay_form</p>
					
					<p>Para visualizar seu pedido, <a href='http://www.girafa.com.br/meus-pedidos/'>clique aqui</a></p>
					
					<p style='font-weight:bold; margin-top:30px;'>DADOS DA ENTREGA</p>
					<span>Endereço: ". $linha['logradouro'] .", ". $linha['numero'] ."</span><br />
					<span>Complemento de Endereço: ". $linha['complemento'] ."</span><br />
					<span>Bairro: ". $linha['bairro'] ."</span><br />
					<span>Cidade: $mail_city - ". $linha['estado'] ."</span><br />
					<span>CEP: ". $linha['cep'] ."</span><br />
					
					<p>As entregas do Girafa serão realizadas de segunda a sexta, das  8h à s 21h. É necessário que haja alguém no endereço escolhido para entrega. Não é possível agendar as entregas. Em caso de dúvida, entre em contato conosco pelo telefone ". $phone['ddd'][0] ." ".$phone['phone'][0] .", pelo e-mail <a href='mailto:atendimento@girafa.com.br' >atendimento@girafa.com.br</a> ou pelo atendimento online no site <a href='http://www.girafa.com.br'>www.girafa.com.br</a>.</p>
					<p style='margin-top:30px;'>Obrigado por comprar no Girafa.</p>
					<p style='margin-top:40px;'>Equipe Girafa.com.br</p>
		<p style='margin-top:40px; padding-left:20px; ' ><a href='http://www.salaodocalcado.com.br/' >Salão do Calçado</a></p>
	";
	$altBody	= '';
?>