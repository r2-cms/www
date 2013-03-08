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
							<?php $this->writeSelectLabeled('id_type', 'Tipo de endereço', 'required', $this->getType()); ?>
							<?php $this->writeInputLabeled('zip', 'CEP', 'required', '', '', array('colWidth'=>6)); ?>
							<?php $this->writeSelectLabeled('stt', 'Estado', 'required', $this->getStt()); ?>
							<?php $this->writeInputLabeled('city', 'Cidade', 'required'); ?>
							<?php $this->writeInputLabeled('district', 'Bairro', 'required'); ?>
							<?php $this->writeInputLabeled('street', 'Logradouro', 'required'); ?>
							<?php $this->writeInputLabeled('number', 'Número', 'required','','', array('colWidth'=>3,'noEM'=>true)); ?>
							<?php $this->writeInputLabeled('complement', 'Complemento', '', '', '', array('colWidth'=>($this->isModal?6:7), 'noSTRONG'=>true, 'styleLABEL'=>'clear:none;')); ?>
						</div>
						<div class="card input-validation" id="card-reference" >
							<?php $this->writeTextareaLabeled('reference', 'Referência', '', '', 250); ?>
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