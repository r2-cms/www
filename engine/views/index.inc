<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Cadastro de usuário | Salão do Calçado</title>
		{{VIEW:head}}
		<link rel="stylesheet" type="text/css" href="{{CROOT}}{{GT8:account.root}}css/account.css" />
		<script type="text/javascript" src="{{CROOT}}{{GT8:account.root}}js/account.js" ></script>
	</head>
	<body class="account home" >
		<div id="eMain" >
			{{VIEW:header}}
			{{VIEW:location}}
			<div class="account row-12 fluid-12 resume clearfix-child" >
				<h1 class="margin-bottom grid-12" >Seu cadastro</h1>
				<div class="grid-12 card bg-graylight shadow-small clearfix-child" >
					{{VIEW:account.menu}}
					<div class="grid-8 marginless bg-white sborder-left unfloat-phone" >
						<div class="padding-medium clearfix-child" >
							<h2>Resumo de sua conta</h2>
							<h5>Dados pessoais</h5>
							<p class="grid-12 clearfix-child" >
								<strong class="grid-3 text-align-right" >Seu nome</strong>
								<span class="grid " >##name##</span>
							</p>
							<p class="grid-12 clearfix-child" >
								<strong class="grid-3 text-align-right" >Data de nascimento</strong>
								<span class="grid " >{{birth}}</span>
							</p>
							<p class="grid-12 clearfix-child" >
								<strong class="grid-3 text-align-right" >Gênero</strong>
								<span class="grid " >{{genre}}</span>
							</p>
							<p class="grid-12 clearfix-child" >
								<strong class="grid-3 text-align-right" >Natureza</strong>
								<span class="grid " >{{natureza}}</span>
							</p>
							<p class="grid-12 clearfix-child" >
								<strong class="grid-3 text-align-right" >{{document-type}}</strong>
								<span class="grid " >{{document}}</span>
							</p>
							<p class="grid-12 clearfix-child" >
								<strong class="grid-3 text-align-right" >{{cadastro-type}}</strong>
								<span class="grid " >{{cpfcnpj}}</span>
							</p>
							<hr />
							
							<h5>Contatos</h5>
							<p class="grid-12 clearfix-child" >
								<strong class="grid-3 text-align-right" >E-mail primário</strong>
								<span class="grid" >{{primary-mail}}</span>
							</p>
							{{FOREACH:contacts}}
							<p class="grid-12 clearfix-child" >
								<strong class="grid-3 text-align-right" >{{channel}} ({{type}})</strong>
								<span class="grid" >{{value}}</span>
							</p>
							{{/FOREACH}}
							<hr />
							
							<h5>Endereços</h5>
							{{FOREACH:address}}
							<p class="grid-12 clearfix-child alpha" >
								<strong class="display-block grid-8 prefix-3m" >##type##</strong>
								<span class="grid-8 prefix-3m" >##street##, {{number}} - ##district##</span>
								<span class="grid-8 prefix-3m" >##city## - {{stt}}</span>
								<span class="grid-8 prefix-3m" >{{zip}}</span>
							</p>
							<hr class="margin-left-3m" />
							{{/FOREACH}}
						</div>
					</div>
				</div>
			</div>
			{{VIEW:footer}}
		</div>
	</body>
</html>