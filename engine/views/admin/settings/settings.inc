<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Configurações gerais | GT8</title>
		<link rel="stylesheet" type="text/css" href="css/settings.css" />
		{{VIEW:admin.head-default}}
		<script type="text/javascript" src="js/settings.js" ></script>
		<script type="text/javascript" >
			{{JS_SERVER_VARS}}
		</script>
	</head>
	<body class="{{modal-class}} overflow-auto" >
		<div id="eMain" >
			<?php
				$x	= '<a id="eBtDel" title="Excluir parâmetro" href="?action=remove-param" class="button" ><img alt="[icon]" src="{{CROOT}}imgs/gt8/cancel-small.png" /></a>';
				$x	.= '<a id="eBtAdd" title="Adicionar novo parâmetro" href="?action=add-param" class="button" ><img alt="[icon]" src="{{CROOT}}imgs/gt8/add-small.png" /></a>';
				$this->setData('toolbar', $x);
			?>
			{{VIEW:admin.header-new}}
			<div id="eResults" class="row-12 fluid-12 margin-top-large" >
				<div class="padding-large card-white" >
					<div class="header clearfix-child line-height-large bg-light box-shadow" >
						<div class="grid-3" ><strong>Nome</strong></div>
						<div class="grid-3" ><strong>Valor</strong></div>
						<div class="grid-3" ><strong>Leitura</strong></div>
						<div class="grid-3" ><strong>Escrita</strong></div>
					</div>
					<div class="body clearfix-child cursor-pointer line-height-medium" >
						{{$this->setParams()}}
						{{FOREACH:params}}
						<div class="grid-12 marginless" id="param-{{id}}" >
							<div class="grid-3" >##name##</div>
							<div class="grid-3" >##value##</div>
							<div class="grid-3" title="{{read_privilege}}" >##pread##</div>
							<div class="grid-3" title="{{write_privilege}}" >##pwrite##</div>
						</div>
						{{/FOREACH}}
					</div>
				</div>
			</div>
			<footer class="footer-clear-height" >&nbsp;</footer>
			{{$this->getFooter()}}
		</div>
		<div id="eModalEditor" class="modal input-validation" >
			<div class="row-12 fluid-12 clearfix-child" >
				<h3 class="grid-10 text-shadow">
					Parâmetro
				</h3>
				<label class="input-text grid-12 omega margin-bottom" >
					<input type="text" class="grid-12 omega" name="name" readonly="readonly" />
					<small>Parâmetro</small>
				</label>
				<label class="input-text grid-12 omega margin-bottom" >
					<input type="text" class="grid-12 omega" name="value" />
					<small>Valor</small>
				</label>
				<hr class="grid-12" />
				<div class="grid-12 margin-bottom-small" >
					Privilégios
				</div>
				<label class="input-select prefix-4 grid-8 omega margin-bottom" >
					<strong class="grid-4 text-align-right" >Leitura</strong>
					<span class="e-select grid-12 omega">
						<select name="read" >
							{{$this->createComboLevels()}}
						</select>
						<span class="button group-button" ><strong>Permitido a:</strong><img alt="" src="{{CROOT}}imgs/gt8/arrow-down-mini.png" /></span>
					</span>
				</label>
				<label class="input-select prefix-4 grid-8 omega margin-bottom" >
					<strong class="grid-4 text-align-right" >Escrita</strong>
					<span class="e-select grid-12 omega" >
						<select name="write" >
							{{$this->createComboLevels()}}
						</select>
						<span class="button group-button" ><strong>Permitido a:</strong><img src="{{CROOT}}imgs/gt8/arrow-down-mini.png" alt="" /></span>
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