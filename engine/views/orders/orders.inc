<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Pedidos de compra | Salão do Calçado</title>
		{{VIEW:head}}
		<link rel="stylesheet" type="text/css" href="{{CROOT}}{{GT8:account.root}}css/account.css" />
		<script type="text/javascript" src="{{CROOT}}{{GT8:account.root}}js/account.js" ></script>
		<script type="text/javascript" >
		//<![CDATA[
			
		//]]>
		</script>
	</head>
	<body class="account orders" >
		<div id="eMain" >
			{{VIEW:header}}
			{{VIEW:location}}
			<div class="account row-12 resume fluid-12" >
				<h1 class="margin-bottom grid-12" >Pedidos de compras</h1>
				<div class="grid-12 card bg-graylight shadow-small clearfix-child" >
					{{VIEW:account.menu}}
					<div class="grid-8 marginless bg-white sborder-left unfloat-phone" >
						<div class="padding-medium clearfix-child panel-right" >
							{{IF: count($this->data['orders'])==0}}<h2 class="grid-12" >Você ainda não realizou compras</h2>{{/IF}}
							{{IF: count($this->data['orders'])>0}}<h2 class="grid-12" >Clique em um pedido para visualizar os detalhes</h2>{{/IF}}
							{{FOREACH:orders}}
							<a href="{{PARAM:order-number-prefix}}{{id}}/" class="grid-12 card card-hide-all-but-me text-decoration-none border-radius line-height margin-bottom" >
								<span class="padding clearfix-child border border-radius" >
									<strong class="grid-6" >{{PARAM:order-number-prefix}}{{id}}</strong>
									<small class="grid-6 text-align-right" >Status: ##status##</small>
									<span class="grid-6" >R$ $$price_total$$</span>
									<span class="grid-6 text-align-right" >//creation\\</span>
									<span class="imgsC grid-12" >
										{{foreach-column:id_orders}}
										<img src="{{CROOT}}{{l_path}}?translate-img=1&amp;small" alt="" />
										{{/foreach-column}}
									</span>
								</span>
							</a>
							{{/FOREACH}}
						</div>
					</div>
				</div>
				<div class="clearfix" ></div>
			</div>
			{{VIEW:footer}}
		</div>
	</body>
</html>