<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>{{GT8:cart.title}} | Processo de Compras</title>
		<link rel="stylesheet" type="text/css" href="{{CROOT}}css/checkout.css" />
		{{VIEW:head}}
		<script type="text/javascript" src="{{CROOT}}js/shopping/cart.js" ></script>
		<script type="text/javascript" >
		//<![CDATA[
			
		//]]>
		</script>
	</head>
	<body class="shopping cart" >
		<div id="eMain" >
			{{VIEW:cart.header}}
			{{VIEW:location}}
			<div class="account row-12 fluid-12 resume clearfix-child" >
				<h1 class="margin-bottom grid-6" >{{GT8:cart.title}}</h1>
				{{VIEW:cart.location}}
				<div class="grid-12 margin-bottom bg-white box-shadow clearfix-child sumary hidden-{{no-products}}" >
					<h3 class="clearfix-child margin-bottom-none" >
						<span class="text-align-center font-size-small grid-4" >Produto</span>
						<span class="text-align-center font-size-small grid-3" >Quantidade</span>
						<span class="text-align-center font-size-small grid-2h" >Valor unitário</span>
						<span class="text-align-center font-size-small grid-2h" >Subtotal</span>
					</h3>
				</div>
				<div class="grid-12 margin-bottom bg-white box-shadow hidden-{{no-products}}" >
					{{FOREACH:products}}
					<div class="row padding-top padding-bottom clearfix-child cart-items" >
						<div class="grid-4h omega clearfix-child product" >
							<div class="imgC grid-4h alpha" >
								<a href="{{CROOT}}{{l_path}}" ><img src="{{CROOT}}{{l_path}}?translate-img=1&amp;small" alt="" /></a>
							</div>
							<div class="grid-7h" >
								<h4 class="title" >##title##</h4>
								<div class="code" ><small title="{{id}}" >Código: ##filename##</small></div>
								<div class="size" ><small>Tamanho: {{tamanho}}</small></div>
								<div class="id hidden" >{{id}}</div>
							</div>
						</div>
						<div class="grid-2h alpha qty" >
							<label class="increaser side-by-side" >
								<input title="1|{{stock}}|1" name="cart-items-qty" value="{{qty}}" />
								<a class="up" href="#incrementar" ><span>+</span></a>
								<a class="dw" href="#decrementar" ><span>-</span></a>
							</label>
						</div>
						<div class="grid-2h line-height text-align-center" >
							<small class="unit" >R$ $$price_selling$$</small>
						</div>
						<div class="grid-2h line-height text-align-center" >
							<span class="subtotal" >R$ $$subtotal$$</span>
						</div>
						<div class="bt-close" ></div>
						<div class="e-bts-hrefs" >
							<a class="bt-close-confirm href-button href-button-red" href="#confirmar" ><span>Confirmar</span></a>
							<a class="bt-close-cancel href-button href-button-cancel" href="#confirmar" ><span>Cancelar</span></a>
						</div>
					</div>
					{{/FOREACH}}
				</div>
				<div class="grid-12 margin-bottom-large bg-light box-shadow clearfix-child bar-total hidden-{{no-products}}" >
					<h3 class="clearfix-child margin-bottom-none" >
						<span class="text-align-center grid-4" >Subtotal</span>
						<span class="text-align-center grid-3 qty" >{{total_qty}}</span>
						<span class="text-align-center grid-2h" >&nbsp;</span>
						<span id="eTotal" class="text-align-center grid-2h total" >R$ $$total_price$$</span>
					</h3>
				</div>
				<div class="grid-12 bg-white box-shadow hidden margin-bottom-large show-{{no-products}}" >
					<h2 class="padding-large" >
						Não há itens em seu carrinho de compras.
					</h2>
				</div>
				<div class="grid-12 clearfix-child" >
					<a href="{{CROOT}}calcados/" class="href-button href-button grid-3 marginless" ><span>Continuar comprando</span></a>
					<a href="entrega/" class="href-button href-button-blue margin-left-5 grid-4 marginless line-height-large hidden-{{no-products}}" ><span>Prosseguir</span></a>
				</div>
			</div>
			{{VIEW:footer}}
		</div>
	</body>
</html>