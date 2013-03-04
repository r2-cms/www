<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>{{GT8:cart.pay.title}} | Processo de Compras</title>
		<link rel="stylesheet" type="text/css" href="{{CROOT}}css/checkout.css" />
		{{VIEW:head}}
		<script type="text/javascript" src="{{CROOT}}js/shopping/pay.js" ></script>
		<script type="text/javascript" >
		//<![CDATA[
			
		//]]>
		</script>
	</head>
	<body class="shopping pay" >
		<div id="eMain" >
			{{VIEW:cart.header}}
			{{VIEW:location}}
			<div class="account row-12 fluid-12 resume clearfix-child" >
				<h1 class="margin-bottom grid-6" >{{GT8:cart.pay.title}}</h1>
				{{VIEW:cart.location}}
				<div class="grid-12 bg-white box-shadow margin-bottom-large hidden show-{{GET:erro}} gt8-debug-error" >
					<div class="padding clearfix-child" >
						<h2 class="grid-12" >Não foi possível passar o cartão!</h2>
						<p class="grid-12" >
							Por favor, certifique-se que os dados estejam corretos e tente novamente.
						</p>
						<div class="close" ></div>
					</div>
				</div>
				<div id="ePayMethodChooser" class="grid-12 bg-white box-shadow margin-bottom-large" >
					<div class="padding clearfix-child" >
						<h2 class="grid-12" >Escolha a forma de pagamento desejada</h2>
						<div class="chooser" >
							<div class="item grid-3 bg-light border-radius box-shadow boleto" >
								<div class="border border-radius padding" >
									<strong class="margin-bottom" >Boleto bancário</strong>
									<img src="{{CROOT}}imgs/shopping/boleto-small.png" width="128" height="65" alt="" />
									<span>R$ $$total_boleto$$<span class="hidden">(-{{round({{desconto_boleto_percentual}}, 0)}}%)</span></span>
								</div>
							</div>
							<div class="item grid-2m bg-light border-radius box-shadow master-card" >
								<div class="border padding border-radius" >
									<strong class="margin-bottom" >Master Card</strong>
									<img src="{{CROOT}}imgs/shopping/master-small.png" width="96" height="65" alt="" />
									<span>R$ $$total_price$$</span>
								</div>
							</div>
							<div class="item grid-2m bg-light border-radius box-shadow visa-card" >
								<div class="border padding border-radius" >
									<strong class="margin-bottom" >Visa</strong>
									<img src="{{CROOT}}imgs/shopping/visa-small.png" width="95" height="65" alt="" />
									<span>R$ $$total_price$$</span>
								</div>
							</div>
							<div class="item grid-2m bg-light border-radius box-shadow diners-card" >
								<div class="border padding border-radius" >
									<strong class="margin-bottom" >Diners Club</strong>
									<img src="{{CROOT}}imgs/shopping/diners-small.png" width="96" height="65" alt="" />
									<span>R$ $$total_price$$</span>
								</div>
							</div>
							<div class="item grid-2m bg-light border-radius box-shadow hiper-card" >
								<div class="border padding border-radius" >
									<strong class="margin-bottom" >Hipercard</strong>
									<img src="{{CROOT}}imgs/shopping/hiper-small.png" width="107" height="65" alt="" />
									<span>R$ $$total_price$$</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="eBodyCardHolder" class="grid-12 marginless margin-bottom-large " >
					<div id="eBodyBoleto" class="grid-12 bg-white box-shadow hidden" >
						<form action="?action=pay-boleto" method="post" class="gt8-form-sender" >
							<div class="padding address-holder clearfix-child" >
								<h2 class="grid-12" >Boleto bancário</h2>
								<p class="grid-12" >
									Ao fechar o pedido um boleto será gerado e poderá ser pago na agência bancária de sua preferência ou pela internet.
								</p>
								<p class="grid-12 clearfix-child" >
									<a href="?fechar-pedido" class="gt8-form-post-save gt8-form-post-send gt8formname-pay-boleto href-button href-button-blue grid-4 marginless line-height-large" ><span>Fechar Pedido</span></a>
								</p>
							</div>
						</form>
					</div>
					<div id="eBodyCreditCard" class="grid-12 bg-white box-shadow hidden" >
						<form action="?action=pay-card" method="post" class="gt8-form-sender" >
							<div class="padding clearfix-child" >
								<input type="hidden" name="card-type" class="gt8-form-post" />
								<h2 class="grid-12" >Escolha a bandeira do seu cartão</h2>
								<div class="grid-6" >
									<p class="grid-12 marginless" >
										<label class="input-text prefix-5 grid-7 omega" >
											<strong class="grid-5 text-align-right" >Nome do titular</strong>
											<input type="text" name="card-name" class="grid-12 omega gt8-form-posto" />
											<small>&nbsp;</small>
										</label>
									</p>
									<p class="grid-12 marginless" >
										<label class="input-text prefix-5 grid-7 omega" >
											<strong class="grid-5 text-align-right" >Número do cartão</strong>
											<input type="text" name="card-number" class="grid-12 omega gt8-form-posto" />
											<small>&nbsp;</small>
										</label>
									</p>
									<p class="grid-12 marginless" >
										<label class="input-text prefix-5 grid-7 omega" >
											<strong class="grid-5 text-align-right text-indent-none" >Código de segurança</strong>
											<input type="text" name="security-code" class="grid-4 omega gt8-form-posto text-align-center" />
											<span class="hidden" ><img src="{{CROOT}}imgs/help-mini.png" alt="[ajuda]" width="16" height="16" /></span>
											<small>&nbsp;</small>
										</label>
									</p>
								</div>
								<div class="grid-6" >
									<p class="grid-12 marginless" >
										<label class="input-select prefix-5 grid-7 omega" >
											<strong class="grid-5 text-align-right" >Parcelas</strong>
											<span class="e-select grid-12 omega">
												<select name="parts" class="gt8-form-post" >
													{{COMBO-OPTIONS:card-parts|0|1|3}}
												</select>
												<span class="button group-button"><strong>Mês</strong><img src="{{CROOT}}imgs/gt8/arrow-down-mini.png" alt="" /></span>
											</span>
										</label>
									</p>
									<p class="grid-12 marginless" >
										<label class="input-select prefix-5 grid-4 omega" >
											<strong class="grid-6h text-align-right" >Validade</strong>
											<span class="e-select grid-12 omega">
												<select name="expire-month" class="gt8-form-posto" >
													<option value="0" >Mês</option>
													<option value="01" >01 - Janeiro</option>
													<option value="02" >02 - Fevereiro</option>
													<option value="03" >03 - Março</option>
													<option value="04" >04 - Abril</option>
													<option value="05" >05 - Maio</option>
													<option value="06" >06 - Junho</option>
													<option value="07" >07 - Julho</option>
													<option value="08" >08 - Agosto</option>
													<option value="09" >09 - Setembro</option>
													<option value="10" >10 - Outubro</option>
													<option value="11" >11 - Novembro</option>
													<option value="12" >12 - Dezembro</option>
												</select>
												<span class="button group-button"><strong>Mês</strong><img src="{{CROOT}}imgs/gt8/arrow-down-mini.png" alt="" /></span>
											</span>
										</label>
										<label class="input-select grid-3 omega" >
											<span class="e-select grid-12 omega">
												<select name="expire-year" class="gt8-form-posto" >
													<option value="0" >Ano</option>
													<option value="2012" >2012</option>
													<option value="2013" >2013</option>
													<option value="2014" >2014</option>
													<option value="2015" >2015</option>
													<option value="2016" >2016</option>
													<option value="2017" >2017</option>
													<option value="2018" >2018</option>
													<option value="2019" >2019</option>
													<option value="2020" >2020</option>
													<option value="2021" >2021</option>
													<option value="2022" >2022</option>
												</select>
												<span class="button group-button"><strong>Ano</strong><img src="{{CROOT}}imgs/gt8/arrow-down-mini.png" alt="" /></span>
											</span>
										</label>
									</p>
								</div>
								<p class="grid-12 margin-top-large" >
									<a id="eBtPayCard" href="fechar-pedido/" class="gt8-form-post-save gt8-form-post-send gt8formname-pay-card href-button grid-4 marginless line-height-large" ><span>Fechar Pedido</span></a>
								</p>
							</div>
						</form>
					</div>
				</div>
			</div>
			{{VIEW:footer}}
		</div>
	</body>
</html>