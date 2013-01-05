<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Alterar endereço | Cadastro</title>
		{{VIEW:head}}
		<link rel="stylesheet" type="text/css" href="{{CROOT}}{{GT8:account.root}}css/account.css" />
		<script type="text/javascript" src="{{CROOT}}{{GT8:account.root}}js/account.js" ></script>
	</head>
	<body class="account order-view" >
		<div id="eMain" >
			{{VIEW:header}}
			{{VIEW:location}}
			<div class="account row-12 resume fluid-12" >
				<h1 class="margin-bottom grid-12" >Pedido de compra nº. {{GT8:order-number-prefix}}{{id}}</h1>
				<div class="grid-12 card bg-graylight shadow-small clearfix-child" >
					{{VIEW:account.menu}}
					<div class="grid-8 marginless bg-white sborder-left unfloat-phone" >
						<div class="padding-medium clearfix-child panel-right" >
							<h2 class="grid-12 status-{{status}}" >Status: ##status##</h2>
							<h3 class="grid-12 margin-bottom border-bottom padding-bottom" >
								<span class="grid-7 alpha" >Valor final da compra: R$ $$price_total$$</span>
								<a href="boleto/" class="grid-5 omega hidden show-{{need-pay-boleto}} href-button href-button-blue line-height-large" ><span>Imprimir Boleto</span></a>
							</h3>
							<h3 class="grid-12 margin-bottom border-bottom padding-bottom" >
								<span class="grid-7 alpha" >Data da compra: //creation\\</span>
							</h3>
							<div class="grid-12 margin-bottom-large" >
								<h5 class="marginless line-height-large margin-bottom border-bottom" >Itens adquiridos</h5>
								{{FOREACH:products}}
								<div class="grid-12 marginless padding-top padding-bottom clearfix-child sumary-item" >
									<div class="grid-3 omega clearfix-child product imgC" >
										<img src="{{CROOT}}{{l_path}}?translate-img=1&small" alt="" />
									</div>
									<div class="grid-9 marginless" >
										<div class="grid-12 marginless margin-bottom-small" >
											<h4 class="grid-7 alpha margin-bottom-none" class="title" >##title##</h4>
											<div class="grid-2 alpha" >R$ $$price$$</div>
											<div class="grid-1 alpha text-align-center" >{{qty}}</div>
											<div class="grid-2 alpha omega text-align-right" >R$ $$subtotal$$</div>
										</div>
										<div class="grid-12 marginless font-size-small border-bottom" >
											<div>Código: {{imgname}}</div>
											<div>Tamanho: {{tamanho}}</div>
											<div>Cor: ##cor##</div>
											<div class="text-capitalize margin-bottom-small" >Marca: ##brand##</div>
										</div>
									</div>
								</div>
								{{/FOREACH}}
							</div>
							<div class="grid-12 margin-bottom-large" >
								<h5 class="marginless border-bottom line-height-large margin-bottom" >Endereço escolhido para entrega</h5>
								<div class="grid-9 margin-left-3 marginless line-height" >
									<div><strong>##type##</strong></div>
									<div><span>##street##, {{number}} - ##district##</span></div>
									<div><span>##city## - {{stt}}</span></div>
									<div class="margin-bottom" ><span>{{zip}}</span></div>
									<div><strong>Frete pago: R$ $$price_freight$$ <small class="font-weight-normal" >(incluído no valor final da compra)</small></strong></div>
								</div>
							</div>
							<div class="grid-12 margin-bottom-large" >
								<h5 class="marginless border-bottom line-height-large margin-bottom" >Forma de Pagamento utilizada</h5>
								{{FOREACH:pays}}
								<div class="grid-12 marginless padding-top padding-bottom clearfix-child sumary-item border-bottom" >
									<div class="grid-3 omega clearfix-child product imgC" >
										<img src="{{CROOT}}imgs/shopping/{{type}}-small.png" alt="" />
									</div>
									<div class="grid-9 marginless" >
										<div class="grid-12 alpha margin-bottom-small" >
											<h4 class="grid-7 alpha margin-bottom-none text-capitalize" class="title" >##type##</h4>
										</div>
										<div class="grid-12 alpha font-size-small" >
											<div>Valor: R$ $$value$$</div>
											<div>Condição: {{condition}}</div>
										</div>
									</div>
								</div>
								{{/FOREACH}}
							</div>
							<div class="height-medium grid" ></div>
							<p class="grid-12 clearfix-child margin-bottom-none omega" >
								<a href="../" class="href-button href-button-blue grid-5 margin-left-7 omega line-height-large" >
									<span>Voltar aos pedidos</span>
								</a>
							</p>
						</div>
					</div>
				</div>
				<div class="clearfix" ></div>
			</div>
			{{VIEW:footer}}
		</div>
	</body>
</html>