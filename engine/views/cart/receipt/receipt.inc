<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>{{GT8:cart.receipt.title}} | Processo de Compras</title>
		<link rel="stylesheet" type="text/css" href="{{CROOT}}css/checkout.css" />
		{{VIEW:head}}
	</head>
	<body class="shopping receipt" >
		<div id="eMain" >
			{{VIEW:cart.header}}
			{{VIEW:location}}
			<div class="account row-12 fluid-12 resume clearfix-child" >
				<h1 class="margin-bottom grid-6" >
					Parabéns pela sua compra
				</h1>
				{{VIEW:cart.location}}
				{{IF:$this->data['pays'][0]['type'] == 'boleto'}}
				<div class="grid-12 bg-white box-shadow margin-bottom-large line-height-large" >
					<div class="padding clearfix-child" >
						<div class="grid-6" >
							<p>
								Para podermos processar seu pedido, você deverá completar o pagamento do boleto bancário.
							</p>
							<p>
								Assim que o pagamento for realizado, sua compra será despachada o mais rápido possível.
							</p>
							<p>
								<a class="grid-6 href-button href-button-blue line-height-large" href="{{CROOT}}{{GT8:account.root}}{{GT8:account.orders.root}}{{GT8:order-number-prefix}}{{id-order}}/boleto/" ><span>Imprimir Boleto</span></a>
							</p>
						</div>
					</div>
					<div class="grid-12 bg-light border-top clearfix-child marginless" >
						<div class="grid-12" >
							<div class="grid-12" >
								<small>Número do pedido: {{GT8:order-number-prefix}}{{id-order}}</small>
								<a class="float-right link" href="javascript:window.print()" ><small>Imprimir esta página</small></a>
							</div>
						</div>
					</div>
				</div>
				{{/IF}}
				{{IF: $this->data['pays'][0]['type']!= 'boleto'}}
				<div class="grid-12 bg-white box-shadow margin-bottom-large line-height-large" >
					<div class="padding clearfix-child" >
						<div class="grid-6" >
							<p>
								Para sua segurança, seu pedido passará por análise de crédito e será despachado o mais rápido possível
							</p>
						</div>
					</div>
				</div>
				{{/IF}}
				<div class="grid-12 bg-white box-shadow margin-bottom-large" >
					<h5 class="marginless bg-bar line-height-large margin-bottom-none padding-h" >Itens a serem enviados</h5>
					<div class="padding sumary-items clearfix-child" >
						{{FOREACH:products}}
						<div class="grid-12 padding-top padding-bottom clearfix-child sumary-item border-bottom" >
							<div class="grid-3 omega clearfix-child product imgC" >
								<img src="{{CROOT}}{{l_path}}?translate-img=1&small" alt="" />
							</div>
							<div class="grid-9 omega" >
								<div class="grid-12 omega border-bottom margin-bottom-small" >
									<h4 class="grid-7 alpha margin-bottom-none" class="title" >##title##</h4>
									<div class="grid-2" >R$ $$price$$</div>
									<div class="grid-1 text-align-center" >{{qty}}</div>
									<div class="grid-2 omega text-align-right" >R$ $$subtotal$$</div>
								</div>
								<div class="grid-12 font-size-small" >
									<div>Código: {{imgname}}</div>
									<div>Tamanho: {{tamanho}}</div>
									<div>Cor: ##cor##</div>
									<div class="text-capitalize" >Marca: ##brand##</div>
								</div>
							</div>
						</div>
						{{/FOREACH}}
					</div>
				</div>
				<div class="grid-12 bg-white box-shadow margin-bottom-large" >
					<h5 class="marginless bg-bar line-height-large margin-bottom-none padding-h" >Endereço</h5>
					<div class="padding  clearfix-child" >
						<div class="grid-12 marginless" >
							<div class="grid-3" >
								Endereço de entrega
							</div>
							<div class="grid-9 line-height" >
								<div class="margin-bottom" ><strong>##type##</strong></div>
								<div><span>##street##, {{number}} - ##district##</span></div>
								<div><span>##city## - {{stt}}</span></div>
								<div class="margin-bottom" ><span>{{zip}}</span></div>
								<div><strong>Frete: R$ $$freight$$</strong></div>
							</div>
						</div>
					</div>
				</div>
				<div class="grid-12 bg-white box-shadow margin-bottom-large" >
					<h5 class="marginless bg-bar line-height-large margin-bottom-none padding-h" >Pagamento</h5>
					<div class="padding sumary-items clearfix-child" >
						{{FOREACH:pays}}
						<div class="grid-12 padding-top padding-bottom clearfix-child sumary-item border-bottom" >
							<div class="grid-3 omega clearfix-child product imgC" >
								<img src="{{CROOT}}imgs/shopping/{{type}}-small.png" alt="" />
							</div>
							<div class="grid-9 omega" >
								<div class="grid-12 margin-bottom-small" >
									<h4 class="grid-7 alpha margin-bottom-none text-capitalize" class="title" >##type##</h4>
								</div>
								<div class="grid-12 font-size-small" >
									<div>Valor: R$ $$value$$</div>
									<div>Condição: {{condition}}</div>
								</div>
							</div>
						</div>
						{{/FOREACH}}
					</div>
				</div>
				<div class="grid-12 bg-white box-shadow margin-bottom-large" >
					<h5 class="marginless bg-bar line-height-large margin-bottom-none padding-h" >
						Informações
					</h5>
					<div class="padding-large clearfix-child" >
						<p class="grid-6" >
							Você poderá acompanhar a situação do seu pedido através na página <a href="{{CROOT}}{{GT8:account.root}}{{GT8:account.orders.root}}{{GT8:order-number-prefix}}{{id-order}}/" class="link text-lower-case" >{{GT8:account.orders.title}}</a> a qualquer momento. Se preferir entrar em contato com a central de atendimento, ligue no telefone {{GT8:phone-number}}.
						</p>
					</div>
				</div>
			</div>
			{{VIEW:footer}}
		</div>
	</body>
</html>