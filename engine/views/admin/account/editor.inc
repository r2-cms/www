<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Usuário {{login}} | GT8</title>
		{{tag:css|AROOT:users/css/editor.css}}
		{{VIEW:admin.header-default}}
		{{tag:script|CROOT:js/gt8/Editor.js}}
		{{tag:script|AROOT:js/Modal.js}}
		{{tag:script:{{CROOT}}js/gt8/Pager.js}}
		{{tag:script:{{AROOT}}users/js/editor.js}}
		{{tag:script|inline:{{JS_SERVER_VARS}}}}
		<script type="text/javascript" >
			jCube.Include('String.md5');
		</script>
	</head>
	<body class="{{modal-class}}" >
		<div id="eMain" >
			<?php $Editor->printHeader(array(
				"title" 		=> utf8_encode(htmlentities($Editor->data['name'])),
				'directory'	=> $Editor->getDirLocation(),
				'toolbar'	=> $Editor->toolbarItems
			)); ?>
			<div class="Editor-TabbedPaneC body padding-h padding-top" >
				<div class="TabbedPane" >
					<div class="header" >
						<div class="tab" ><div>Geral</div></div>
						<div class="tab" ><div title="Informações vinculadas" >Informações</div></div>
						<div class="tab" ><div>Contatos</div></div>
						<div class="tab" ><div>Endereços</div></div>
						<div class="tab" ><div>Log</div></div>
						<?php $Editor->printManagerTab(); ?>
						<div class="clear"></div>
					</div>
					<div class="body" >
						<div class="card input-validation" id="card-general" >
							<?php $Editor->writeInputLabeled('name', 'Nome', 'required', '', '', array()); ?>
							<?php $Editor->writeInputLabeled('login', 'Login', 'positive', '', '', array('readonly'=>true)); ?>
							<label class="line positive" >
								<strong class="col-5" >Nível</strong>
								<span class="col-6 e-select" >
									<select name="level" class="gt8-update" >
										{{?$this->createComboLevels({{level}})}}
									</select>
									<span class="button group-button" >
										<strong>Escolha uma opção</strong>
										<img class="hidden" alt="" src="{{CROOT}}imgs/gt8/arrow-down-mini.png" />
									</span>
								</span>
								<em class="col-6" >&nbsp;</em>
							</label>
							<label class="line" title="">
								<strong class="col-5">Senha</strong>
								<span class="col-6" >
									<a href="#alterar-senha" id="eChangePassBt" class="col-6 href-button href-button-ok marginless-left" ><span>Alterar senha</span></a>
								</span>
								<em class="col-6">&nbsp;</em>
							</label>
						</div>
						<div class="card input-validation" id="card-info" >
							<?php $Editor->writeSelectLabeled('natureza', 'Natureza', '', array(array('F', 'Física'), array('J', 'Jurídica'))); ?>
							<?php $Editor->writeInputLabeled('cpfcnpj', 'CPF/CNPJ', '', '', '', array('colWidth'=>6)); ?>
							<?php $Editor->writeInputLabeled('document', 'Documento', '', '', '', array('colWidth'=>6)); ?>
							<?php $Editor->writeSelectLabeled('genre', 'Gênero', '', array(array('M', 'Masculino'), array('F', 'Feminino'))); ?>
							<?php $Editor->writeInputLabeled('birth', 'Data de nascimento', '', '', '', array('title'=>'##/##/####', 'type'=> 'date')); ?>
						</div>
						<div class="card input-validation" id="card-contact" >
							<div class="holder" >
								<?php $Editor->printEditableContacts('
								<div class="line margin-bottom" id="eContactTemplate" >
									<span class="col-5 e-select" title="Descrição do canal de comunicação" >
										<select class="gt8-update" name="contact.channel" >
											@@##channels##@@
										</select>
										<span class="button group-button out-hidden" >
											<strong>@@##channel##@@</strong>
											<img src="'. CROOT .'imgs/gt8/arrow-down-mini.png" alt="" />
										</span>
									</span>
									<span class="col-5 e-select" title="Escolha o tipo de contato" >
										<select class="gt8-update" name="contact.type" >
											@@##types##@@
										</select>
										<span class="button group-button out-hidden" >
											<strong>@@##type##@@</strong>
											<img src="'. CROOT .'imgs/gt8/arrow-down-mini.png" alt="" />
										</span>
									</span>
									<span class="col-6 overflow-visible" >
										<input type="text" value="@@##value##@@" name="contact.value" class="input-rounded-shadowed gt8-update out-hidden" />
									</span>
									<a href="excluir-contato/" onclick="Editor.deleteContact(this); return false;" class="button top-marginless small-padding" title="Exclui este contato" ><img src="'. CROOT .'imgs/gt8/delete-small.png" alt="" /></a>
								</div>
								'); ?>
								<div class="line" >
									<a href="novo-contato/" onclick="Editor.insertContact(); return false" class="col-18bm href-button href-button-ok" ><span>Adicionar nova forma de contato</span></a>
								</div>
							</div>
						</div>
						<div class="card input-validation" id="card-address" >
							<div class="cards" >
								{{METHOD:getAddress}}
								<a title="address-4" href="{{AROOT}}address/{{id}}/" class="address card card-border col-7" id="address-{{id}}" >
									<em class="zip">{{zip}}</em>
									<span class="imgC" >
										<img alt="" src="{{CROOT}}imgs/gt8/address/{{type}}.png" />
									</span>
									<strong class="estado">{{stt}} - ##city##</strong>
									<span class="title">##street##, {{number}} - ##complement##. ##district##</span>
									<span class="id_users hidden">{{id_users}}</span>
									<span class="stt hidden">{{stt}}</span>
									<span class="city hidden">##city##</span>
									<span class="street hidden">##street##</span>
									<span class="number hidden">##number##</span>
									<span class="district hidden">##district##</span>
								</a>
								{{/METHOD}}
							</div>
						</div>
						<div class="card input-validation" id="card-history" >
							<?php $Editor->printLog(); ?>
						</div>
						<?php $Editor->printManagerCard(); ?>
					</div>
				</div>
				<?php $Editor->printManagerModal(); ?>
			</div>
			<?php $Editor->printModalButtons(); ?>
			<footer class="footer-clear-height" >&nbsp;</footer>
			<?php $Editor->printFooter(); ?>
			<!-- CUSTOMS  -->
			<div id="eModalPassEdit" class="modal input-validation" >
				<h3 class="col-10 text-shadow">
						Alterar senha
				</h3>
				<label title="minlength:1" class="line required col-10" >
					<span><input id="ePassInput" type="text" value="" class="input-rounded-shadowed" /><small>Senha</small></span>
				</label>
				<label title="minlength:1" class="line padding-v required col-10" >
					<span><input id="ePassConfirm" type="text" value="" class="input-rounded-shadowed" /><small>Confirme a senha</small></span>
				</label>
				<label class="line" >
					<em class="feedback col-10 spinner-small spinner-small-hidden" >&nbsp;</em>
				</label>
				<label class="line">
					<a class="col-3 href-button href-button-cancel" href="#cancel" ><span>Cancelar</span></a>
					<a class="col-7 href-button href-button-ok" href="#" ><span>OK</span></a>
				</label>
			</div>
		</div>
	</body>
</html>