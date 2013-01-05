<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Redefinição de Senha | Cadastro</title>
		{{VIEW:head}}
		<link rel="stylesheet" type="text/css" href="{{CROOT}}{{GT8:account.root}}css/account.css" />
		<script type="text/javascript" src="{{CROOT}}{{GT8:account.root}}js/account.js" ></script>
		<script type="text/javascript" >
		//<![CDATA[
			
			jCube.Include('Element.setFocus');
			jCube.Include('Event.add');
			jCube.Include('Event.trigger');
			jCube.Include('Window.DOMReady');
			jCube(function(){
				jCube(':input[name=login]').addEvent('onkeyup', function(E){
					if ( E.key == 13) {
						window.setTimeout(function(){
							jCube(':a.gt8-form-post-save').trigger('onclick', E);
						}, 250);
					}
				}).setFocus();
			});
		//]]>
		</script>
		<style type="text/css" >
			input.login {
				font-size: 2em !important;
				padding: 0.8em 0 !important;
				height: 1.3em !important;
				font-weight: bolder !important;
				color: #000 !important;
				letter-spacing: 0.1em;
			}
		</style>
	</head>
	<body class="account reset-pass" >
		<div id="eMain" >
			{{VIEW:header}}
			{{VIEW:location}}
			<div class="account row-12 resume fluid-12 clearfix-child" >
				<h1 class="margin-bottom grid-7 prefix-2h suffix-2h" >Redefina sua senha</h1>
				<div class="grid-7 prefix-2h suffix-2h clearfix-child" >
					<div class="grid-12 marginless bg-white shadow-small" >
						<div class="padding-large clearfix-child" >
							<form action="?action=pass-recover" method="post" class="gt8-form-sender" >
								<h2 class="grid-12" >Insira seu e-mail cadastrado e clique em prosseguir</h2>
								<p class="grid-12 clearfix-child" >
									<label class="input-text clearfix-child" >
										<input type="text" name="login" class="grid-12 marginless gt8-form-posto gt8-form-required login" />
										<small>&nbsp;</small>
									</label>
								</p>
								
								<div class="height-medium grid" ></div>
								<hr class="grid-12 margin-bottom" />
								<p class="grid-12 clearfix-child margin-bottom-none omega" >
									<a href="../" class="href-button grid-4 alpha line-height-large" >
										<span>Cancelar</span>
									</a>
									<a href="#prosseguir" class="href-button grid-7 margin-left-1 omega line-height-large gt8-form-post-save gt8formname-pass-recover gt8-form-post-send" >
										<span>Prosseguir</span>
									</a>
								</p>
							</form>
						</div>
					</div>
				</div>
			</div>
			{{VIEW:footer}}
		</div>
	</body>
</html>