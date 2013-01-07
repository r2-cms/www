<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Configuração das ofertas | GT8</title>
		<link rel="stylesheet" type="text/css" href="../css/offers-config.css" />
		<link rel="stylesheet" type="text/css" href="{{CROOT}}css/gt8/gt8.css" />
		<link rel="stylesheet" type="text/css" href="{{CROOT}}{{GT8:admin.root}}css/admin.css" />
		<script type="text/javascript" src="{{CROOT}}jCube/jCube.single.js" ></script>
		<script type="text/javascript" src="{{CROOT}}js/gt8/gt8.js" ></script>
		<script type="text/javascript" src="{{CROOT}}{{GT8:admin.root}}js/main.js" ></script>
		<script type="text/javascript" src="{{CROOT}}js/gt8/Editor.js" ></script>
		<script type="text/javascript" src="{{CROOT}}{{GT8:admin.root}}js/Modal.js" ></script>
		<script type="text/javascript" src="{{CROOT}}js/gt8/Pager.js" ></script>
		<script type="text/javascript" src="../js/offers-config.js" ></script>
		<script type="text/javascript" >
			{{JS_SERVER_VARS}}
		</script>
		<style type="text/css" >
			.stock {
				color: #888;
				font-size: small;
			}
			.color-0 {
				background: rgba( 155,0,0, 0.2);
			}
			.stock.color-0 {
				background: transparent;
				color: #900;
				font-size: large;
				font-weight: bolder;
			}
		</style>
	</head>
	<body class="{{modal-class}}" >
		<div id="eMain" >
			<?php $this->printHeader(array(
				"h1" 		=> 'Configuração de ofertas',
				'directory'	=> $this->getDirLocation(),
				'toolbar'	=> $this->toolbarItems
			)); ?>
			<div class="Editor-TabbedPaneC body padding-h padding-top" >
				<div class="TabbedPane" >
					<div class="header clearfix-child" >
						<div class="tab" ><div title="Informações" >Geral</div></div>
						<div class="tab" ><div title="Resumo do pedido de compra" >Produtos</div></div>
					</div>
					<div class="body row-12 fluid-12 fullwidth padding-none" >
						<div class="card line-height-large padding padding-left-none padding-right-none clearfix-child" id="eCardGeneral" >
							<div class="clearfix-child" >
								<p class="grid-12" >
									<label class="input-text prefix-4 grid-8 omega" >
										<strong class="grid-4 text-align-right" >Título</strong>
										<input type="text" value="##title##" class="grid-12 omega gt8-update" name="title" />
										<small>&nbsp;</small>
									</label>
								</p>
							</div>
						</div>
						<div class="card line-height-large padding padding-left-none padding-right-none clearfix-child" >
							<div id="eSortablesC" >
								{{FOREACH:cards}}
								<div title="##title##" class="grid-12 marginless card bg-white box-shadow text-decoration-none text-align-center" >
									<span class="imgC height-2 grid-4" >
										<img alt="[imagem ilustrativa]" src="{{CROOT}}{{varname}}/?translate-img=1&preview" />
									</span>
									<span class="hidden ref-explorer-path" >{{l_path}}/</span>
									<span class="grid-8 brand text-align-left" >##brand##</span>
									<span class="grid-8 title text-align-left" id="{{id_dir}}" >##title##</span>
									<strong class="grid-8 text-align-left price_selling currency" >R$ $$price_selling$$</strong>
									<img alt="[Excluir]" src="{{CROOT}}imgs/gt8/delete-aqua-small.png" class="bt-delete" width="32" height="32" />
									<a href="#Excluir" class="bt-delete-confirm href-button href-button-warning" ><span>Confirmar</span></a>
								</div>
								{{/FOREACH}}
							</div>
							<div id="eCardTemplate" class="grid-12 marginless card bg-white box-shadow text-decoration-none text-align-center hidden" >
								<span class="imgC height-2 grid-4" >
									<img alt="[imagem ilustrativa]" src="{{CROOT}}imgs/gt8/add-regular.png" />
								</span>
								<span class="hidden ref-explorer-path" >calcados/</span>
								<span class="grid-8 brand text-align-left" >&nbsp;</span>
								<span class="grid-8 title text-align-left" >&nbsp;</span>
								<strong class="grid-8 text-align-left price_selling currency" >&nbsp;</strong>
								<img alt="[Excluir]" src="{{CROOT}}imgs/gt8/delete-aqua-small.png" class="bt-delete" width="32" height="32" />
								<a href="#Excluir" class="bt-delete-confirm href-button href-button-warning" ><span>Confirmar</span></a>
							</div>
						</div>
					</div>
				</div>
				<section class="modal-info" id="ePublishC" >
					<h3 class="margin-bottom" >Gerenciar</h3>
					<label id="eLimit" class="increaser side-by-side fullwidth margin-bottom" >
						<span><small>Limite</small></span>
						<input value="{{limit}}" name="limit" title="2|60" class="float-right" />
						<a href="#incrementar" class="up"><span>+</span></a>
						<a href="#decrementar" class="dw"><span>-</span></a>
					</label>
					<hr class="margin-bottom" />
					<label id="eRandom" class="random display-block margin-bottom" >
						<a class="checkbox text-align-left {{random-selected}}" >
							Aleatório
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