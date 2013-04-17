<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Lista de inscrições | GT8</title>
		<link rel="stylesheet" type="text/css" href="css/editor.css" />
		{{VIEW:admin.head-default}}
		<script type="text/javascript" src="js/editor.js" ></script>
		<script type="text/javascript" src="{{CROOT}}js/gt8/editor.js" ></script>
		<script type="text/javascript" >
			{{JS_SERVER_VARS}}
		</script>
	</head>
	<body class="{{modal-class}} overflow-auto" >
		<div id="eMain" >
			<?php
				$x	.= '<a id="eBtAdd" title="Adicionar nova inscrição" href="?action=add-subscription" class="button" ><img alt="[icon]" src="{{CROOT}}imgs/gt8/add-small.png" /></a>';
				$this->setData('toolbar', $x);
			?>
			{{VIEW:admin.header-new}}
			<div id="eResults" class="row-12 fluid-12 margin-top-large" >
				<div class="padding-large card-white" >
					<div class="header clearfix-child line-height-large bg-light box-shadow margin-bottom-small" >
						<div class="grid-h text-indent" >&nbsp;</div>
						<div class="grid-3 text-indent" ><strong>Nome</strong></div>
						<div class="grid-3h" ><strong>E-mail</strong></div>
						<div class="grid-1" ><strong>Gênero</strong></div>
						<div class="grid-1" ><strong>Idade</strong></div>
						<div class="grid-3" ><strong>Cadastro</strong></div>
					</div>
					<div class="body clearfix-child line-height-medium editable-fields" >
						{{$this->loadRows()}}
						{{FOREACH:rows}}
						<div class="grid-12 marginless gt8-update-id" id="subscription-{{id}}" >
							<a class="grid-h icon-enabled-{{enabled}}" href="#" >&nbsp;</a>
							<div class="grid-3" ><input type="text" value="##name##" name="name" class="gt8-update" /></div>
							<div class="grid-3h" >##mail##</div>
							<div class="grid-1" >
								<span class="selectless grid-12 marginless" >
									<select name="genre" class="gt8-update" >
										<option value="0" {{genre-}}>&nbsp;</option>
										<option value="F" {{genre-f}}>Feminino</option>
										<option value="M" {{genre-m}}>Masculino</option>
									</select>
								</span>
							</div>
							<div class="grid-1" ><input type="text" value="{{age}}" name="birth" class="gt8-update" /></div>
							<div class="grid-3" >//creation\\</div>
						</div>
						{{/FOREACH}}
					</div>
				</div>
			</div>
			<footer class="footer-clear-height" >&nbsp;</footer>
			{{$this->getFooter()}}
		</div>
		<div id="eModalEditor" class="modal input-validation grid-5" >
			<div class="row-12 fluid-12 clearfix-child" >
				<h3 class="grid-10 text-shadow" >
					Nova inscrição de e-mail
				</h3>
				<label class="input-text grid-12 omega margin-bottom" >
					<input type="text" class="grid-12 omega" name="mail" />
					<small>E-mail</small>
				</label>
				<label class="input-text grid-12 omega margin-bottom" >
					<input type="text" class="grid-12 omega" name="name" />
					<small>Nome</small>
				</label>
				<label class="input-text grid-12 omega margin-bottom" >
					<input type="text" class="grid-12 omega" name="birth" />
					<small>Data de nascimento</small>
				</label>
				<label class="input-select prefix-4 grid-8 omega margin-bottom" >
					<strong class="grid-4" >Gênero</strong>
					<span class="e-select grid-12 omega">
						<select name="read" >
							<option value="0" >Gênero</option>
							<option value="F" >Feminino</option>
							<option value="M" >Masculino</option>
						</select>
						<span class="button group-button" ><strong>Gênero</strong><img alt="" src="{{CROOT}}imgs/gt8/arrow-down-mini.png" /></span>
					</span>
				</label>
				<hr class="grid-12" />
				<label class="grid-12 marginless" >
					<em id="eAttrFeedback" class="grid-12 height-hm feedback spinner-small spinner-small-hidden font-size-small" >&nbsp;</em>
				</label>
				<hr class="grid-12" />
				<label class="grid-12 marginless" >
					<a href="?cancelar" class="grid-3 href-button href-button-cancel" ><span>Cancelar</span></a>
					<a href="?salvar" class="grid-7 href-button href-button-ok margin-left-2 " ><span>Salvar</span></a>
				</label>
			</div>
		</div>
	</body>
</html>