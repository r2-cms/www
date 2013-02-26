<header class="main clearafix-child" >
				<nav class="top top-1 clearfix-child" >
					<div class="row-12 fluid-12 clearfix-child" >
						<a href="{{CROOT}}./" class="omega logo float-left grid-2 t-grid-2" >
							<span>
								<img src="{{CROOT}}imgs/logo-large.png" alt="[logotipo]" class="display-block margin-top-small" />
							</span>
						</a>
						<div class="fast-links desktop-visible grid-2 marginless" >
							<a href="{{CROOT}}institucional/" class="" >Institucional</a>
							<a href="{{CROOT}}{{GT8:atendimento.root}}" class="" >Atendimento</a>
							<span class="marginless" >{{PARAM:phone-comercial}}</span>
						</div>
						<div class="grid-8 t-grid-4 alpha clearfix-child" >
							<div class="bts-fast-access grid-12 margin-top-small clearfix-child" >
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
							<label class="main-search input-button grid-12 margin-top-small margin-bottom-small" >
								<input type="text" value="{{GT8:search-key-words}}" name="q" />
								<small>Nome, código, descrição, etc</small>
								<a href="?q=" class="href-button" ><span class="padding-left-medium padding-right-medium" >BUSCA</span></a>
							</label>
						</div>
						<div class="clearfix" ></div>
						<div class="fast-links fast-links-tablet desktop-hidden grid-12 marginless text-align-right" >
							<a href="{{CROOT}}institucional/" class="padding-h" >Institucional</a>
							<a href="{{CROOT}}{{GT8:atendimento.root}}" class="padding-h" >Atendimento</a>
							<span class="padding-h" >{{PARAM:phone-comercial}}</span>
						</div>
					</div>
				</nav>
				<nav class="menu bg-light MenuDropDown" >
					<div class="row-12 fluid-12 clearfix-child" >
						<div class="grid-12" >
							<div class="top" >
								<a href="{{CROOT}}" ><strong>Home</strong></a>
							</div>
							<div class="top" >
								<a href="{{CROOT}}calcados/anabela/" ><strong>Anabela</strong></a>
								<!--<div class="MenuDropDown-sub hidden" >
									<div class="holder" >
										<ul>
											<li><a href="{{CROOT}}masculino/calcados/" ><strong>Calçados</strong></a></li>
											<li><a href="{{CROOT}}masculino/calcados/cuturnos/" >Cuturnos</a></li>
											<li><a href="{{CROOT}}masculino/calcados/mocassins/" >Mocassins</a></li>
											<li><a href="{{CROOT}}masculino/calcados/sapatenis/" >Sapatênis</a></li>
											<li><a href="{{CROOT}}masculino/calcados/sapato-casual/" >Sapato Casual</a></li>
											<li><a href="{{CROOT}}masculino/calcados/sapato-social/" >Sapato Social</a></li>
											<li><a href="{{CROOT}}masculino/calcados/chinelos/" >Chinelos</a></li>
											<li><a href="{{CROOT}}masculino/calcados/tenis/" >Tênis</a></li>
											<li><a href="{{CROOT}}masculino/calcados/esportivos/" >Esportivos</a></li>
										</ul>
										<ul>
											<li><a href="{{CROOT}}masculino/esportivos/" ><strong>Esportivos</strong></a></li>
											<li><a href="{{CROOT}}masculino/esportivos/running/" >Running</a></li>
											<li><a href="{{CROOT}}masculino/esportivos/adventure/" >Adventure</a></li>
											<li><a href="{{CROOT}}masculino/esportivos/skate/" >Skate</a></li>
											<li><a href="{{CROOT}}masculino/esportivos/surf/" >Surf</a></li>
											<li><a href="{{CROOT}}masculino/esportivos/futebol/" >Futebol</a></li>
											<li><a href="{{CROOT}}masculino/esportivos/training/" >Training</a></li>
											<li><a href="{{CROOT}}masculino/esportivos/basquete/" >Basquete</a></li>
										</ul>
										<ul>
											<li><a href="{{CROOT}}masculino/acessorios/" ><strong>Acessórios</strong></a></li>
											<li><a href="{{CROOT}}masculino/acessorios/mochilas/" >Mochilas</a></li>
											<li><a href="{{CROOT}}masculino/acessorios/cintos/" >Cintos</a></li>
											<li><a href="{{CROOT}}masculino/acessorios/oculos/" >Óculos</a></li>
											<li><a href="{{CROOT}}masculino/acessorios/suspensorios/" >Suspensórios</a></li>
											<li><a href="{{CROOT}}masculino/acessorios/canetas/" >Canetas</a></li>
										</ul>
									</div>
								</div>-->
							</div>
							<div class="top" >
								<a href="{{CROOT}}calcados/botas/" ><strong>Botas</strong></a>
							</div>
							<div class="top" >
								<a href="{{CROOT}}calcados/cap-toe/" ><strong>Cap Toe</strong></a>
							</div>
							<div class="top" >
								<a href="{{CROOT}}calcados/meia-pata/" ><strong>Meia Pata</strong></a>
							</div>
							<div class="top" >
								<a href="{{CROOT}}calcados/peep-toe/" ><strong>Peep Toe</strong></a>
							</div>
							<div class="top" >
								<a href="{{CROOT}}calcados/rasteiras/" ><strong>Rasteiras</strong></a>
							</div>
							<div class="top" >
								<a href="{{CROOT}}calcados/sandalias/" ><strong>Sandálias</strong></a>
							</div>
							<div class="top" >
								<a href="{{CROOT}}calcados/sapatilhas/" ><strong>Sapatilhas</strong></a>
							</div>
							<div class="top" >
								<a href="{{CROOT}}calcados/scarpins/" ><strong>Scarpins</strong></a>
							</div>
						</div>
					</div>
				</nav>
			</header>
