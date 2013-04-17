<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>File Editor | GT8</title>
		<link rel="stylesheet" type="text/css" href="{{AROOT}}explorer/css/fileEditor.css" />
		{{VIEW:admin.header-default}}
		{{tag:script|inline:{{JS_SERVER_VARS}}}}
		<script type="text/javascript" src="{{CROOT}}js/gt8/Editor.js" ></script>
		<script type="text/javascript" src="{{AROOT}}explorer/js/fileEditor.js" ></script>
		{{PLUGGIN:css}}
		<meta name="viewport" content="width=device-width,initial-scale=1" />
	</head>
	<body class="{{modal-class}}" >
		<div id="eMain" >
			<?php $this->printHeader(array(
				"h1" 		=> 'Explorer | <a class="link" href="{{CROOT}}{{GT8:explorer.root}}'. $this->Pager['rows'][0]['path'] . ($this->type=='file'? $this->Pager['rows'][0]['filename']:'') .'" >#'. $this->id .'</a>',
				'directory'	=> $this->getDirLocation(),
				'toolbar'	=> $this->toolbarItems
			)); ?>
			<div class="Editor-TabbedPaneC body padding-h padding-top" >
				<div class="TabbedPane" >
					<div class="header" >
						<div class="tab resumo" ><div>Resumo</div></div>
						<div class="tab dados" ><div>Dados</div></div>
						<div class="tab imagem" ><div>Imagem</div></div>
						<div class="tab privilegios" ><div>Privilégios</div></div>
						<div class="tab atributos" ><div>Atributos</div></div>
						<?php $this->printManagerTab(); ?>
						{{PLUGGIN:tabs}}
						<div class="clear"></div>
					</div>
					<div class="body" >
						<div class="card resumo line-height" >
							<div class="line" >
								<div class="col-4" ><strong>ID</strong></div>
								<div class="col-6" >{{id}}</div>
							</div>
							<div class="line" >
								<div class="col-4" ><strong>Autor</strong></div>
								<div class="col-6" >##user##</div>
							</div>
							<div class="line" >
								<div class="col-10 bg-head" ><strong>Vizualizações</strong></div>
							</div>
							<div class="line" >
								<div class="col-4" ><strong>Total:</strong></div>
								<div class="col-6" >{{vtotal}}</div>
							</div>
							<div class="line" >
								<div class="col-4" ><strong>Este mês:</strong></div>
								<div class="col-6" >{{vmonth}}</div>
							</div>
							<div class="line" >
								<div class="col-4" ><strong>Esta semana:</strong></div>
								<div class="col-6" >{{vweek}}</div>
							</div>
							<div class="line" >
								<div class="col-4" ><strong>Hoje:</strong></div>
								<div class="col-6" >{{vtoday}}</div>
							</div>
							<div class="line" >
								<div class="col-10 bg-head" ><strong>Tamanho</strong></div>
							</div>
							{{$this->getSizeInfo()}}
							<div class="line" >
								<div class="col-10 bg-head" ><strong>Datas</strong></div>
							</div>
							<div class="line" >
								<div class="col-4" ><strong>Criação:</strong></div>
								<div class="col-6" >{{creation}}</div>
							</div>
							<div class="line" >
								<div class="col-4" ><strong>Modificação:</strong></div>
								<div class="col-6" >{{modification}}</div>
							</div>
						</div>
						<div class="card dados input-validation" >
							<label class="line" >
								<strong class="col-5">Título</strong>
								<span class="col-10"><input type="text" value="##title##" name="title" class="gt8-update input-rounded-shadowed" /><small>Título</small></span>
								<em class="col-6" >&nbsp;</em>
							</label>
							<label class="line readonly" >
								<strong class="col-5" >Nome de arquivo</strong>
								<span class="col-10" ><input type="text" value="##filename##" name="filename" class="gt8-update input-rounded-shadowed readonly" readonly="readonly" /><small>Nome de arquivo</small></span>
								<em class="col-6" >&nbsp;</em>
							</label>
							<label class="line" >
								<strong class="col-5" >Código</strong>
								<span class="col-10" ><input type="text" value="##code##" name="code" class="gt8-update input-rounded-shadowed" /><small>Código</small></span>
								<em class="col-6" >&nbsp;</em>
							</label>
							<label title="maxlength:128" class="line" >
								<strong class="col-5" >Resumo</strong>
								<span class="col-14" ><textarea style="height:78px" name="sumary" class="gt8-update input-rounded-shadowed" rows="1" cols="1" >##sumary##</textarea><small>Resumo</small></span>
								<em class="col-6" >&nbsp;</em>
							</label>
							<label title="maxlength:4096" class="line" >
								<strong class="col-5" >Descrição</strong>
								<span class="col-14" ><textarea name="description" class="gt8-update input-rounded-shadowed" rows="1" cols="1" >##description##</textarea><small>Descrição</small></span>
								<em class="col-6" >&nbsp;</em>
							</label>
						</div>
						<div class="card imagem" >
							<div class="line" >
								<div class="col-<?php print($this->isModal?6:8); ?> imgC locked-{{locked}}" >
									<a class="preview" href="{{CROOT}}{{GT8:explorer.root}}{{$this->data['path']}}{{filename}}" title="Visualização real imagem" >&nbsp;</a>
									<div class="glass-lock" ></div>
								</div>
								<table class="col-<?php print($this->isModal?8:10); ?> list-filter no-cellspacing bordered" >
									<tr>
										<td><strong>Tipo de arquivo</strong></td>
										<td><span>{{mime}}</span></td>
									</tr>
									<?php $this->printMimeSpecificInfo() ?>
									<tr>
										<td><strong>Data de criação</strong></td>
										<td><span>{{creation}}</span></td>
									</tr>
									<tr>
										<td><strong>Data de modificação</strong></td>
										<td><span id="eModification" >{{modification}}</span></td>
									</tr>
									<tr>
										<td><strong>Tamanho</strong></td>
										<td><span id="eSize" >{{??number_format({{size}}, 0, ',', '.')}} B</span></td>
									</tr>
								</table>
							</div>
						</div>
						<div class="card privileges line-height" >
							<div class="line" >
								<div class="col-4" ><strong>Leitura</strong></div>
								<div class="col-6" ><?php $this->prnt('read_privilege'); ?></div>
							</div>
							<div class="line" >
								<div class="col-4" ><strong>Escrita</strong></div>
								<div class="col-6" ><?php $this->prnt('write_privilege'); ?></div>
							</div>
						</div>
						<div class="card attributes input-validation line-height" >
							<div class="card resumo line-height over <?php print($this->type=='file'? 'hidden': ''); ?> clearfix-child row-12 fluid-12 fullwidth clearfix-child" >
								<div class="grid-12 bg-head" >
									<div class="grid-4" title="Nome do atributo" ><strong>Atributo</strong></div>
									<div class="grid-2" title="Quem poderá visualizar este atributo" ><strong>Privilégio</strong></div>
									<div class="grid-4" title="Tipo de valor permitido" ><strong>Definição</strong></div>
									<div class="grid-1" title="Prefixo" ><strong>Prefixo</strong></div>
									<div class="grid-1" title="Sufixo" ><strong>Sufixo</strong></div>
								</div>
								<?php $this->printAttributes('
									<div class="grid-12 line cursor-pointer" id="e-attr-{{id}}" >
										<div class="att grid-4 overflow" >
											<img src="{{CROOT}}imgs/gt8/delete-aqua-small.png" width="24" height="24" alt="[excluir]" />
											<span>[[attribute]]</span>
										</div>
										<div title="{{level}}" class="lvl grid-2 overflow" >{{level}}</div>
										<div title="{{type}}" class="typ grid-4 overflow" >##type##</div>
										<div class="pfx grid-1 overflow" >##prefix##</div>
										<div class="sfx grid-1 overflow" >##suffix##</div>
									</div>
									');
								?>
							</div>
							<div class="card resumo line-height over <?php print($this->type=='file'? '': 'hidden'); ?> clearfix-child" >
								<div class="line bg-head margin-bottom-medium" >
									<div class="col-5" title="Nome do atributo" ><strong>Atributo</strong></div>
									<div class="col-2" title="Prefixo" ><strong>Prefixo</strong></div>
									<div class="col-5" title="Valor permitido" ><strong>Valor</strong></div>
									<div class="col-1" title="Sufixo" ><strong>Suf.</strong></div>
								</div>
								<?php $this->printAttributesValue('
									<label class="line attr-value clearfix-child" id="e-attr-{{id_attributes}}-{{id_explorer}}" >
										<strong class="att col-5" >##attribute##</strong>
										<strong class="pfx col-2" >##prefix##&nbsp;</strong>
										<span class="typ col-5 editable-{{editable}}" >
											{{input}}
											<small>&nbsp;</small>
										</span>
										<strong class="sfx col-1" >##suffix##&nbsp;</strong>
										<span class="clear" >&nbsp;</span>
									</label>
									');
								?>
								<div class="clear" ></div>
							</div>
							<label class="margin-top-large <?php print($this->type=='file'? 'hidden': ''); ?>" >
								<a id="eAttrEdit" href="#editar" class="col-4 href-button href-button-cancel" ><span>Excluir</span></a>
								<a id="eAttrShowModal" href="#show-modal" class="col-10 href-button href-button-ok" ><span>Adicionar categoria</span></a>
								<span class="clear" >&nbsp;</span>
							</label>
						</div>
						{{PLUGGIN:cards}}
						<?php $this->printManagerCard(); ?>
					</div>
				</div>
				<?php $this->printManagerModal(array('html' => $this->getAllowModeration())); ?>
			</div>
			<?php $this->printModalButtons(); ?>
			<footer class="footer-clear-height" >&nbsp;</footer>
			<?php $this->printFooter(); ?>
			<div class="modal input-validation" id="eModalAttributeNew" >
				<h3 class="col-10 text-shadow">
					Adicionar nova categoria de atributo
				</h3>
				<label class="line required col-10" title="minlength:1">
					<span><input id="eAttrProperty" type="text" class="input-rounded-shadowed" /><small>Atributo</small></span>
				</label>
				<span id="eAttrChooser" class="line e-select col-10 overflow-visible margin-bottom-small" >
					<select >
						<option value="0" >Escolha uma opção</option>
						<option value="string" >Texto</option>
						<option value="integer" >Número inteiro</option>
						<option value="float" >Número flutuante</option>
						<option value="enum" >Lista de opções</option>
					</select>
					<span class="button group-button"><strong>Escolha uma opção</strong><img src="{{CROOT}}imgs/gt8/arrow-down-mini.png" alt="" /></span>
				</span>
				<label id="eAttrEnumChooser" class="line required hidden margin-bottom-small" title="minlength:1" >
					<span class="col-8 paddingless" >
						<input type="text" class="input-rounded-shadowed" />
						<small>lista;com;ponto;e;vírgula</small>
					</span>
					<a href="#" class="col-2bm button top-marginless small-padding" >
						<img class="margin-centered" src="{{CROOT}}imgs/gt8/delete-small.png" alt="" />
					</a>
				</label>
				<label class="line required col-10" title="minlength:1" >
					<span><input id="eAttrPrefix" type="text" class="input-rounded-shadowed" /><small>Prefixo</small></span>
				</label>
				<label class="line required col-10" title="minlength:1" >
					<span><input id="eAttrSuffix" type="text" class="input-rounded-shadowed" /><small>Sufixo</small></span>
				</label>
				<span id="eAttrLevelChooser" class="line e-select col-10 overflow-visible" >
					<select >
						<option value="0" >Visualização permitida a usuário:</option>
						{{$this->createComboLevels()}}
					</select>
					<span class="button group-button"><strong>Visualização permitida a usuário:</strong><img src="{{CROOT}}imgs/gt8/arrow-down-mini.png" alt="" /></span>
				</span>
				<label class="line" >
					<em id="eAttrFeedback" class="col feedback spinner-small spinner-small-hidden" >&nbsp;</em>
					<span class="clear" >&nbsp;</span>
				</label>
				<label class="line" >
					<a href="?cancel" class="col-3 href-button href-button-cancel" ><span>Cancelar</span></a>
					<a href="?createNewFolder" class="col-7 href-button href-button-ok" ><span>OK</span></a>
				</label>
			</div>
		</div>
		{{PLUGGIN:editor.js}}
	</body>
</html>