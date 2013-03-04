<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Endereços | Cadastro</title>
		{{VIEW:head}}
		<link rel="stylesheet" type="text/css" href="{{CROOT}}{{GT8:account.root}}css/account.css" />
		<script type="text/javascript" src="{{CROOT}}{{GT8:account.root}}js/account.js" ></script>
		<script type="text/javascript" >
		//<![CDATA[
			
		//]]>
		</script>
	</head>
	<body class="account address" >
		<div id="eMain" >
			{{VIEW:header}}
			{{VIEW:location}}
			<div class="account row-12 resume fluid-12" >
				<h1 class="margin-bottom grid-12" >Endereços</h1>
				<div class="grid-12 card bg-graylight shadow-small clearfix-child" >
					{{VIEW:account.menu}}
					<div class="grid-8 marginless bg-white sborder-left unfloat-phone" >
						<div class="padding-medium clearfix-child panel-right" >
							<h2 class="grid-12" >Clique em um endereço para editá-lo ou crie um novo</h2>
							{{FOREACH:addresses}}
							<a href="{{id}}/" class="grid-12 card text-decoration-none line-height" >
								<span class="padding" >
									<strong class="display-block" >##type##</strong>
									<span class="display-block" >##street##, {{number}} - ##district##</span>
									<span class="display-block" >##city## - {{stt}}</span>
									<span class="display-block a-zip" >{{zip}}</span>
								</span>
							</a>
							{{/FOREACH}}
							<div class="height-medium grid" ></div>
							<hr class="grid-12" />
							<p class="grid-12 clearfix-child margin-bottom-none" >
								<a href="novo/" class="href-button grid-6 float-right line-height-large" >
									<span>Criar novo endereço</span>
								</a>
							</p>
						</div>
					</div>
				</div>
				<div class="clearfix" ></div>
			</div>
			{{VIEW:footer}}
		</div>
	</body>
</html>