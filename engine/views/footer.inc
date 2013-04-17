			{{IF:!$_COOKIE['newsletter']}}<div id="eNewsletterCapture" >
				<div class="row-12 fluid-12 clearfix-child" >
					<div class="grid-12 bg-white shadow overflow-hidden" >
						<div class="above fields padding clearfix-child" >
							<div class="grid-3 alpha" >
								<strong class="display-block padding-left padding-top-small" >
									RECEBA promoções em seu e-mail
								</strong>
							</div>
							<label class="grid-3 omega input-button" >
								<input type="text" name="name" value="" />
								<small>Digite seu nome</small>
							</label>
							<label class="grid-6 omega input-button" >
								<input type="text" name="mail" value="" />
								<small>Digite seu e-mail</small>
								<a class="href-button href-button-blue href-button-blue-fixed" href="{{CROOT}}?action=adicionar-newsletter" rel="nofollow" ><span class=" padding-left-medium padding-right-medium" >CADASTRAR</span></a>
							</label>
						</div>
						<img src="{{CROOT}}imgs/mail-regular.png" class="footer-bg" alt="" />
						<div class="above feedback padding line-height-large display-none" ></div>
					</div>
				</div>
			</div>
			{{/IF}}
			<div class="height-large" ></div>
			<footer class="main" >
				<div class="shadow-container block top" >
					<div class="header row-12 fluid-12 block clearfix-child" >
						<div class="grid-2h t-grid-2 mobile-hidden" >
							<h6>Formas de Pagamento</h6>
							<div class="bandeirasC" >
								<img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="|{{CROOT}}imgs/sprite-bandeiras.png|50|0,0" alt="[Cartão Master Card]" />
								<img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="|{{CROOT}}imgs/sprite-bandeiras.png|50|1,0" alt="[Cartão Visa]" />
								<img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="|{{CROOT}}imgs/sprite-bandeiras.png|50|2,0" alt="[Cartão American Express]" />
								<img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="|{{CROOT}}imgs/sprite-bandeiras.png|50|0,1" alt="[Cartão Diners Club]" />
								<img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="|{{CROOT}}imgs/sprite-bandeiras.png|50|1,1" alt="[Cartão Paypal]" />
								<img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="|{{CROOT}}imgs/sprite-bandeiras.png|50|2,1" alt="[Boleto Bancário]" />
								<img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="|{{CROOT}}imgs/sprite-bandeiras.png|50|0,2" alt="[Cartão Hipercard]" />
								<img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="|{{CROOT}}imgs/sprite-bandeiras.png|50|1,2" alt="[Cartão American Express]" />
								<img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="|{{CROOT}}imgs/sprite-bandeiras.png|50|2,2" alt="[Transferência Bradesco]" />
								<img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="|{{CROOT}}imgs/sprite-bandeiras.png|50|0,3" alt="[Transferência Itaú]" />
								<img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="|{{CROOT}}imgs/sprite-bandeiras.png|50|1,3" alt="[Transferência Banco do Brasil]" />
								<img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="|{{CROOT}}imgs/sprite-bandeiras.png|50|2,3" alt="[Diners Club]" />
							</div>
						</div>
						<div class="grid-2h t-grid-2 m-grid-1h" >
							<h6><a href="{{CROOT}}{{GT8:atendimento.root}}" >Atendimento</a></h6>
							<ul>
								<li><a class="link" href="{{CROOT}}{{GT8:atendimento.root}}duvidas-frequentes/" >Dúvidas Frequentes</a></li>
								<li><a class="link" href="{{CROOT}}{{GT8:atendimento.root}}duvidas-frequentes/" >Dúvidas Frequentes</a></li>
								<li><a class="link" href="{{CROOT}}{{GT8:atendimento.root}}trocas-e-devolucoes/" >Trocas e Devoluções</a></li>
								<li><a class="link" href="{{CROOT}}{{GT8:atendimento.root}}prazos-de-entrega/" >Entregas</a></li>
								<li><a class="link" href="{{CROOT}}{{GT8:atendimento.root}}formas-de-pagamento/" >Pagamentos</a></li>
								<li><a class="link" href="{{CROOT}}{{GT8:account.root}}" >Meus Cadastro</a></li>
								<li><a class="link" href="{{CROOT}}{{GT8:account.root}}{{GT8:account.orders.root}}" >Meus Pedidos</a></li>
							</ul>
						</div>
						<div class="grid-2h t-grid-2 m-grid-1h" >
							<h6><a href="{{CROOT}}institucional/" >Institucional</a></h6>
							<ul>
								<li><a class="link" href="{{CROOT}}institucional/sobre-a-empresa/" >Sobre a Empresa</a></li>
								<li><a class="link" href="{{CROOT}}institucional/politica-de-privacidade/" >Política de Privacidade</a></li>
								<li><a class="link" href="{{CROOT}}institucional/seguranca/" >Política de Segurança</a></li>
								<li><a class="link" href="{{CROOT}}institucional/seja-nosso-fornecedor/" >Seja Nosso Fornecedor</a></li>
							</ul>
						</div>
						<div class="grid-2hm t-grid-3 shadowless mobile-hidden" >
							<h6>Certificados</h6>
							<p class="text-light" ><small>Utilizamos a tecnologia SSL da Comodo Secure para transmitir de forma segura todas as informações de compra e cadastro.</small></p>
							<p class="text-light" ><small>Todo o site foi programado com as mais modernas linguagens.</small></p>
							<p class="text-light" ><small>Todo o site é validado segundo o órgão validador W3C</small></p>
						</div>
						<div class="grid-1h t-grid-3 shadowless text-align-right overflow-hidden mobile-hidden" >
							<p class="margin-bottom-small" >
								<img src="{{CROOT}}imgs/icn-comodo.png" alt="[Certificado SSL Comodo Secure]" width="104" height="51" />
							</p>
							<p class="margin-bottom-small" >
								<img src="{{CROOT}}imgs/icn-html5.png" alt="[Certificado do moderno código HTML5]" width="40" height="54" />
							</p>
							<p class="margin-bottom-small" >
								<img src="{{CROOT}}imgs/icn-w3c.png" alt="[Certificado de código padronizado segundo a W3C]" width="100" height="35" />
							</p>
							<p class="margin-bottom-small hidden" >
								<img src="{{CROOT}}imgs/icn-ebit.png" alt="[Certificado de qualidade]" width="87" height="95" />
							</p>
						</div>
					</div>
					<div class="row-12 fluid-12 clearfix-child mobile-hidden" >
						<hr class="grid-12" />
					</div>
					<div class="row-12 fluid-12 block clearfix-child mobile-hidden" >
						<div class="grid-12 text-light" >
							{{$this->H1only4Home()}}
							<small>O <a href="{{CROOT}}" class="link" >Salãodocalcado.com.br</a> é especializado no comércio eletrônico de calçados nobres, de alta qualidade, todos fabricados em couro legítimo. O site busca constantemente a satisfação do consumidor, oferecendo o melhor atendimento possível, rapidez nas entregas, segurança e simplicidade nas compras. Estamos sempre pesquisando tendências na moda feminina para poder lhe oferecer o que há de mais novo e melhor.</small>
							{{$this->H1only4Home(1)}}
						</div>
					</div>
				</div>
				<div class="row-12 fluid-12 clearfix-child bottom" >
					<p class="grid-12 margin-bottom-none row-1" >
						<small class="text-light" >Fotos meramente ilustrativas | Ofertas válidas enquanto durarem nossos estoques | Vendas sujeitas a análise de dados</small>
					</p>
					<p class="grid-12 row-2" >
						<small>
							<a href="{{CROOT}}" >SALÃO DO CALÇADO (C) 2012</a> | Todos os direitos reservados  |  <a href="{{CROOT}}institucional/politica-de-privacidade/" >Privacidade</a>  |  <a href="{{CROOT}}institucional/seguranca/" >Segurança</a>  |  <a href="{{CROOT}}{{GT8:atendimento.root}}" >SAC</a>
						</small>
					</p>
				</div>
			</footer>
			{{VIEW:tracking-ga}}