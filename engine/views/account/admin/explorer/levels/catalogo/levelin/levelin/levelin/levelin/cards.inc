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