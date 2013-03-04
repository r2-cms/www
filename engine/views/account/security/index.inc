<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Segurança | Cadastro</title>
		{{VIEW:head}}
		<link rel="stylesheet" type="text/css" href="{{CROOT}}{{GT8:account.root}}css/account.css" />
		<script type="text/javascript" src="{{CROOT}}{{GT8:account.root}}js/account.js" ></script>
		<script type="text/javascript" >
		//<![CDATA[
			
			jCube.Include('Element.setFocus');
			jCube.Include('Event.add');
			jCube.Include('Event.trigger');
			jCube.Include('String.md5');
			jCube.Include('Window.DOMReady');
			jCube(function(){
				jCube(':input[name=pass2]').addEvent('onkeyup', function(E){
					if ( this.value && this.value==jCube(':input[name=pass1]').value ) {
						if ( !jCube(':#eBtConfirm').className.contains('href-button-blue')) {
							jCube(':#eBtConfirm').addClass('href-button-blue');
						}
					} else {
						jCube(':#eBtConfirm').removeClass('href-button-blue');
					}
				}).addEvent('onkeydown', function(E){
					if ( E.key == 13) {
						if ( window.OnBeforeSave() ) {
							jCube(':a.gt8-form-post-save').trigger('onclick', E);
						}
					}
				}).addEvent('onchange', function(E){
					window.OnBeforeSave();
				});
				jCube(':input[name=pass1]').setFocus();
				window.OnBeforeSave	= function() {
					if ( jCube(':input[name=pass1]').value == '' ) {
						GT8.Spinner.show({
							label: 'Por favor, digite uma senha',
							hideImage: true,
							type: 'error',
							position: 'center',
							hideAfter: 5000
						});
						jCube(':input[name=pass1]').setFocus().select();
					} else if ( jCube(':input[name=pass1]').value === jCube(':input[name=pass2]').value) {
						jCube(':#ePass').value	= (jCube(':input[name=pass1]').value+ASP.tkn).md5();
						return true;
					} else {
						GT8.Spinner.show({
							label: 'As senhas digitas não conferem!',
							hideImage: true,
							type: 'error',
							position: 'center',
							hideAfter: 5000
						});
						jCube(':input[name=pass2]').setFocus().select();
					}
					return false;
				}
				window.OnAfterSave	= function(req) {
					if ( req.ret.affected ) {
						window.location	= 'confirmacao/';
					}
				}
			});
		//]]>
		</script>
		<script type="text/javascript" >
		//<![CDATA[
			var ASP	= {
				tkn:	'{{GT8:account.token}}',
				tstart: '-{{SESSION:GT8.tstart}}'
			}
		//]]>
		</script>
	</head>
	<body class="account security" >
		<div id="eMain" >
			{{VIEW:header}}
			{{VIEW:location}}
			<div class="account row-12 resume fluid-12" >
				<h1 class="margin-bottom grid-12" >
					Segurança
				</h1>
				<div class="grid-12 card bg-graylight shadow-small clearfix-child" >
					{{VIEW:account.menu}}
					<div class="grid-8 marginless bg-white sborder-left unfloat-phone" >
						<div class="padding-medium clearfix-child panel-right" >
							<h2 class="grid-12" >Para maior segurança, use uma senha que tenha mais de 8 caracteres, com números, letras e símbolos e não a compartilhe com ninguém.</h2>
							
							<p class="grid-12" >
								<label class="input-text input-pass prefix-4 grid-8 omega" >
									<strong class="grid-4 text-align-right" >Digite a senha</strong>
									<input type="password" name="pass1" class="grid-12 omega gt8" value="" />
									<small>&nbsp;</small>
									<em><span>&nbsp;</span></em>
								</label>
							</p>
							<input type="hidden" name="pass" class="gt8-form-posto" id="ePass" />
							<p class="grid-12" >
								<label class="input-text prefix-4 grid-8 omega" >
									<strong class="grid-4 text-align-right" >Redigite a senha</strong>
									<input type="password" name="pass2" class="grid-12 omega" value="" />
									<small>&nbsp;</small>
								</label>
							</p>
							<div class="abs-right abs-bottom margin-bottom grid-12 marginless" >
								<hr class="grid-12" />
								<p class="grid-12 clearfix-child margin-bottom-none" >
									<a id="eBtConfirm" href="#alterar-senha" class="href-button grid-8 float-right line-height-large gt8-form-post-save gt8formname-change-pass" >
										<span>Alterar a senha</span>
									</a>
								</p>
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix" ></div>
			</div>
			{{VIEW:footer}}
		</div>
	</body>
</html>