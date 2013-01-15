						<div class="card article line-height" >
							<div class="cards" >
								<a id="eSaveArticleButton" class="button save hidden" href="#" ><img src="{{CROOT}}{{GT8:admin.root}}explorer/imgs/save-small.png" alt="[icon]" title="Salvar alterações no artigo" class="float-left" width="32" height="32" /><small class="padding-right" >Salvar alterações</small></a>
								<div id="eArticleEditor" >##description##</div>
								<div id="ePublishAttach" class="hidden actions clearfix-child margin-top-mini" >
									<hr class="margin-bottom-small" />
									<div class="clearfix-child line editor-fontSize" >
										<label class="select-encapsuled halfwidth float-left margin-right-none" >
											<select class="editor-fontSize" name="editor-fontSize" >
												<option value="editor-fontSize-1" >Mínimo</option>
												<option value="editor-fontSize-2" >Extra pequeno</option>
												<option value="editor-fontSize-3" >Pequeno</option>
												<option selected="selected" value="editor-fontSize-none" >Normal</option>
												<option value="editor-fontSize-5" >Grande</option>
												<option value="editor-fontSize-6" >Extra grande</option>
											</select>
											<span><span>Alinhamento</span><img alt="" src="{{CROOT}}imgs/gt8/arrow-down-mini.png" /></span>
										</label>
										<label class="select-encapsuled halfwidth margin-right-none float-right" >
											<select class="editor-formatBlock" name="editor-formatBlock" >
												<option selected="selected" value="editor-formatBlock-div" >Parágrafo</option>
												<option value="editor-formatBlock-h1" >Título 1</option>
												<option value="editor-formatBlock-h2" >Título 2</option>
												<option value="editor-formatBlock-h3" >Título 3</option>
												<option value="editor-formatBlock-h4" >Título 4</option>
												<option value="editor-formatBlock-h5" >Título 5</option>
												<option value="editor-formatBlock-h6" >Título 6</option>
											</select>
											<span><span>Alinhamento</span><img alt="" src="{{CROOT}}imgs/gt8/arrow-down-mini.png" /></span>
										</label>
									</div>
									<div class="clearfix-child" >
										<span class="action-justify group-button group-unique group-toggle" >
											<button class="editor-justifyLeft" ><img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="Alinhar à esquerda|{{CROOT}}imgs/gt8/sprite-editor.png|50|0,15" alt="" /></button>
											<button class="editor-justifyCenter" ><img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="Alinhar ao centro|{{CROOT}}imgs/gt8/sprite-editor.png|50|0,16" alt="" /></button>
											<button class="editor-justifyRight" ><img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="Alinhar à direita|{{CROOT}}imgs/gt8/sprite-editor.png|50|0,17" alt="" /></button>
											<button class="editor-justifyFull" ><img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="Justificar|{{CROOT}}imgs/gt8/sprite-editor.png|50|0,18" alt="" /></button>
										</span>
										<span class="action-style group-button group-toggle margin-right-none" >
											<button class="editor-bold" ><img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="Negrito|{{CROOT}}imgs/gt8/sprite-editor.png|50|0,0" alt="" ></button>
											<button class="editor-italic" ><img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="Itálico|{{CROOT}}imgs/gt8/sprite-editor.png|50|0,1" alt="" ></button>
											<button class="editor-underline" ><img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="Sublinhado|{{CROOT}}imgs/gt8/sprite-editor.png|50|0,2" alt="" ></button>
											<button class="editor-strikeThrough" ><img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="Linha|{{CROOT}}imgs/gt8/sprite-editor.png|50|0,3" alt="" ></button>
										</span>
									</div>
									<div class="clearfix-child margin-bottom-small" >
										<span class="action-img_link group-button group-unique group-toggle" >
											<button class="editor-openImageEditor" ><img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="Inserir imagem|{{CROOT}}imgs/gt8/sprite-editor.png|50|0,10" alt="" /></button>
											<button class="editor-createLink" ><img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="Insere hyperlink|{{CROOT}}imgs/gt8/sprite-editor.png|50|0,21" alt="" /></button>
										</span>
										<span class="action-indent group-button group-trigger-only" >
											<button class="editor-indent" ><img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="Aumenta indentação|{{CROOT}}imgs/gt8/sprite-editor.png|50|0,12" alt="" /></button>
											<button class="editor-outdent" ><img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="Diminui indentação|{{CROOT}}imgs/gt8/sprite-editor.png|50|0,11" alt="" /></button>
										</span>
										<span class="action-list group-button group-toggle group-unique" >
											<button class="editor-insertOrderedList" ><img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="Insere/remove lista numerada|{{CROOT}}imgs/gt8/sprite-editor.png|50|0,26" alt="" /></button>
											<button class="editor-insertUnorderedList" ><img src="{{CROOT}}imgs/gt8/blank.gif" class="auto-sprite" title="Insere/remove lista sem numeração|{{CROOT}}imgs/gt8/sprite-editor.png|50|0,24" alt="" /></button>
										</span>
									</div>
									<div class="clearfix-child margin-bottom-small line editor-link hidden" >
										<label class="input-encapsuled fullwidth" >
											<span>Link</span>
											<input value="" />
										</label>
									</div>
									<div class="clearfix-child margin-bottom-small line editor-image hidden" >
										<label class="input-encapsuled fullwidth input-encapsuled fullwidth margin-bottom-small" >
											<span>URL</span>
											<input value="" name="editor-img-src" />
										</label>
										<label class="select-encapsuled fullwidth input-encapsuled fullwidth margin-bottom-small" >
											<select class="editor-img-align" name="editor-img-align" >
												<option selected="selected" value="none" >Sem alinhamento</option>
												<option value="left" >Alinhado à esquerda</option>
												<option value="right" >Alinhado à direita</option>
											</select>
											<span><span>Alinhamento</span><img alt="" src="{{CROOT}}imgs/gt8/arrow-down-mini.png" /></span>
										</label>
										<label class="select-encapsuled fullwidth input-encapsuled fullwidth margin-bottom-small" >
											<select class="editor-img-size" name="editor-img-size" >
												<option value="small" >Tamanho pequeno</option>
												<option value="regular" >Tamanho padrão</option>
												<option value="preview" >Tamanho grande</option>
												<option value="default" >Tamanho real</option>
											</select>
											<span><span>Tamanho pequeno</span><img alt="" src="{{CROOT}}imgs/gt8/arrow-down-mini.png" /></span>
										</label>
										<label class="input-encapsuled fullwidth" >
											<span>Margens</span>
											<input value="0 10px 10px 10px" name="editor-img-margin" />
										</label>
									</div>
								</div>
							</div>
						</div>