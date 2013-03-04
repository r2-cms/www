<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Novo Endereço | Cadastro</title>
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
				jCube(':input[name=zip]').addEvent('onkeydown', function(E){
					if ( E.key == 13) {
						jCube(':a.gt8-form-post-save').trigger('onclick', E);
					}
				}).setFocus();
			});
			window.OnAfterSave	= function(req) {
				if ( req.ret.insertId ) {
					window.location	= '../'+ req.ret.insertId +'/';
				}
			}
		//]]>
		</script>
		<style type="text/css" >
			input.zip {
				font-size: 4em !important;
				height: 1.3em !important;
				font-weight: bolder !important;
				color: #000 !important;
				letter-spacing: 0.1em;
				text-align: center !important;
			}
		</style>
	</head>
	<body class="account new-address" >
		<div id="eMain" >
			{{VIEW:header}}
			{{VIEW:location}}
			<div class="account row-12 resume fluid-12 clearfix-child" >
				<h1 class="margin-bottom grid-7 prefix-2h suffix-2h" >Novo endereço</h1>
				<div class="grid-7 prefix-2h suffix-2h clearfix-child" >
					<div class="grid-12 marginless bg-white shadow-small" >
						<div class="padding-large clearfix-child" >
							<h2 class="grid-12" >Digite o CEP da sua localidade</h2>
							<p class="grid-12 clearfix-child" >
								<label class="input-text clearfix-child" >
									<input type="text" name="zip" class="grid-12 marginless gt8-form-post mask-zip zip" />
									<small>&nbsp;</small>
								</label>
							</p>
							
							<div class="height-medium grid" ></div>
							<hr class="grid-12 margin-bottom" />
							<p class="grid-12 clearfix-child margin-bottom-none omega" >
								<a href="../" class="href-button grid-4 alpha line-height-large" >
									<span>Cancelar</span>
								</a>
								<a href="#criar" class="href-button href-button-blue grid-7 margin-left-1 omega line-height-large gt8-form-post-save gt8formname-create-address" >
									<span>Criar</span>
								</a>
							</p>
						</div>
					</div>
				</div>
			</div>
			{{VIEW:footer}}
		</div>
	</body>
</html>