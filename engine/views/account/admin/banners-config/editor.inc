<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Configuração de banner | GT8</title>
		<link rel="stylesheet" type="text/css" href="../css/editor.css" />
		<link rel="stylesheet" type="text/css" href="{{CROOT}}css/gt8/gt8.css" />
		<link rel="stylesheet" type="text/css" href="{{CROOT}}{{GT8:admin.root}}css/admin.css" />
		<script type="text/javascript" src="{{CROOT}}jCube/jCube.single.js" ></script>
		<script type="text/javascript" src="{{CROOT}}js/gt8/gt8.js" ></script>
		<script type="text/javascript" src="{{CROOT}}{{GT8:admin.root}}js/main.js" ></script>
		<script type="text/javascript" src="{{CROOT}}js/gt8/Editor.js" ></script>
		<script type="text/javascript" src="{{CROOT}}{{GT8:admin.root}}js/Modal.js" ></script>
		<script type="text/javascript" src="{{CROOT}}js/gt8/Pager.js" ></script>
		<script type="text/javascript" src="../js/editor.js" ></script>
		<script type="text/javascript" >
			{{JS_SERVER_VARS}}
		</script>
	</head>
	<body class="{{modal-class}}" >
		<div id="eMain" >
			<?php $this->printHeader(array(
				"h1" 		=> 'Configuração de banners',
				'directory'	=> $this->getDirLocation(),
				'toolbar'	=> $this->toolbarItems
			)); ?>
			<div class="Editor-TabbedPaneC body padding-h padding-top" >
				<div class="TabbedPane" >
					<div class="header clearfix-child" >
						<div class="tab" ><div title="Resumo do pedido de compra" >Banners</div></div>
					</div>
					<div class="body row-12 fluid-12 fullwidth padding-none" >
						<div class="card line-height-large padding padding-left-none padding-right-none clearfix-child" >
							<div id="eSortablesC" >
								{{FOREACH:cards}}
								<div title="##title##" class="grid-12 marginless card text-decoration-none text-align-center" >
									<div class="bg-white box-shadow overflow-hidden content" >
										<span class="imgC height-3 grid-12" >
											<img alt="[imagem ilustrativa]" src="{{CROOT}}{{GT8:explorer.root}}{{l_path}}?preview" />
										</span>
										<span class="hidden ref-explorer-path" >{{path}}</span>
										<span class="grid-12 title text-align-left" id="{{id_dir}}" >##title##</span>
										<img alt="[Excluir]" src="{{CROOT}}imgs/gt8/delete-aqua-small.png" class="bt-delete" width="32" height="32" />
										<a href="#Excluir" class="bt-delete-confirm href-button href-button-warning" ><span>Confirmar</span></a>
									</div>
									<div class="grid-12 height-h" >&nbsp;</div>
								</div>
								{{/FOREACH}}
							</div>
							<div id="eCardTemplate" class="grid-12 marginless card text-decoration-none text-align-center hidden" >
								<div class="bg-white box-shadow overflow-hidden" >
									<span class="imgC height-3 grid-12" >
										<img alt="[imagem ilustrativa]" src="{{CROOT}}imgs/gt8/add-regular.png" />
									</span>
									<span class="hidden ref-explorer-path" >banners/</span>
									<span class="grid-12 title text-align-left" >&nbsp;</span>
									<img alt="[Excluir]" src="{{CROOT}}imgs/gt8/delete-aqua-small.png" class="bt-delete" width="32" height="32" />
									<a href="#Excluir" class="bt-delete-confirm href-button href-button-warning" ><span>Confirmar</span></a>
								</div>
								<div class="grid-12 height-h" >&nbsp;</div>
							</div>
						</div>
					</div>
				</div>
				<section class="modal-info" id="ePublishC" >
					<h3 class="margin-bottom" >Gerenciar</h3>
					<p>
						<strong>Atenção!</strong><br /><br />
						Utilize imagens de dimensões iguais
					</p>
					<hr class="margin-bottom" />
					<label id="eRandom" class="random display-block margin-bottom" >
						<a class="href-button href-button-ok" href="{{CROOT}}{{GT8:admin.root}}explorer/banners/" >
							<span>Editar imagens</span>
						</a>
					</label>
				</section>
			</div>
			<footer class="footer-clear-height" >&nbsp;</footer>
			{{METHOD:getFooter}}{{/METHOD}}
			<!-- CUSTOMS  -->
			<div id="eModalChangeStatus" class="modal input-validation" >
				<h3 class="col-10 text-shadow">
						Confirme a alteração de status
				</h3>
				<label title="minlength:1" class="line required col-10" >
					<span>Atenção, esta ação envia um e-mail de notificação ao cliente e é irreversível!</span>
				</label>
				<label class="line">
					<a class="col-3 href-button href-button-cancel" href="#cancel" ><span>Cancelar</span></a>
					<a class="col-7 href-button href-button-ok" href="#" ><span>OK</span></a>
				</label>
			</div>
		</div>
	</body>
</html>