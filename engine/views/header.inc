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
								<div class="MenuDropDown-sub hidden" >
									<div id="eMenuHome" class="holder mdd-stop row-12 fluid-12 clearfix-child" >
										<ul class="grid-12 marginless title" ><li><strong>Salão do Calçado</strong></li></ul>
										<div class="grid-12 marginless viewer single border-left" >
											<a href="{{CROOT}}" class="home-info text-decoration-none cursor-default" onclick="return null;" >
												<span class="content padding" >
													O Salão do Calçado comercializa somente produtos confeccionados em couro legítimo.
													Não trabalhos com materiais alternativos, como PU.<br /><br />
													Apesar de nossos produtos serem calçados finos, produzidos com materiais de qualidade e de marcas reconhecidas,
													nossos preços são altamernte competitivos, em geral, abaixo da concorrência.<br /><br />
													Todos nossos produtos passam por rigoroso controle de qualidade. <br /><br /><br />
													<small>Salão do Calçado 2013 &copy;</small>
												</span>
											</a>
										</div>
									</div>
								</div>
							</div>
							<div class="top" >
								<a href="{{CROOT}}calcados/" ><strong>Calçados Femininos</strong></a>
								<div class="MenuDropDown-sub hidden" >
									<div id="eMenuFamilies" class="holder mdd-stop row-12 fluid-12 clearfix-child" >
										<ul class="grid-12 marginless title" ><li><strong>Categorias</strong></li></ul>
										<ul class="grid-3 marginless" >
											<li><a href="{{CROOT}}calcados/anabela/" ><span>Anabela</span></a></li>
											<li><a href="{{CROOT}}calcados/botas/" ><span>Botas</span></a></li>
											<li><a href="{{CROOT}}calcados/cap-toe/" ><span>Cap Toe</span></a></li>
											<li><a href="{{CROOT}}calcados/meia-pata/" ><span>Meia Pata</span></a></li>
											<li><a href="{{CROOT}}calcados/peep-toe/" ><span>Peep Toe</span></a></li>
										</ul>
										<ul class="grid-3 marginless border-left" >
											<li><a href="{{CROOT}}calcados/rasteiras/" ><span>Rasteiras</span></a></li>
											<li><a href="{{CROOT}}calcados/sandalias/" ><span>Sandálias</span></a></li>
											<li><a href="{{CROOT}}calcados/sapatilhas/" ><span>Sapatilhas</span></a></li>
											<li><a href="{{CROOT}}calcados/scarpins/" ><span>Scarpins</span></a></li>
											<li><a href="{{CROOT}}calcados/tenis/" ><span>Tênis</span></a></li>
										</ul>
										<div class="grid-6 marginless viewer default border-left" >
											<a href="{{CROOT}}calcados/" class="family-info text-decoration-none cursor-default" onclick="return null;" >
												<strong>Modelo</strong>
												<span class="content" >&nbsp;</span>
											</a>
										</div>
									</div>
								</div>
							</div>
							<div class="top brands" >
								<a href="{{CROOT}}calcados/marcas/" ><strong>Marcas</strong></a>
								<div class="MenuDropDown-sub hidden" >
									<div id="eMenuBrands" class="holder mdd-stop row-12 fluid-12 clearfix-child" >
										<ul class="grid-12 marginless title" ><li><strong>Marcas mais procuradas</strong></li></ul>
										<ul class="grid-3 marginless" >
											{{$this->setBrandsTop10()}}
											{{FOREACH:brands-top-5}}
											<li><a href="{{CROOT}}calcados/marcas/{{filename}}/" ><span>##title##</span></a></li>
											{{/FOREACH}}
										</ul>
										<ul class="grid-3 marginless border-left" >
											{{FOREACH:brands-top-10}}
											<li><a href="{{CROOT}}calcados/marcas/{{filename}}/" ><span>##title##</span></a></li>
											{{/FOREACH}}
										</ul>
										<div class="grid-6 marginless viewer default border-left" >
											<a href="{{CROOT}}calcados/marcas/" class="brand-info text-decoration-none cursor-default" onclick="return false;" >
												<span class="display-block margin-bottom-small" >
													<img src="{{CROOT}}imgs/gt8/blank.gif" />
												</span>
												<span class="content" >&nbsp;</span>
											</a>
										</div>
									</div>
								</div>
							</div>
							<div class="top bolsas" >
								<a href="{{CROOT}}acessorios/bolsas/" ><strong>Bolsas &amp; Acessórios</strong></a>
								<div class="MenuDropDown-sub hidden" >
									<div id="eMenuBolsas" class="holder mdd-stop row-12 fluid-12 clearfix-child" >
										<ul class="grid-12 marginless title" ><li><strong>Acessórios e Bolsas</strong></li></ul>
										<div class="grid-3 marginless" >
											<div class="padding" >
												Confira nossa linha de bolsas, todas confeccionadas em couro legítimo
											</div>
										</div>
										<div class="grid-9 viewer single border-left" >
											<a href="{{CROOT}}acessorios/bolsas/top-couro/grande-marrom/" class="grid-4 small-card text-decoration-none" onclick="return null;" >
												<img src="{{CROOT}}acessorios/bolsas/top-couro/grande-marrom/?translate-img=1&preview" />
											</a>
											<a href="{{CROOT}}acessorios/bolsas/top-couro/verniz-marrom/" class="grid-4 small-card text-decoration-none" onclick="return null;" >
												<img src="{{CROOT}}acessorios/bolsas/top-couro/verniz-marrom/?translate-img=1&preview" />
											</a>
											<a href="{{CROOT}}acessorios/bolsas/top-couro/couro-azul/" class="grid-4 small-card text-decoration-none" onclick="return null;" >
												<img src="{{CROOT}}acessorios/bolsas/top-couro/couro-azul/?translate-img=1&preview" />
											</a>
										</div>
									</div>
								</div>
							</div>
							<div class="top" >
								<a href="{{CROOT}}liquidacao/" ><strong>Liquidação</strong></a>
							</div>
						</div>
					</div>
				</nav>
			</header>
