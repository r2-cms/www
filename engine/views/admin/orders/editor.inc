<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Usuário {{login}} | GT8</title>
		{{tag:css|AROOT:orders/css/editor.css}}
		{{VIEW:admin.header-default}}
		{{tag:script|CROOT:js/gt8/Editor.js}}
		{{tag:script|AROOT:js/Modal.js}}
		{{tag:script:{{CROOT}}js/gt8/Pager.js}}
		{{tag:script:{{AROOT}}orders/js/editor.js}}
		{{tag:script|inline:{{JS_SERVER_VARS}}}}
	</head>
	<body class="{{modal-class}}" >
		<div id="eMain" >
			<?php $Editor->printHeader(array(
				"h1" 		=> 'Pedido ({{GT8:order-number-prefix}}){{id}}',
				'directory'	=> $Editor->getDirLocation(),
				'toolbar'	=> $Editor->toolbarItems
			)); ?>
			<div class="Editor-TabbedPaneC body padding-h padding-top" >
				<div class="TabbedPane" >
					<div class="header clearfix-child" >
						<div class="tab" ><div title="Resumo do pedido de compra" >Resumo</div></div>
						<div class="tab" ><div title="Informações relacionadas ao pagamento" >Pagamento</div></div>
						<div class="tab" ><div title="Itens adquiridos" >Produtos</div></div>
						<div class="tab" ><div title="Dados do cliente" >Cliente</div></div>
						<div class="tab" ><div title="Histórico de acesso" >Analytics</div></div>
						<div class="tab" ><div title="Histórico de compras" >Pedidos ({{orders-succeed}}/{{orders-total}})</div></div>
					</div>
					<div class="body" >
						<div class="card resumo line-height-medium input-validation" id="card-sumary" >
							<div class="line bg-head" >
								<div class="col-15" ><strong>Pedido</strong></div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>Status</strong></div>
								<div class="col-9" id="eSumaryStatus" >({{id_stts}}) ##status##</div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>ID</strong></div>
								<div class="col-9" >{{id}}</div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>Data</strong></div>
								<div class="col-9" >//creation:%Y/%m/%d às %H:%i\\</div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>Valor final</strong></div>
								<div class="col-9" >$$price_total$$</div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>Frete</strong></div>
								<div class="col-9" >R$ $$price_freight$$</div>
							</div>
							<div class="line bg-head margin-top" >
								<div class="col-15" ><strong>Produtos adquiridos</strong></div>
							</div>
							{{FOREACH:products}}
							<a href="{{CROOT}}{{l_path}}" class="line" title="Código: {{imgname}}" >
								<span class="col-12" >({{qty}}) ##title## - ##brand##</span>
								<span class="col-3" >R$ $$subtotal$$</span>
							</a>
							{{/FOREACH}}
							<div class="line bg-head margin-top" >
								<div class="col-15" ><strong>Cliente</strong></div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>Nome</strong></div>
								<div class="col-9" ><a href="{{CROOT}}{{GT8:admin.root}}users/{{customer.login}}/" title="ID: {{customer.id}}" >##name## ({{customer.login}})</a></div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>Gênero</strong></div>
								<div class="col-9" >{{customer.genre}}</div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>Idade</strong></div>
								<div class="col-9" >{{customer.age}} anos (//customer.birth:%d/%m/%Y\\)</div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>Documento</strong></div>
								<div class="col-9" >{{customer.document}}</div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>CPF / CNPJ</strong></div>
								<div class="col-9" >{{customer.cpfcnpj}}</div>
							</div>
							<div class="line bg-head margin-top" >
								<div class="col-6" ><strong>Endereço</strong></div>
								<div class="col-9" ></div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>&nbsp;</strong></div>
								<div class="col-9" >
									{{a_stt}} - ##a_city## - ##a_district##.<br />##a_street##, {{a_number}} - {{a_zip}}
								</div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong></strong></div>
								<div class="col-9" ></div>
							</div>
							<div class="clearfix" ></div>
						</div>
						<div class="card input-validation" id="card-pay" >
							{{FOREACH:pays}}
							<div class="line" >
								<div class="col-3 product" >
									<img src="{{CROOT}}imgs/shopping/{{type}}-small.png" alt="" style="max-height:68px; max-width:100px;" />
								</div>
								<div class="col-12 alpha omega clearfix-child" >
									<div class="col-12" >
										<strong>##type##</strong>
									</div>
									<div class="col-12" >
										<div><small>Valor: R$ $$value$$</small></div>
										<div><small>Condição: {{condition}}</small></div>
									</div>
								</div>
							</div>
							{{/FOREACH}}
						</div>
						<div class="card input-validation" id="card-products" >
							{{FOREACH:products}}
							<a class="col-15 border-bottom margin-bottom" href="{{CROOT}}{{l_path}}" >
								<span class="col-3 position-relative product overflow-hidden" >
									<img src="{{CROOT}}{{l_path}}?translate-img=1&amp;small" alt="" style="max-height:112px; max-width:100px;" />
								</span>
								<span class="col-11 clearfix-child" >
									<strong class="col-11" >##title## - ##brand##</strong>
									<span class="col-7 clearfix-child" >
										<span class="display-block text-capitalize margin-bottom-small" >Marca: ##brand##</span>
										<span class="display-block" >Código: {{imgname}}</span>
										<span class="display-block" >Tamanho: {{tamanho}}</span>
										<span class="display-block" >Cor: ##cor##</span>
									</span>
									<span class="grid-4 marginless font-size-small" >
										<span class="display-block" >&nbsp;</span>
										<span class="display-block" >&nbsp;</span>
										<span class="display-block text-align-right" >{{qty}}x R$ $$price$$</span>
										<strong class="display-block text-align-right text-error" >R$ $$subtotal$$</strong>
									</span>
								</span>
								<span class="col-15" >&nbsp;</span>
							</a>
							{{/FOREACH}}
						</div>
						<div class="card resumo line-height" id="card-customer" >
							<div class="line bg-head" >
								<div class="col-6" ><strong>Dados pessoais</strong></div>
								<div class="col-9" ></div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>Nome</strong></div>
								<div class="col-9" ><a href="{{CROOT}}{{GT8:admin.root}}users/{{customer.login}}/" title="ID: {{customer.id}}" >##name## ({{customer.login}})</a></div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>Gênero</strong></div>
								<div class="col-9" >{{customer.genre}}</div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>Idade</strong></div>
								<div class="col-9" >{{customer.age}} anos (//customer.birth\\)</div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>Documento</strong></div>
								<div class="col-9" >{{customer.document}}</div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>CPF / CNPJ</strong></div>
								<div class="col-9" >{{customer.cpfcnpj}}</div>
							</div>
							<div class="line bg-head margin-top-small" >
								<div class="col-6" ><strong>Contato</strong></div>
								<div class="col-9" ></div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>E-mail primário</strong></div>
								<div class="col-9" >{{login}}</div>
							</div>
							{{FOREACH:contacts}}
							<div class="line" >
								<div class="col-6" ><strong>{{channel}} ({{type}})</strong></div>
								<div class="col-9" >{{value}}</div>
							</div>
							{{/FOREACH}}
							<div class="line bg-head margin-top-small" >
								<div class="col-15" ><strong>Endereço escolhido para entrega</strong></div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>CEP</strong></div>
								<div class="col-9" >{{a_zip}}</div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>Estado</strong></div>
								<div class="col-9" >{{a_stt}}</div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>Cidade</strong></div>
								<div class="col-9" >##a_city##</div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>Bairro</strong></div>
								<div class="col-9" >##a_district##</div>
							</div>
							<div class="line" >
								<div class="col-6" ><strong>Logradouro</strong></div>
								<div class="col-9" >##a_street##, {{a_number}}</div>
							</div>
						</div>
						<div class="card resumo line-height" id="card-analytics" >
							<div class="line bg-head" >
								<div class="col-10" ><strong>Página</strong></div>
								<div class="col-3 text-align-right" ><strong>Duração</strong></div>
								<div class="col-2" ><strong>Horário</strong></div>
							</div>
							<div class="line" >
								<div class="col-15" ><strong>(Páginas acessadas na data da compra: //creation:%Y/%m/%d\\)</strong></div>
							</div>
							<div class="font-size-small" >
								{{FOREACH:analytics}}
								<a class="line" href="{{CROOT}}{{page}}" >
									<span class="col-12" ><strong>{{page}}</strong></span>
									<span class="col-1 text-align-right" >{{duration}}s</span>
									<span class="col-2 text-align-right" >//creation:%H:%i:%s\\</span>
								</a>
								{{/FOREACH}}
							</div>
						</div>
						<div class="card cards" id="card-orders" >
							{{FOREACH:orders}}
							<a href="../{{id}}/" class="col-15 card bg-linear-light-gray text-decoration-none border-radius line-height margin-bottom-small cursor-pointer" >
								<span class="col-15 margin-left-none margin-right-none clearfix-child" >
									<strong class="col-5 text-align-left" >
										{{GT8:order-number-prefix}}{{id}}
									</strong>
									<span class="col-9 text-align-right" >Status: ##status##</span>
								</span>
								<span class="col-7 text-align-left" >R$ $$price_total$$</span>
								<span class="col-7 text-align-right" >//creation\\</span>
							</a>
							{{/FOREACH}}
						</div>
					</div>
				</div>
				<section class="modal-info" id="ePublishC">
					<h3 title="8">Gerenciar</h3>
					<h4 title="8">Alterar status para:</h4>
					<div id="eMailActionC" class="line margin-top-small" >
						<a class="full-width href-button href-button-cancel  margin-bottom-small text-align-left text-indent-medium" href="?action=change-status&amp;status=40" id="stt-40" ><span >(40) Liberado para entrega</span></a>
						<a class="full-width href-button href-button-cancel  margin-bottom-small text-align-left text-indent-medium" href="?action=change-status&amp;status=49" id="stt-49" ><span >(49) Pedido entregue</span></a>
						<a class="full-width href-button href-button-cancel  margin-bottom-small text-align-left text-indent-medium" href="?action=change-status&amp;status=30" id="stt-30" ><span >(30) Prazo de entrega prorrogado</span></a>
						<a class="full-width href-button href-button-cancel  margin-bottom-small text-align-left text-indent-medium" href="?action=change-status&amp;status=31" id="stt-31" ><span >(31) Extravio na entrega</span></a>
						<a class="full-width href-button href-button-cancel  margin-bottom-small text-align-left text-indent-medium" href="?action=change-status&amp;status=39" id="stt-39" ><span >(39) Atendimento especial</span></a>
						<a class="full-width href-button href-button-warning margin-bottom-small text-align-left text-indent-medium" href="?action=change-status&amp;status=29" id="stt-29" ><span >(29) Cancelar pedido</span></a>
					</div>
					
				</section>
			</div>
			<?php $Editor->printModalButtons(); ?>
			<footer class="footer-clear-height" >&nbsp;</footer>
			<?php $Editor->printFooter(); ?>
			<!-- CUSTOMS  -->
			<div id="eModalChangeStatus" class="modal input-validation" >
				<h3 class="col-10 text-shadow">
						Confirme a alteração de status
				</h3>
				<label title="minlength:1" class="line required col-10" >
					<span>Atenção, esta ação envia um e-mail de notificação ao cliente e é irreversível!</span>
				</label>
				<label class="line">
					<a class="col-3 href-button href-button-cancel" href="#cancel" ><span>Cancelar</span></a>
					<a class="col-7 href-button href-button-ok" href="#" ><span>OK</span></a>
				</label>
			</div>
		</div>
	</body>
</html>