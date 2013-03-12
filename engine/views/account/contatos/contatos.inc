<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Contato | Cadastro</title>
		{{VIEW:head}}
		<link rel="stylesheet" type="text/css" href="{{CROOT}}{{GT8:account.root}}css/account.css" />
		<script type="text/javascript" src="{{CROOT}}{{GT8:account.root}}js/account.js" ></script>
		<script type="text/javascript" >
		//<![CDATA[
			
			window.OnAfterSave	= function(req) {
				window.location	= '../';
			}
			
		//]]>
		</script>
	</head>
	<body class="account contacts" >
		<div id="eMain" >
			{{VIEW:header}}
			{{VIEW:location}}
			<div class="account row-12 resume fluid-12" >
				<h1 class="margin-bottom grid-12" >Contatos</h1>
				<div class="grid-12 card bg-graylight shadow-small clearfix-child" >
					{{VIEW:account.menu}}
					<div class="grid-8 marginless bg-white sborder-left unfloat-phone" >
						<div class="padding-medium clearfix-child" >
							<h2 class="grid-12" >Mantenha seus dados pessoais atualizados para podermos melhor atendê-lo</h2>
							
							<p class="grid-12" >
								<label class="input-text prefix-4 grid-8 omega" >
									<strong class="grid-4 text-align-right" >E-mail primário</strong>
									<input type="text" name="primary-mail" class="grid-12 omega" readonly="readonly" value="##primary-mail##"/>
									<small>&nbsp;</small>
								</label>
							</p>
							{{FOREACH:contacts}}
							
							<hr class="grid-12" />
							<h5 class="grid-12" >##channel##</h5>
							{{foreach-column:channel}}
							<p class="grid-12" >
								<label class="input-text prefix-4 grid-8 omega" >
									<strong class="grid-4 text-align-right" >##channel## ##type##</strong>
									<input type="text" name="channel-{{id}}" class="grid-12 omega gt8-form-post" value="##value##" />
									<small>&nbsp;</small>
								</label>
							</p>
							{{/foreach-column}}
							{{/FOREACH}}
							
							<div class="height-medium grid" ></div>
							<hr class="grid-12" />
							<p class="grid-12 clearfix-child margin-bottom-none omega" >
								<a href="../" class="href-button grid-2hm line-height-large margin-left-4m" >
									<span>Cancelar</span>
								</a>
								<a href="#salvar" class="href-button href-button-blue grid-5 omega line-height-large gt8-form-post-save gt8formname-save-account-contacts" >
									<span>Salvar</span>
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