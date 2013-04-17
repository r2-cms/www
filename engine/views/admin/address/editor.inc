<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Edição de endereço {{id}} | GT8</title>
		{{tag:css|AROOT:address/css/editor.css}}
		{{VIEW:admin.header-default}}
		{{tag:script|CROOT:js/gt8/Editor.js}}
		{{tag:script|AROOT:js/Modal.js}}
		{{tag:script:{{CROOT}}js/gt8/Pager.js}}
		{{tag:script:{{AROOT}}address/js/editor.js}}
		{{tag:script|inline:{{JS_SERVER_VARS}}}}
		<script type="text/javascript" >
			jCube.Include('String.md5');
		</script>
	</head>
	<body class="{{modal-class}}" >
		<div id="eMain" >
			<?php $this->printHeader(array(
				"title" 		=> utf8_encode(htmlentities($this->data['name'])),
				'directory'	=> $this->getDirLocation(),
				'toolbar'	=> $this->toolbarItems
			)); ?>
			<div class="Editor-TabbedPaneC body padding-h padding-top" >
				<div class="TabbedPane" >
					<div class="header" >
						<div class="tab" ><div>Geral</div></div>
						<div class="tab" ><div title="Descreva como chegar até sua residência" >Referência</div></div>
						<div class="tab" ><div>Log</div></div>
						<div class="clear"></div>
					</div>
					<div class="body" >
						<div class="card input-validation" id="card-general" >
							<label class="line required" >
								<strong class="col-5" >Tipo</strong>
								<span class="col-6 e-select" >
									<select name="id_type" class="gt8-update" >
										{{METHOD:getAddressTypes}}
										<option value="{{id}}" {{selected}} >##type##</option>
										{{/METHOD}}
									</select>
									<span class="button group-button">
										<strong>Escolha uma opção</strong>
										<img alt="" src="{{CROOT}}imgs/gt8/arrow-down-mini.png" />
									</span>
								</span>
								<em class="col-6">&nbsp;</em>
							</label>
							<label class="line required positive" >
								<strong class="col-5" >CEP</strong>
								<span class="col-6" >
									<input type="text" value="{{zip}}" name="zip" class="gt8-update input-rounded-shadowed" />
									<small class="hidden" >CEP</small>
								</span>
								<em class="col-6">&nbsp;</em>
							</label>
							<label class="line required" >
								<strong class="col-5">Estado</strong>
								<span class="col-6 e-select" >
									<select name="stt" class="gt8-update" >
										{{$this->setUfs()}}
										{{FOREACH:ufs}}
										<option value="{{uf}}" {{selected}}>##name##</option>
										{{/FOREACH}}
									</select>
									<span class="button group-button">
										<strong>Escolha uma opção</strong>
										<img alt="" src="{{CROOT}}imgs/gt8/arrow-down-mini.png" />
									</span>
								</span>
								<em class="col-6">&nbsp;</em>
							</label>
							<label class="line required positive" >
								<strong class="col-5" >Cidade</strong>
								<span class="col-6" >
									<input type="text" value="##city##" name="city" class="gt8-update input-rounded-shadowed" />
									<small>Cidade</small>
								</span>
								<em class="col-6">&nbsp;</em>
							</label>
							<label class="line required positive" >
								<strong class="col-5" >Bairro</strong>
								<span class="col-6" >
									<input type="text" value="##district##" name="district" class="gt8-update input-rounded-shadowed" />
									<small>Bairro</small>
								</span>
								<em class="col-6">&nbsp;</em>
							</label>
							<label class="line required positive" >
								<strong class="col-5" >Logradouro</strong>
								<span class="col-9" >
									<input type="text" value="##street##" name="street" class="gt8-update input-rounded-shadowed" />
									<small>Logradouro</small>
								</span>
								<em class="col-6">&nbsp;</em>
							</label>
							<label class="line required positive" >
								<strong class="col-5" >Número</strong>
								<span class="col-3" >
									<input type="text" value="##number##" name="number" class="gt8-update input-rounded-shadowed" />
									<small>Número</small>
								</span>
							</label>
							<label class="line required positive" style="clear:none;" >
								<span class="col-6" >
									<input type="text" value="##complement##" name="complement" class="gt8-update input-rounded-shadowed" />
									<small>Complemento</small>
								</span>
							</label>
						</div>
						<div class="card input-validation" id="card-reference" >
							<label title="maxlength:250" class="line" >
									<strong class="col-5" >Referência</strong>
									<span class="col-14" >
										<textarea name="reference" class="gt8-update input-rounded-shadowed" rows="1" cols="1" >##reference##</textarea>
										<small class="">Referência</small>
									</span>
									<em class="col-6" >&nbsp;</em>
							</label>
						</div>
						<div class="card input-validation" id="card-history" >
							<?php $this->printLog(); ?>
						</div>
					</div>
				</div>
			</div>
			<?php $this->printModalButtons(); ?>
			{{$this->getFooter()}}
		</div>
	</body>
</html>