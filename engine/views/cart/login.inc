<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Autenticação | Processo de compras</title>
		<meta http-equiv="cache-control" content="max-age=0" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="expires" content="0" />
		<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
		<meta http-equiv="pragma" content="no-cache" />
		<link rel="stylesheet" type="text/css" href="{{CROOT}}css/checkout.css" />
		<link rel="stylesheet" type="text/css" href="{{CROOT}}account/css/login.css" />
		{{VIEW:head}}
		<script type="text/javascript" src="{{CROOT}}account/js/login.js" ></script>
		<script type="text/javascript" src="{{CROOT}}js/shopping/cart.js" ></script>
		<script type="text/javascript" >
		//<![CDATA[
			
			var ASP	= {
				tkn:	'{{GT8:account.token}}',
				sstart: '-{{SESSION:GT8.tstart}}'
			}
			
		//]]>
		</script>
	</head>
	<body class="shopping delivery" >
		<div id="eMain" >
			{{VIEW:cart.header}}
			<div id="eLogin" class="login row-12 fluid-12 clearfix-child margin-top-large" >
				<h1 class="grid-12 margin-bottom" >Seu cadastro</h1>
				<div class="grid-12 card bg-white shadow-small clearfix-child" >
					<div class="grid-7 marginless bg-graylight sborder-left" >
						<div class="height-4 grid-10 prefix-1 suffix-1 padding-top-large padding-bottom-large clearfix-child" >
							<h2 class="text-align-left" >
								<strong>Já tenho cadastro</strong>
							</h2>
							<p>
								<label class="input-text" >
									<input type="text" value="{{COOKIE:user-name|}}" name="name" />
									<small>Digite o e-mail</small>
								</label>
							</p>
							<p class="margin-bottom-small" >
								<label class="input-text" >
									<input type="password" value="" name="password" />
									<small>Digite a senha</small>
								</label>
							</p>
							<p>
								<small><a href="{{CROOT}}{{GT8:account.root}}{{GT8:account.resetPassword.root}}" >Esqueceu a senha?</a></small>
							</p>
							<h3>
								<img alt="carregando..." src="{{CROOT}}imgs/gt8/spinner.gif" class="spinner login-spinner-feedback opacity-0 float-left" />
								<span class="login-info-feedback" >&nbsp;</span>
							</h3>
							<p class="abs-bottom pos-bottom grid-10 marginless" >
								<a href="#" class="href-button href-button-blue line-height-large submit" >
									<span>Acessar</span>
								</a>
							</p>
						</div>
					</div>
					<div class="grid-5 marginless" >
						<div class="height-4 grid-10 prefix-1 suffix-1 padding-top-large padding-bottom-large clearfix-child" >
							<h2 class="text-align-left" >
								<strong>Não tenho cadastro</strong>
							</h2>
							<ul>
								<li>Rastrei seus pedidos</li>
								<li>Veja seu histórico de compras</li>
								<li>Receba promoções</li>
							</ul>
							<p class="abs-bottom pos-bottom grid-10" >
								<a href="{{CROOT}}{{GT8:account.root}}{{GT8:account.register.root}}" class="href-button line-height-large bt-create-new-account" >
									<span>Criar conta</span>
								</a>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>