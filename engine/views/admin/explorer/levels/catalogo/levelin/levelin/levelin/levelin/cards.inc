						<div class="card logistic input-validation" >
							<label title="minlength:1" class="line" >
								<strong class="col-5" >Estoque físico</strong>
								<span class="col-6" ><input type="text" value="{{stock}}" name="stock" class="gt8-update input-rounded-shadowed" /><small class="hidden" >Estoque</small></span>
								<em class="col-6">&nbsp;</em>
							</label>
							<label class="line" >
								<?php
									$Pager	= Pager(array(
										'sql'	=> 'storage.list',
										'where'	=> ' AND e.id = '. $this->id
									));
									$Pager	= $Pager['rows'][0];
									if ( !$Pager) {
										$Pager	= array(
											'id' => 0,
											'path' => '',
											'filename' => '',
											'fullpath' => '',
											'idStorage' => 0
										);
									}
									$Pager['cel']	= explode('/', $Pager['path']);
									$Pager['cel']	= array_reverse($Pager['cel']);
									array_shift($Pager['cel']);
									if ( count($Pager['cel']) === 0 ) {
										$Pager['cel']	= '<strong class="display-block" >---</strong><small class="padding-small" >Não armazenado</small>';
										$Pager['storaged']	= false;
									} else {
										$Pager['cel']	= '<strong class="display-block" >'. strtoupper($Pager['filename']) .'</strong><small class="padding-small" >'. join('<br /> ', $Pager['cel']).'</small>';
										$Pager['storaged']	= true;
									}
								?>
								<script type="text/javascript" src="{{AROOT}}js/Modal.js" ></script>
								<script type="text/javascript" >
									jCube(function(){
										jCube(':#eStorageDelBt').addEvent('onclick', function(E){
											E.stop();
											if ( confirm("Deseja realmente remover este produto do estoque?")) {
												var req	= new jCube.Server.HttpRequest({
													url: '{{AROOT}}storage/?action=remove-product&format=JSON&idProduct='+ ASP.id,
													noCache: true
												});
												GT8.Spinner.request(req);
												jCube(':#eStorageBt span').innerHTML	= '<strong class="display-block" >---</strong><small class="padding-small" >Removido do estoque</small>';
												this.addClass('hidden');
											}
										});
										jCube(':#eStorageBt').addEvent('onclick', function(E){
											E.stop();
											
											Modal.getFrame().scrolling	= 'no';
											Modal.show({
												url: '{{AROOT}}storage/matriz/',
												objRef: this,
												height: 550
											});
										});
									});
									Modal.onStorageChoose	= function( id, card) {
										if ( id != ASP.idStorage) {
											var req	= new jCube.Server.HttpRequest({
												url: '{{AROOT}}storage/?action=save-product&format=JSON&idProduct='+ ASP.id +'&idStorage='+ id,
												noCache: true
											});
											GT8.Spinner.request(req);
											var path	= card.query(':.path.hidden').innerHTML.split('/').reverse();
											var filename	= path[0].toUpperCase();
											path.shift();
											path.pop();
											jCube(':#eStorageBt span').innerHTML	= '<strong>'+ filename +'</strong><small class="padding-small" >'+ path.join('<br />') +'</small>';
											jCube(':#eStorageDelBt').removeClass("hidden");
										}
										Modal.hide();
									}
									ASP.idStorage	= <?php print($Pager['idStorage']);?>;
								</script>
								<strong class="col-5" >Armazenamento</strong>
								<a id="eStorageBt" href="{{AROOT}}<?php print($Pager['path'] . $Pager['filename']); ?>/" class="button col-6" >
									<span class="headed" >
										<?php print($Pager['cel']); ?>
									</span>
								</a>
								<span class="clearfix" >&nbsp;</span>
								<span class="col-5" >&nbsp;</span>
								<a id="eStorageDelBt" href="#remover-armzenamento" class="button col-6 href-button-warning <?php print($Pager['storaged']?'':'hidden'); ?>" >
									<span class="headed" >
										Remover armazenamento
									</span>
								</a>
							</label>
						</div>
						<div class="card photos line-height" >
							<div class="padding cards" >
							<?php
								/****************************************
								 *
								 * IMAGENS COMUNS AO PRODUTO
								 *
								 ***************************************/
								require_once( SROOT .'engine/functions/Pager.php');
								require_once( SROOT .'engine/functions/formatInBytes.php');
								$Pager	= Pager(array(
									sql	=> 'explorer.list',
									addSelect	=> ', UNIX_TIMESTAMP(e.creation) AS icreation',
									addWhere	=> ' AND e.dirpath REGEXP "^'. $this->data['dirpath'] .'[0-9]+/$" ',
									foundRows	=> 50,
									limit	=> 50
								));
								$Pager	= $Pager['rows'];
								$html	= '';
								$htmlN	= '';
								$aroot	= CROOT . $GT8['admin']['root'];
								$time	= time();
								for ( $i=0, $len=count($Pager); $i<$len; $i++) {
									$crr	= $Pager[$i];
									
									if ( $crr['size'] > 0 ) {
										$html	.= '
											<div class="card col-6" >
												<iframe class="col-6 height-160 marginless" frameborder="0" src="'. $aroot .'explorer/upload/?id='. $crr['id'] .'&amp;W=220&amp;H=160&amp;size=small&amp;nocache='. rand(1,1000000) .'" ></iframe>
												<div>'. number_format($crr['vtotal'], 0, '.', ',') .' / '. formatInBytes($crr['size']) .'</div>
											</div>
										';
									} else {
										$htmlN	.= '
											<div class="card col-6 hidden" >
												<iframe class="col-6 height-160 marginless" frameborder="0" src="'. $aroot .'explorer/upload/?id='. $crr['id'] .'&amp;W=220&amp;H=160&amp;size=small&amp;nocache='. rand(1,1000000) .'" ></iframe>
												<div>'. number_format($crr['vtotal'], 0, '.', ',') .' / '. formatInBytes($crr['size']) .'</div>
											</div>
										';
									}
								}
								print($html);
								print($htmlN);
							?>
								<a id="addNewPicture" class="card col-6 imgC height-160" href="#" >
									<small class="margin-small height-auto" ><img src="{{CROOT}}imgs/gt8/add-regular.png" alt="" /></small>
								</a>
								<div class="clear" ></div>
							</div>
						</div>
						<?php
							if ( isset($_GET['action']) && $_GET['action'] ==='queue-print-barcode') {
								mysql_query("
									INSERT INTO
										print_queue( id_explorer, creation)
										SELECT {$this->data['id']}, NOW()
										FROM
											print_queue
										WHERE
											id_explorer = {$this->data['id']}
										HAVING
											COUNT(*) = 0
								") or die(mysql_error());
								mysql_query("UPDATE print_queue SET printed = 0 WHERE id_explorer = {$this->data['id']}") or die(mysql_error());
								print("//#message: Produto adiconado à fila de impressão.<br />Aguarde alguns segundos e pegue a etiquena na impressora.". PHP_EOL);
								die();
							}
							/****************************************
							 *
							 * Cria código de barras, padrão code39, nas variações dos produtos
							 *
							 ***************************************/
							$codProd	= substr( '000000'. $this->data['filename'], -6);
							$codFamily	= explode('/', $this->data['path']);
							$codFamily	= $codFamily[2];
							$codFamily	= mysql_fetch_array(mysql_query("SELECT e.code FROM gt8_explorer e WHERE e.path = 'catalogo/calcados/' AND e.filename = '$codFamily' "));
							$codFamily	= substr('00'. strtoupper($codFamily[0]), -2);
							$tamanho	= Pager(array(
								'sql'	=> 'explorer.list-attributes-value',
								'where'	=> '
									AND a.id_dir IN ('. str_replace('/', ',', substr($this->Pager['rows'][0]['dirpath'], 0, -1)) .')
									AND a.attribute = "tamanho"
								',
								'replace'	=> array(
									array('vIn.id_explorer = vIn.id_explorer', 'vIn.id_explorer = '. $this->Pager['rows'][0]['id'])
								),
								'foundRows'	=> 1,
								'limit'	=> 1
							));
							$tamanho	= $tamanho['rows'][0]['value'];
							$price	= mysql_fetch_array(mysql_query("SELECT e.price_cost FROM gt8_explorer e WHERE e.id = ". $this->data['id_dir']));
							$price	= $price[0].'';
							$price	= substr('000'. substr($price, 0, strpos($price, '.')), -3);
							
							require_once( SROOT.'engine/functions/Barcode39.php');
							$this->htmlManagerModal	= '
								<hr />
								<div id="eCodSDC" class="margin-top-small" title="Código Salão do Calçado" >
									<div class="font-size-small" >
										Código Salão do Calçado
									</div>
									<div class="clearfix" ></div>
									<div class="barcode code39" >
										'. Barcode39( $codProd.$codFamily.$tamanho.$price) .'
									</div>
									<div class="clearfix" ></div>
									<div class="barcode code39 font-size-small textual-code" >
										'. $codProd.$codFamily.$tamanho.$price .'
									</div>
								</div>
								<span class="button group-button group-toggle hidden" ><a id="eTBbtPrint" title="Adicionar código de barra à fila de impressão" onclick="return Explorer.addBarcodeToQueue" href="#" class="button" ><img alt="" src="{{CROOT}}imgs/print-small.png" /></a></span>
								<section id="eBarPrint" class="barcode-printer clearfix-child hidden" >
									<div class="content" >
										<div class="img" >
											<img alt="" src="{{CROOT}}imgs/gt8/blank.gif" />
										</div>
										<div class="size" >
											
										</div>
										<div class="info" >
											<strong class="title" >Título</strong>
											<span class="family" >Família</span>
											<span class="brand" >Marca</span>
											<span class="id" >REF: {{id}}</span>
										</div>
										<div class="clearfix" ></div>
										<div class="middle-info" >
											
										</div>
										<div class="barcode code39C" >
											
										</div>
										<div class="barcode code39 font-size-small textual-code" >
											*'. $codProd.$codFamily.$tamanho.$price .'*
										</div>
										<img class="img-qr" src="{{CROOT}}imgs/gt8/blank.gif" alt="" />
										<div class="clearfix" ></div>
										<div class="barfoot" >
											<small>www.salaodocalcado.com.br</small>
										</div>
									</div>
								</section>
								<script type="text/javascript" >
									jCube.Include("{{AROOT}}explorer/levels/catalogo/levelin/levelin/levelin/levelin/barcode.css");
									jCube(function(){
										(function(){
											jCube(":#eBarPrint").addEvent("onclick", function(E){
												E.stop();
												this.addClass("hidden");
												jCube(":#eMain").removeClass("hidden");
											});
											jCube(":#eCodSDC .barcode.code39").addEvent("onclick", function(E){
												E.stop();
												jCube(":#eBarPrint").removeClass("hidden");
												jCube(":#eMain").addClass("hidden");
											});
											jCube(":#eBarPrint .img img").src	= jCube(":#ePublishC .img-preview img").src.replace(jCube(":input[name=filename]").value, "");
											jCube(":#eBarPrint .title").innerHTML	= jCube("::nav.directory a")[7].innerHTML;
											jCube(":#eBarPrint .family").innerHTML	= jCube("::nav.directory a")[5].innerHTML;
											jCube(":#eBarPrint .brand").innerHTML	= jCube("::nav.directory a")[6].innerHTML;
											jCube(":#eBarPrint .size").innerHTML	= jCube(":input[name=tamanho]").value;
											jCube(":#eBarPrint .code39C").appendChild(jCube(":#eCodSDC .barcode.code39").cloneNode(true));
											jCube(":#eBarPrint .img-qr").appendTo(jCube(":#eBarPrint .barcode.code39C"));
											jCube(":#eCodSDC .barcode.code39").addClass("cursor-pointer");
											jCube(":#eBarPrint .barcode.code39.textual-code").appendTo(jCube(":#eBarPrint .code39C"));
											jCube(":#eBarPrint").appendTo(document.body);
											jCube(":#eBarPrint img.img-qr").src	= "?action=print-qr-bar&url="+ jCube(":#ePublishC .img-preview a").href;
											
											//toolbar
											jCube(":#eTBbtPrint").addEvent("onclick", function(E){
												GT8.Spinner.request(new jCube.Server.HttpRequest({
													url: "?action=queue-print-barcode",
													noCache: true
												}));
											}).getParent().appendTo(jCube(":header.admin nav.toolbar")).removeClass("hidden");
										}).delay(1000);//use o delay para dar tempo a outro DOMReady executar algumas funções que precisaremos aqui
									});
								</script>
							';
						?>
						<?php
							/************************************************************************************************************************
							 *
							 * IMPRIME ATALHOS PARA O MESMO PRODUTO COM NUMERAÇÃO DIFERENTE
							 *
							************************************************************************************************************************/
						?>
						<div id="eLinkedSizeShs" class="card resumo line-height over clearfix-child hidden" >
							<div class="line bg-head margin-bottom-medium clearfix-child" >
								<div class="col-13" ><strong>Outros números</strong></div>
							</div>
							<div class="clear" ></div>
							<div class="col-13 clearfix-child" >
							<?php
								$Pager	= Pager(array(
									'sql'	=> 'explorer.list',
									'addWhere'	=> "AND e.path = '{$this->data['path']}' AND e.id != ". $this->data['id'],
									'addSelect'	=> ',
										(
											SELECT
												v.value
											FROM
												gt8_explorer_attributes_value v
												JOIN gt8_explorer_attributes a ON a.id = v.id_attributes
											WHERE
												a.attribute = "tamanho" AND v.id_explorer = e.id AND v.id_attributes = a.id 
										) AS tamanho
									'
								));
								$Pager	= $Pager['rows'];
								$html	= '';
								for ( $i=0; $i<count($Pager); $i++) {
									$crr	= $Pager[$i];
									$html	.= "
										<a class='col-3 href-button href-button-cancel' href='{$crr['filename']}?edit' ><span>{$crr['tamanho']}</span></a>
									";
								}
								print($html);
							?>
							</div>
							<div class="clear" ></div>
						</div>
						<script type="text/javascript" >
						//<![CDATA[
							jCube(function(){
								jCube(':#eLinkedSizeShs').appendTo(jCube(':.body .card.attributes')).removeClass('hidden');
							});
						//]]>
						</script>