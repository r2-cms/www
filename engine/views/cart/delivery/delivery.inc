<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>{{GT8:cart.delivery.title}} | Processo de Compras</title>
		{{VIEW:head}}
		<link rel="stylesheet" type="text/css" href="{{CROOT}}css/checkout.css" />
		<script type="text/javascript" src="{{CROOT}}js/shopping/cart.js" ></script>
		<script type="text/javascript" >
		//<![CDATA[
			
		//]]>
		</script>
	</head>
	<body class="shopping delivery" >
		<div id="eMain" >
			{{VIEW:cart.header}}
			{{VIEW:location}}
			<div class="account row-12 fluid-12 resume clearfix-child" >
				<h1 class="margin-bottom grid-6" >{{GT8:cart.delivery.title}}</h1>
				{{VIEW:cart.location}}
				<div class="grid-12 bg-white box-shadow margin-bottom-large" >
					<div class="padding address-holder clearfix-child" >
						<h2 class="grid-12" >Clique no endereço escolhido ou <a class="link" href="{{CROOT}}{{GT8:account.root}}enderecos/novo/" >clique aqui para criar um novo</a></h2>
						{{FOREACH:addresses}}
						<a href="?action=definir-endereco&continuar-com-pagamento&id={{id}}" class="grid-4 card bg-light border-radius text-decoration-none line-height margin-bottom" title="Usar este endereço" >
							<span class="border display-block border-radius" >
								<span class="padding display-block" >
									<strong class="display-block margin-bottom-small" >##type##</strong>
									<span class="display-block" >##street##, {{number}} - ##district##</span>
									<span class="display-block" >##city## - {{stt}}</span>
									<span class="display-block a-zip margin-bottom-small" >{{zip}}</span>
									<small class="display-block" >Frete: R$ $$freight$$</small>
									<small class="display-block" >Prazo: {{deliveryTime}} dias úteis</small>
								</span>
							</span>
						</a>
						{{/FOREACH}}
					</div>
				</div>
				<div class="grid-12 clearfix-child" >
					<a href="{{CROOT}}{{GT8:account.root}}enderecos/novo/" class="href-button href-button grid-2h alpha" ><span>Criar novo endereço</span></a>
					<a href="{{CROOT}}{{GT8:account.root}}enderecos/" class="href-button href-button grid-2h" ><span>Alterar endereço</span></a>
				</div>
			</div>
			{{VIEW:footer}}
		</div>
	</body>
</html>