<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Dados pessoais | Cadastro</title>
		{{VIEW:head}}
		<link rel="stylesheet" type="text/css" href="{{CROOT}}{{GT8:account.root}}css/account.css" />
		<script type="text/javascript" src="{{CROOT}}{{GT8:account.root}}js/account.js" ></script>
		<script type="text/javascript" >
		//<![CDATA[
			
			jCube.Include('Element.setHTML');
			jCube.Include('Element.getOptionValue');
			jCube.Include('Event.add');
			jCube.Include('Event.trigger');
			jCube(function(){
				jCube(':select[name=natureza]').addEvent('onchange', function(E){
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
			});
			window.OnAfterSave	= function(req) {
				window.location	= '../';
			}
		//]]>
		</script>
	</head>
	<body class="account data" >
		<div id="eMain" >
			{{VIEW:header}}
			{{VIEW:location}}
			<div class="account row-12 resume fluid-12" >
				<h1 class="margin-bottom grid-12" >Dados Pessoais</h1>
				<div class="grid-12 card bg-graylight shadow-small clearfix-child" >
					{{VIEW:account.menu}}
					<div class="grid-8 marginless bg-white sborder-left unfloat-phone" >
						<div class="padding-medium clearfix-child" >
							<h2 class="grid-12" >Mantenha seus dados pessoais atualizados para podermos melhor atendê-lo</h2>
							<p class="grid-12" >
								<label class="input-text prefix-4 grid-8 omega" >
									<strong class="grid-4 text-align-right" >Nome completo</strong>
									<input type="text" name="name" class="grid-12 omega gt8-form-post" value="##name##" />
									<small>&nbsp;</small>
								</label>
							</p>
							<p class="grid-12" >
								<label class="input-text prefix-4 grid-8 omega" >
									<strong class="grid-4 text-align-right" >Identificação</strong>
									<input type="text" class="grid-12 omega" value="##login##" readonly="readonly" />
									<small>&nbsp;</small>
								</label>
							</p>
							{{IF:$_SESSION['login']['level']>2}}
							<p class="grid-12" >
								<label class="input-text prefix-4 grid-8 omega" >
									<strong class="grid-4 text-align-right" >Registrado como</strong>
									<input type="text" class="grid-12 omega" value="##level_pt##" readonly="readonly" />
									<small>&nbsp;</small>
								</label>
							</p>
							{{/IF}}
							<p class="grid-12" >
								<label class="input-select prefix-4 grid-8 omega" >
									<strong class="grid-4 text-align-right" >Gênero</strong>
									<span class="e-select grid-12 omega" >
										<select name="genre" class="gt8-form-post" >
											<option value="F" {{genre-f-selected}}>Feminino</option>
											<option value="M" {{genre-m-selected}}>Masculino</option>
										</select>
										<span class="button group-button"><strong>{{genre}}</strong><img src="{{CROOT}}imgs/gt8/arrow-down-mini.png" alt="" /></span>
									</span>
								</label>
							</p>
							<p class="grid-12" >
								<label class="input-text prefix-4 grid-8 omega" >
									<strong class="grid-4 text-align-right" >Data de nascimento</strong>
									<input type="text" name="birth" class="grid-12 omega gt8-form-post mask-date" value="{{birth}}" />
									<small>&nbsp;</small>
								</label>
							</p>
							
							<p class="grid-12" >
								<label class="input-select prefix-4 grid-8 omega" >
									<strong class="grid-4 text-align-right" >Natureza</strong>
									<span class="e-select grid-12 omega">
										<select name="natureza" class="gt8-form-post" >
											<option value="F" {{natureza-f-selected}}>Física</option>
											<option value="J" {{natureza-j-selected}}>Jurídica</option>
										</select>
										<span class="button group-button"><strong>{{natureza}}</strong><img src="{{CROOT}}imgs/gt8/arrow-down-mini.png" alt="" /></span>
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
							
							<div class="height-medium grid" ></div>
							<hr class="grid-12" />
							<p class="grid-12 clearfix-child margin-bottom-none omega" >
								<a href="../" class="href-button grid-2hm line-height-large margin-left-4m" >
									<span>Cancelar</span>
								</a>
								<a href="#salvar" class="href-button href-button-blue grid-5 omega line-height-large gt8-form-post-save gt8formname-save-account-data" >
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