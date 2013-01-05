			<header class="main clearafix-child" >
				<nav class="top top-1 clearfix-child" >
					<div class="row-12 fluid-12 clearfix-child">
						<a class="logo float-left grid-2" href="{{CROOT}}" >
							<span>
								<img width="109" height="69" class="display-block margin-top-small" alt="[logotipo]" src="{{CROOT}}imgs/logo-large.png">
							</span>
						</a>
						<div class="fast-links grid-3 marginless" >
							<img class="ssl-seal grid" src="{{CROOT}}imgs/ssl-seal.png" alt="" width="52" height="52" />
							<span class="grid line-height-large margin-top-mini" >Área segura</span>
							<span class="grid-12 marginless" >Televendas {{GT8:phone-number}}</span>
						</div>
						<div class="grid-6 margin-left-1 omega margin-top" >
							<div class="bts-fast-access grid-12 marginless clearfix-child" >
								<a id="eBtShoppingCart" href="{{CROOT}}{{GT8:cart.root}}" class="grid-4 marginless" >
									<span class="clearfix-child" >
										<img src="{{CROOT}}imgs/cart-small.png" alt="[ícone meu carrinho]" width="30" height="27" />
										<span>{{GT8:cart.title}}</span>
										<strong class="float-right margin-right-m" >{{SESSION:shopping.total-items|}}</strong>
									</span>
								</a>
								<a id="eBtAccountOrders" href="{{CROOT}}{{GT8:account.root}}{{GT8:account.orders.root}}" class="grid-3h marginless" >
									<span class="clearfix-child" >
										<img src="{{CROOT}}imgs/account-orders-small.png" alt="[ícone meus pedidos]" width="30" height="27" />
										<span>{{GT8:account.orders.title}}</span>
									</span>
								</a>
								<a id="eBtAccountHome" href="{{CROOT}}{{GT8:account.root}}" class="grid-4h marginless" >
									<span class="clearfix-child" >
										<img src="{{CROOT}}imgs/account-home-small.png" alt="[ícone meu cadastro]" width="30" height="27" />
										<span>{{SESSION:login.name|Acesse sua conta}}</span>
									</span>
								</a>
								<a id="eBtAccountLeave" href="{{CROOT}}?logout" class="grid {{IF:!$_SESSION['login']['id']}}hidden{{/IF}}" >
									SAIR
								</a>
							</div>
						</div>
					</div>
				</nav>
			</header>
