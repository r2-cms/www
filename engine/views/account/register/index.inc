<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Novo registro | Salão do Calçado</title>
		{{VIEW:head}}
		<link rel="stylesheet" type="text/css" href="{{CROOT}}{{GT8:account.root}}css/account.css" />
		<script type="text/javascript" src="{{CROOT}}{{GT8:account.root}}js/account.js" ></script>
		<script type="text/javascript" >
		//<![CDATA[
			
			jCube.Include('Element.addClass');
			jCube.Include('Element.setHTML');
			jCube.Include('Element.getOptionValue');
			jCube.Include('Element.removeClass');
			jCube.Include('Element.setFocus');
			jCube.Include('Element.setSelection');
			jCube.Include('Element.setValue');
			jCube.Include('Event.add');
			jCube.Include('Event.trigger');
			jCube.Include('String.md5');
			jCube(function(){
				
				jCube(':select[name=natureza]').addEvent('onchange', function(E){//NATUREZA
					if ( this.getOptionValue() == 'J') {
						jCube(':#eCPFCNPJ strong').setHTML('CNPJ');
						jCube(':#eCPFCNPJ input').setFixedMask('##.###.###/####-##');
						jCube(':#eDocument strong').setHTML('Inscrição');
					} else {
						jCube(':#eCPFCNPJ strong').setHTML('CPF');
						jCube(':#eCPFCNPJ input').setFixedMask('###.###.###-##');
						jCube(':#eDocument strong').setHTML('RG');
					}
				}).trigger('onchange');
				jCube(':input[name=zip]').addEvent('onchange', function(E){//ZIP
					GT8.Spinner.request(new jCube.Server.HttpRequest({
						url: '?action=get-zip&zip='+ this.value,
						noCache: false,
						label: 'Buscando endereço...',
						message: 'Concluído',
						onComplete: function(){
							var Zip	= GT8.meow(this.responseText);
							jCube(':input[name=street]').value		= Zip.logradouro;
							jCube(':input[name=district]').value	= Zip.bairro;
							jCube(':input[name=city]').value		= Zip.cidade;
							jCube(':select[name=stt]').setValue(Zip.estado).trigger('onchange');
						},
						hideAfter: 1
					}))
				});
				jCube(':input[name=mail]').addEvent('onchange', function(E){//MAIL
					GT8.Spinner.request(new jCube.Server.HttpRequest({
						url: '?action=check-mail&mail='+ this.value,
						noCache: false,
						label: 'Checando e-mail...',
						onComplete: function(){
							if ( this.responseText.toLowerCase().contains('e-mail já cadastrado')) {
								jCube(':#eMailMessage').removeClass('hidden');
							} else {
								jCube(':#eMailMessage').addClass('hidden');
							}
						},
						hideAfter: 1
					}))
				});
				window.OnBeforeSave	= function() {
					var eInput, message;
					
					if ( jCube(':input[name=name]').value == '' ) {
						message	= 'Por favor, escreva seu nome completo';
						eInput	= jCube(':input[name=name]');
						
					} else if ( jCube(':select[name=natureza]').value == 'F' && !jCube(':input[name=cpfcnpj]').value ) {
						message	= 'Por favor, informe seu CPF';
						eInput	= jCube(':input[name=cpfcnpj]');
						
					} else if ( jCube(':select[name=natureza]').value == 'J' && !jCube(':input[name=cpfcnpj]').value ) {
						message	= 'Por favor, informe seu CNPJ';
						eInput	= jCube(':input[name=cpfcnpj]');
						
					} else if ( jCube(':input[name=mail]').value == '' ) {
						message	= 'Por favor, informe seu e-mail';
						eInput	= jCube(':input[name=mail]');
						
					} else if ( jCube(':input[name=phone-home]').value == '' ) {
						message	= 'Por favor, informe seu número de telefone residencial';
						eInput	= jCube(':input[name=phone-home]');
						
					} else if ( jCube(':input[name=phone-mobile]').value == '' ) {
						message	= 'Por favor, informe seu número de telefone celular';
						eInput	= jCube(':input[name=phone-mobile]');
						
					} else if ( jCube(':input[name=street]').value == '' ) {
						message	= 'Por favor, informe o nome da sua rua/avenida';
						eInput	= jCube(':input[name=street]');
						
					} else if ( jCube(':input[name=number]').value == '' ) {
						message	= 'Por favor, informe o número da sua residência';
						eInput	= jCube(':input[name=number]');
						
					} else if ( jCube(':input[name=district]').value == '' ) {
						message	= 'Por favor, informe o nome de seu bairro';
						eInput	= jCube(':input[name=district]');
						
					} else if ( jCube(':input[name=city]').value == '' ) {
						message	= 'Por favor, informe o nome da sua cidade';
						eInput	= jCube(':input[name=city]');
						
					} else if ( jCube(':select[name=stt]').selectedIndex == 0 ) {
						message	= 'Por favor, informe o seu estado';
						eInput	= jCube(':select[name=stt]');
						
					} else if ( jCube(':input[name=pass1]').value == '' ) {
						message	= 'Por favor, digite uma senha';
						eInput	= jCube(':input[name=pass1]');
						
					} else if ( jCube(':input[name=pass1]').value === jCube(':input[name=pass2]').value) {
						jCube(':#ePass').value	= (jCube(':input[name=pass1]').value+ASP.tkn).md5();
						return true;
					
					} else {
						message	= 'As senhas digitadas não conferem!';
						eInput	= jCube(':input[name=pass1]');
						
					}
					
					GT8.Spinner.show({
						label: message,
						hideImage: true,
						type: 'error',
						position: 'center',
						hideAfter: 5000
					});
					eInput.setFocus().setSelection();
					
					return false;
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
	<body class="account new-registry" >
		<div id="eMain" >
			{{VIEW:header}}
			{{VIEW:location}}
			<div class="account row-12 fluid-12 resume clearfix-child" >
				<h1 class="margin-bottom grid-12" >Novo cadastro</h1>
				<div class="grid-12 card bg-graylight shadow-small clearfix-child" >
					<div class="grid-12 marginless bg-white sborder-left unfloat-phone" >
						<div class="padding-medium clearfix-child" >
							<div class="grid-7 margin-left-1 marginless clearafix-child" >
								<h2>Os dados abaixo são necessários para emissão de nota fiscal</h2>
								
								<h5>Dados pessoais</h5>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Nome completo</strong>
										<input type="text" name="name" class="grid-12 omega gt8 gt8-form-post" />
										<small>&nbsp;</small>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Data de nascimento</strong>
										<input type="text" name="birth" class="grid-12 omega gt8 gt8-form-post mask-date" />
										<small>&nbsp;</small>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-select prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Gênero</strong>
										<span class="e-select grid-12 omega">
											<select name="genre" class="gt8-form-post" >
												<option value="F" selected="selected" >Feminino</option>
												<option value="M" >Masculino</option>
											</select>
											<span class="button group-button"><strong>Feminino</strong><img src="{{CROOT}}imgs/gt8/arrow-down-mini.png" alt="" /></span>
										</span>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-select prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Sou pessoa</strong>
										<span class="e-select grid-12 omega">
											<select name="natureza" class="gt8-form-post" >
												<option value="F" selected="selected" >Física</option>
												<option value="J" >Jurídica</option>
											</select>
											<span class="button group-button"><strong>Física</strong><img src="{{CROOT}}imgs/gt8/arrow-down-mini.png" alt="" /></span>
										</span>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" id="eCPFCNPJ" >
										<strong class="grid-4 text-align-right" >CPF</strong>
										<input type="text" name="cpfcnpj" class="grid-12 omega gt8-form-post" />
										<small>&nbsp;</small>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" id="eDocument" >
										<strong class="grid-4 text-align-right" >RG</strong>
										<input type="text" name="document" class="grid-12 omega gt8-form-post" />
										<small>&nbsp;</small>
									</label>
								</p>
								
								<hr class="grid-12" />
								<h5 class="grid-12" >Informações de contato</h5>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >E-mail principal</strong>
										<input type="text" name="mail" class="grid-12 omega gt8-form-post" />
										<small>&nbsp;</small>
									</label>
									<span id="eMailMessage" class="grid-8 prefix-4 margin-top omega hidden" >
										<span class="text-error grid-12" >
											<span class="padding-small grid" >
												Este e-mail já está cadastrado! Gostaria de <a href="{{CROOT}}{{GT8:account.root}}{{GT8:account.resetPassword.root}}" class="link" >recuperar a senha</a>?
											</span>
										</span>
									</span>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Telefone residencial</strong>
										<input type="text" name="phone-home" class="grid-12 omega gt8-form-post mask-phone" />
										<small>&nbsp;</small>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Celular pessoal</strong>
										<input type="text" name="phone-mobile" class="grid-12 omega gt8-form-post" />
										<small>&nbsp;</small>
									</label>
								</p>
								
								<hr class="grid-12" />
								<h5 class="grid-12" >Endereço de entrega</h5>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >CEP</strong>
										<input type="text" name="zip" class="grid-12 omega mask-zip gt8-form-post" />
										<small>&nbsp;</small>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Rua</strong>
										<input type="text" name="street" class="grid-12 omega gt8-form-post" />
										<small>&nbsp;</small>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Número</strong>
										<input type="text" name="number" class="grid-12 omega gt8-form-post" />
										<small>&nbsp;</small>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Complemento</strong>
										<input type="text" name="complement" class="grid-12 omega gt8-form-post" />
										<small>&nbsp;</small>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Referência</strong>
										<input type="text" name="reference" class="grid-12 omega gt8-form-post" />
										<small>&nbsp;</small>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Bairro</strong>
										<input type="text" name="district" class="grid-12 omega gt8-form-post" />
										<small>&nbsp;</small>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Cidade</strong>
										<input type="text" name="city" class="grid-12 omega gt8-form-post" />
										<small>&nbsp;</small>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-select prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Escolha seu estado</strong>
										<span class="e-select grid-12 omega">
											<select name="stt" class="gt8-form-post" >
												{{COMBO-OPTIONS:estados|1|0|3}}
											</select>
											<span class="button group-button"><strong>Estados</strong><img src="{{CROOT}}imgs/gt8/arrow-down-mini.png" alt="" /></span>
										</span>
									</label>
								</p>
								
								
								<hr class="grid-12" />
								<h5 class="grid-12" >Senha de acesso ao site</h5>
								<p class="grid-12" >
									<label class="input-text input-pass prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Digite a senha</strong>
										<input type="password" name="pass1" class="grid-12 omega gt8" value="" />
										<small>&nbsp;</small>
										<em><span>&nbsp;</span></em>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Redigite a senha</strong>
										<input type="password" name="pass2" class="grid-12 omega" value="" />
										<small>&nbsp;</small>
									</label>
								</p>
								
								<div class="height-superlarge grid-12" ></div>
								<hr class="grid-12" />
								<p class="grid-12 clearfix-child margin-bottom-none omega" >
									<form action="?create-account" class="gt8-form-sender" method="post" >
										<input type="hidden" name="pass" class="gt8-form-posto" id="ePass" />
										<a href="#criar-novo-registro" class="href-button grid-8 margin-left-4 omega line-height-large gt8-form-post-send gt8formname-create-account" >
											<span>Registrar</span>
										</a>
									</form>
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			{{VIEW:footer}}
		</div>
	</body>
</html>