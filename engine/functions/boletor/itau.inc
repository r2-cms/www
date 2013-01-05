<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>{{title}}</title>
		{{VIEW:head}}
		<link rel="stylesheet" type="text/css" href="{{CROOT}}css/boleto.css" />
	</head>
	<body class="boleto" >
		<div id="eMain" >
			<header class="main clearafix-child" >
				<nav class="top top-1 clearfix-child">
					<div class="clearfix-child margin-bottom-small" >
						<a href="{{CROOT}}" class="logo float-left grid-2">
							<span>
								<img src="{{CROOT}}imgs/logo-large.png" alt="[logotipo]" class="display-block margin-top-small" width="109" height="69">
							</span>
						</a>
					</div>
				</nav>
			</header>
			<h6 class="text-align-center" >Recibo do sacado</h6>
			<div class="row clearfix-child" >
				<div class="bank-logo" ><img src="{{CROOT}}imgs/boletor/itau.jpg" alt="" width="150" height="40" /></div>
				<div class="bank-dv-code" >341-7</div>
				<div class="linha-digitavel" >{{linha-digitavel}}</div>
			</div>
			<div class="cedente-holder" >
				<div class="row clearfix-child" >
					<div class="cedente" >
						<small class="display-block" >Cedente</small>
						<span class="display-block" >{{cedente}}</span>
					</div>
					<div class="agencia" >
						<small class="display-block" >Agência/Código do Cedente</small>
						<span class="display-block" >{{agencia}}</span>
					</div>
					<div class="especie" >
						<small class="display-block" >Espécie</small>
						<span class="display-block" >R$</span>
					</div>
					<div class="quantidade" >
						<small class="display-block" >Quantidade</small>
						<span class="display-block" >&nbsp;</span>
					</div>
					<div class="nosso-numero" >
						<small class="display-block" >Nosso número</small>
						<span class="display-block text-align-right" >{{nosso-numero}}</span>
					</div>
				</div>
				<div class="row clearfix-child" >
					<div class="num-doc" >
						<small class="display-block" >Número do documento</small>
						<span class="display-block" >{{numero-documento}}</span>
					</div>
					<div class="cpfcnpj" >
						<small class="display-block" >CPF/CNPJ</small>
						<span class="display-block" >015.526.086/0001-68</span>
					</div>
					<div class="vencimento" >
						<small class="display-block" >Vencimento</small>
						<span class="display-block" >{{vencimento}}</span>
					</div>
					<div class="valor-documento" >
						<small class="display-block" >Valor do documento</small>
						<span class="display-block text-align-right" >$$valor-boleto$$</span>
					</div>
				</div>
				<div class="row clearfix-child" >
					<div class="descontos" >
						<small class="display-block" >(-) Desconto / Abatimentos</small>
						<span class="display-block" >{{descontos}}&nbsp;</span>
					</div>
					<div class="outras-deducoes" >
						<small class="display-block" >(-) Outras deduções</small>
						<span class="display-block" >{{outras-deducoes}}&nbsp;</span>
					</div>
					<div class="mora-multa" >
						<small class="display-block" >(+) Mora / Multa</small>
						<span class="display-block" >{{mora-multa}}&nbsp;</span>
					</div>
					<div class="outros-acrescimos" >
						<small class="display-block" >(+) Outros acréscimos</small>
						<span class="display-block text-align-right" >{{outros-acrescimos}}&nbsp;</span>
					</div>
					<div class="valor-cobrado" >
						<small class="display-block" >(=) Valor cobrado</small>
						<span class="display-block text-align-right" >{{valor-cobrado}}&nbsp;</span>
					</div>
				</div>
				<div class="row clearfix-child" >
					<div class="sacado" >
						<small class="display-block" >Sacado</small>
						<span class="display-block" >##name##</span>
					</div>
				</div>
			</div>
			<div class="demonstrativo-holder margin-bottom-large" >
				<div class="clearfix-child text-indent-small" >
					<small>Demonstrativo</small>
				</div>
				<div class="clearfix-child" >
					<p class="padding font-size-small" >
						{{demonstrativo}}
					</p>
				</div>
			</div>
			<div class="separator-dotted text-align-right margin-bottom" >
				<small>Corte na linha pontilhada</small>
			</div>
			<div class="row clearfix-child" >
				<div class="bank-logo" ><img src="{{CROOT}}imgs/boletor/itau.jpg" alt="" width="150" height="40" /></div>
				<div class="bank-dv-code" >341-7</div>
				<div class="linha-digitavel" > {{linha-digitavel}}</div>
			</div>
			<div class="bank-holder" >
				<div class="row clearfix-child" >
					<div class="local-pagamento" >
						<small class="display-block" >Local do pagamento</small>
						<span class="display-block" >{{local-pagamento}}</span>
					</div>
					<div class="vencimento" >
						<small class="display-block" >Vencimento</small>
						<span class="display-block" >{{vencimento}}</span>
					</div>
				</div>
				<div class="row clearfix-child" >
					<div class="cedente" >
						<small class="display-block" >Cedente</small>
						<span class="display-block" >{{cedente}}</span>
					</div>
					<div class="agencia" >
						<small class="display-block" >Agência/Código do Cedente</small>
						<span class="display-block text-align-right" >{{agencia}}</span>
					</div>
				</div>
				<div class="row clearfix-child" >
					<div class="data-do-documento" >
						<small class="display-block" >Data do documento</small>
						<span class="display-block" >&nbsp;</span>
					</div>
					<div class="numero-documento" >
						<small class="display-block" >Nº do documento</small>
						<span class="display-block" >{{numero-documento}}</span>
					</div>
					<div class="especie-doc" >
						<small class="display-block" >Espécie doc.</small>
						<span class="display-block" >{{especie-doc}}&nbsp;</span>
					</div>
					<div class="aceite" >
						<small class="display-block" >Aceite</small>
						<span class="display-block" >{{aceite}}&nbsp;</span>
					</div>
					<div class="data-processamento" >
						<small class="display-block" >Data processamento</small>
						<span class="display-block" >{{data-processamento}}</span>
					</div>
					<div class="nosso-numero" >
						<small class="display-block" >Nosso número</small>
						<span class="display-block text-align-right" >{{nosso-numero}}</span>
					</div>
				</div>
				<div class="row clearfix-child" >
					<div class="uso-do-banco" >
						<small class="display-block" >Uso do banco</small>
						<span class="display-block" >&nbsp;</span>
					</div>
					<div class="carteira" >
						<small class="display-block" >Carteira</small>
						<span class="display-block" >{{carteira}}</span>
					</div>
					<div class="especie" >
						<small class="display-block" >Espécie</small>
						<span class="display-block" >R$</span>
					</div>
					<div class="quantidade" >
						<small class="display-block" >Quantidade</small>
						<span class="display-block" >{{quantidade}}&nbsp;</span>
					</div>
					<div class="valor-documento" >
						<small class="display-block" >Valor Documento</small>
						<span class="display-block" >&nbsp;</span>
					</div>
					<div class="valor-documento-final" >
						<small class="display-block" >(=)Valor documento</small>
						<span class="display-block text-align-right" >$$valor-boleto$$</span>
					</div>
				</div>
			</div>
			<div class="instructions-holder clearfix-child" >
				<div class="instruction" >
					<div><span>Instruções</span></div>
					<div>{{instructions}}</div>
				</div>
				<div class="sumary" >
					<div class="row clearfix-child" >
						<small class="display-block" >(-) Desconto / Abatimentos</small>
						<span class="display-block" >&nbsp;</span>
					</div>
					<div class="row clearfix-child" >
						<small class="display-block" >(-) Outras deduções</small>
						<span class="display-block" >&nbsp;</span>
					</div>
					<div class="row clearfix-child" >
						<small class="display-block" >(+) Mora / Multa</small>
						<span class="display-block" >&nbsp;</span>
					</div>
					<div class="row clearfix-child" >
						<small class="display-block" >(+) Outros acréscimos</small>
						<span class="display-block" >&nbsp;</span>
					</div>
					<div class="row clearfix-child" >
						<small class="display-block" >(=) Valor cobrado</small>
						<span class="display-block" >&nbsp;</span>
					</div>
				</div>
			</div>
			<div class="sacado-holder clearfix-child" >
				<div class="row clearfix-child text-indent-small" >
					<small>Sacado</small>
				</div>
				<div class="row clearfix-child" >
					<div class="nome" ><span>##name##</span></div>
					<div class="cpfcnpj" ><span>##cpfcnpj##</span></div>
				</div>
				<div class="row clearfix-child" >
					<div class="address" ><span>##street##, {{number}} - ##district##. ##city## - {{stt}} - {{zip}}</span></div>
					<div class="cod-baixa" ><span>Cód. baixa</span></div>
				</div>
			</div>
			<div class="bar-holder clearfix-child" >
				<div class="row clearfix-child" >
					<div class="float-left text-indent-small" >
						<small>Sacador/Avalista</small>
					</div>
					<div class="float-right" >
						<small>Autenticação mecânica - </small><span>Ficha de compensação</span>
					</div>
				</div>
				<div class="row barcode clearfix-child" >
					{{barcode}}
				</div>
			</div>
			<div class="separator-dotted text-align-right margin-bottom" >
				<small>Corte na linha pontilhada</small>
			</div>
		</div>
	</body>
</html>