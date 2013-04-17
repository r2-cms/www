<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Novo registro | Salão do Calçado</title>
		<meta http-equiv="cache-control" content="max-age=0" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="expires" content="0" />
		<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
		<meta http-equiv="pragma" content="no-cache" />
		{{VIEW:head}}
		<link rel="stylesheet" type="text/css" href="{{CROOT}}{{GT8:account.root}}css/account.css" />
		<script type="text/javascript" src="{{CROOT}}{{GT8:account.root}}js/account.js" ></script>
		<script type="text/javascript" src="{{CROOT}}{{GT8:account.root}}js/register.js" ></script>
		<script type="text/javascript" >
		//<![CDATA[
			var ASP	= {
				tkn:	'{{GT8:account.token}}',
				tstart: '-{{SESSION:GT8.tstart}}',
				CROOT: '{CROOT}}',
				padmin: '{CROOT}}',
				genre: '{{genre}}',
				natureza: '{{natureza}}',
				stt: '{{stt}}',
				accountError: 'login already exists'
			}
			jCube(function(){
				if ( ASP.genre) {
					jCube(':select[name=genre]').setValue( ASP.genre).trigger('onchange');
				}
				if ( ASP.natureza) {
					jCube(':select[name=natureza]').setValue( ASP.natureza).trigger('onchange');
				}
				if ( ASP.stt) {
					jCube(':select[name=stt]').setValue( ASP.stt).trigger('onchange');
				}
				
				if ( ASP.accountError.length ) {
					jCube(':#ePendingFields').removeClass('hidden');
					jCube(':#eAllFields').addClass('hidden');
					
					var eC	= jCube(':#ePendingFields .content');
					if ( ASP.accountError === 'login already exists') {
						jCube(':input[name=mail]').getParent('p').appendTo( eC);
					}
					jCube('::input').each(function(){
						if ( this.value == '' && this.getParent('p')) {
							this.getParent('p').appendTo( eC);
						} else {
							console.log([this.name, this.value]);
						}
					});
				} else {
					jCube(':#ePendingFields').addClass('hidden');
					jCube(':#eAllFields').removeClass('hidden');
				}
			});
		//]]>
		</script>
	</head>
	<body class="account new-registry" >
		<div id="eMain" >
			{{VIEW:cart.header}}
			{{VIEW:location}}
			<div class="account row-12 fluid-12 resume margin-bottom-large clearfix-child" >
				<div class="grid-12 gt8-debug-error {{$this->hasMessages('accountError')}}" >
					<div class="padding-medium" >
						<div class="close" ></div>
						<h2>Ops, ocorreu um erro!</h2>
						<div>
							{{$this->getMessages('accountError')}}
						</div>
					</div>
				</div>
				<h1 class="margin-bottom grid-12" >Realize seu cadastro</h1>
				<div class="grid-12 card bg-graylight shadow-small clearfix-child" >
					<div class="grid-12 marginless bg-white sborder-left unfloat-phone" >
						<div class="padding-medium clearfix-child" >
							<div id="ePendingFields" class="grid-7 margin-left-1 marginless clearfix-child hidden" >
								<h2>Complete os campos abaixo para concluir</h2>
								<div class="content" >
									
								</div>
							</div>
							<div id="eAllFields" class="grid-7 margin-left-1 marginless clearafix-child " >
								<h2>Os dados abaixo são necessários para emissão de nota fiscal</h2>
								
								<h5>Dados pessoais</h5>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Nome completo</strong>
										<input type="text" name="name" class="grid-12 omega gt8 gt8-form-post" value="{{name}}" />
										<small>&nbsp;</small>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Data de nascimento</strong>
										<input type="text" name="birth" class="grid-12 omega gt8 gt8-form-post mask-date" value="{{birth}}" />
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
										<input type="text" name="cpfcnpj" class="grid-12 omega gt8-form-post" value="{{cpfcnpj}}" />
										<small>&nbsp;</small>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" id="eDocument" >
										<strong class="grid-4 text-align-right" >RG</strong>
										<input type="text" name="document" class="grid-12 omega gt8-form-post" value="{{document}}" />
										<small>&nbsp;</small>
									</label>
								</p>
								
								<hr class="grid-12" />
								<h5 class="grid-12" >Informações de contato</h5>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >E-mail principal</strong>
										<input type="text" name="mail" class="grid-12 omega gt8-form-post" value="{{mail}}" />
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
										<input type="text" name="phone-home" class="grid-12 omega gt8-form-post mask-phone" value="{{phone-home}}" />
										<small>&nbsp;</small>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Celular pessoal</strong>
										<input type="text" name="phone-mobile" class="grid-12 omega gt8-form-post" value="{{phone-mobile}}" />
										<small>&nbsp;</small>
									</label>
								</p>
								
								<hr class="grid-12" />
								<h5 class="grid-12" >Endereço de entrega</h5>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >CEP</strong>
										<input type="text" name="zip" class="grid-12 omega mask-zip gt8-form-post" value="{{zip}}" />
										<small>&nbsp;</small>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Rua</strong>
										<input type="text" name="street" class="grid-12 omega gt8-form-post" value="{{street}}" />
										<small>&nbsp;</small>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Número</strong>
										<input type="text" name="number" class="grid-12 omega gt8-form-post" value="{{number}}" />
										<small>&nbsp;</small>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Complemento</strong>
										<input type="text" name="complement" class="grid-12 omega gt8-form-post" value="{{complement}}" />
										<small>&nbsp;</small>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Referência</strong>
										<input type="text" name="reference" class="grid-12 omega gt8-form-post" value="{{reference}}" />
										<small>&nbsp;</small>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Bairro</strong>
										<input type="text" name="district" class="grid-12 omega gt8-form-post" value="{{district}}" />
										<small>&nbsp;</small>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Cidade</strong>
										<input type="text" name="city" class="grid-12 omega gt8-form-post" value="{{city}}" />
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
										<input type="password" name="pass1" class="grid-12 omega gt8" value="{{pass1}}" />
										<small>&nbsp;</small>
										<em><span>&nbsp;</span></em>
									</label>
								</p>
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Redigite a senha</strong>
										<input type="password" name="pass2" class="grid-12 omega" value="{{pass1}}" />
										<small>&nbsp;</small>
									</label>
								</p>
								
							</div>
							<div class="grid-7 margin-left-1 marginless clearfix-child" >
								<div class="height-superlarge grid-12" ></div>
								<hr class="grid-12" />
								<form action="?create-account" class="grid-12 clearfix-child margin-bottom-none omega gt8-form-sender" method="post" >
									<input type="hidden" name="pass" class="gt8-form-posto" id="ePass" />
									<a href="#criar-novo-registro" class="href-button href-button-blue grid-8 margin-left-4 omega line-height-large gt8-form-post-send gt8formname-create-account" >
										<span>Registrar</span>
									</a>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>