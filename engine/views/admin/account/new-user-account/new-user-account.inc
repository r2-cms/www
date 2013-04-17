<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Administração de usuários | GT8</title>
		{{tag:css:css/new-user-account.css}}
		{{VIEW:admin.header-default}}
		{{tag:script|CROOT:js/gt8/Editor.js}}
		{{tag:script:js/new-user-account.js}}
		{{tag:script|AROOT:js/Modal.js}}
		{{tag:script|inline:{{JS_SERVER_VARS}}}}
	</head>
	<body class="{{modal-class}}" >
		<div id="eMain" >
			<?php $this->printHeader(array(
				"h1" 		=> 'Novo registro',
				'directory'	=> $this->getDirLocation(),
				'toolbar'	=> $this->toolbarItems
			)); ?>
			<div class="Editor-new-item padding-top col-22 border-radius input-validation" >
				<h2>Registro de usuário</h2>
				<hr />
				<label class="line required waiting margin-top" >
					<strong class="col-5" >Login</strong>
					<span class="col-12" >
						<input type="text" value="" name="login" class="gt8-update input-rounded-shadowed" />
						<small class="hidden" >Login</small>
					</span>
					<em class="col-5" >&nbsp;</em>
				</label>
				<label class="line required" >
					<strong class="col-5" >Nome completo</strong>
					<span class="col-12" >
						<input type="text" value="" name="name" class="gt8-update input-rounded-shadowed" />
						<small class="hidden" >Nome</small>
					</span>
					<em class="col-5" >&nbsp;</em>
				</label>
				<label class="line positive" >
					<strong class="col-5" >Nível</strong>
					<span class="col-6 e-select" >
						<select name="level" class="gt8-update" >
							{{$this->getComboLevel()}}
						</select>
						<span class="button group-button" >
							<strong>Escolha uma opção</strong>
							<img class="hidden" alt="" src="{{CROOT}}imgs/gt8/arrow-down-mini.png" />
						</span>
					</span>
					<em class="col-6" >&nbsp;</em>
				</label>
				<label class="line positive" >
					<strong class="col-5" >Moderador</strong>
					<span class="col-6 e-select" >
						<select name="approval_level_required" class="gt8-update" >
							{{$this->getComboLevel(7)}}
						</select>
						<span class="button group-button" >
							<strong>Escolha uma opção</strong>
							<img class="hidden" alt="" src="../../../imgs/gt8/arrow-down-mini.png" />
						</span>
					</span>
					<em class="col-6" >&nbsp;</em>
				</label>
				<label id="eModalBtC" class="line" >
					<a href="javascript: Modal.hide( true); " class="col-5 href-button href-button-cancel" ><span>Cancelar</span></a>
					<a href="javascript: Editor.createNew();" class="col-12 href-button href-button-ok href-button-disabled" ><span>Concluir</span></a>
				</label>
			</div>
			<div class="overlay" >&nbsp;</div>
		</div>
	</body>
</html>