						<div class="card logistic input-validation" >
							<label title="minlength:1" class="line" >
								<strong class="col-5" >Estoque físico</strong>
								<span class="col-6" ><input type="text" value="{{stock}}" name="stock" class="gt8-update input-rounded-shadowed" /><small class="hidden" >Estoque</small></span>
								<em class="col-6">&nbsp;</em>
							</label>
						</div>
						<div class="card photos line-height" >
							<div class="padding cards" >
							<?php
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
								<div class="margin-top-small" title="Código do fornecedor" >
									<div class="font-size-small" >
										Código do fornecedor
									</div>
									<div class="clearfix" ></div>
									<div class="barcode code39" >
										'. Barcode39($this->data['filename']) .'
									</div>
									<div class="clearfix" ></div>
									<div class="barcode code39 font-size-small" >
										'. $this->data['filename'] .'
									</div>
								</div>
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
									<div class="barcode code39 font-size-small" >
										'. $codProd.$codFamily.$tamanho.$price .'
									</div>
								</div>
							';
						?>