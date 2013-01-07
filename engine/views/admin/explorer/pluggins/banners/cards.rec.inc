						<div class="card background line-height" >
							<div class="padding cards" >
							<?php
								require_once( SROOT .'engine/functions/Pager.php');
								require_once( SROOT .'engine/functions/formatInBytes.php');
								$Pager	= Pager(array(
									'sql'	=> 'explorer.list',
									'addSelect'	=> ', UNIX_TIMESTAMP(e.creation) AS icreation',
									'addWhere'	=> ' AND e.id_dir = '. $this->data['id'],
									'foundRows'	=> 5,
									'limit'	=> 5
								));
								$Pager	= $Pager['rows'];
								$html	= '';
								$htmlN	= '';
								$aroot	= CROOT . $GT8['admin']['root'];
								$time	= time();
								for ( $i=0, $len=count($Pager); $i<$len; $i++) {
									$crr	= $Pager[$i];
									
									$html	.= '
										<div class="card col-6" >
											<iframe class="col-6 height-160 marginless" frameborder="0" src="'. $aroot .'explorer/upload/?id='. $crr['id'] .'&amp;W=220&amp;H=160&amp;size=small&amp;nocache='. rand(1,1000000) .'" ></iframe>
											<div>'. number_format($crr['vtotal'], 0, '.', ',') .' / '. formatInBytes($crr['size']) .'</div>
										</div>
									';
								}
								print($html);
								print($htmlN);
							?>
								<a id="addNewPicture" class="card col-6 imgC height-160 <?php (count($Pager)? print('hidden'): ''); ?>" href="#" >
									<small class="margin-small height-auto" ><img src="{{CROOT}}imgs/gt8/add-regular.png" alt="" /></small>
								</a>
								<div class="clear" ></div>
							</div>
						</div>