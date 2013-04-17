<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Adição de endereço | GT8 Admin</title>
		<link rel="stylesheet" type="text/css" href="{{CROOT}}{{GT8:admin.root}}address/css/home.css" />
		{{VIEW:admin.head-default}}
		<script type="text/javascript" >
		//<![CDATA[
			{{JS_SERVER_VARS}}
			
			jCube.Include('Element.getElementsBySelector')
			jCube.Include('Element.getParent');
			jCube.Include('Element.setFixedMask')
			jCube.Include('Element.setFocus')
			jCube.Include('Element.setStyle');
			jCube.Include('Element.setValue');
			jCube.Include('Element.show');
			jCube.Include('Event.add');
			jCube.Include('Transition.fadeIn');
			jCube.Include('Transition.fadeOut');
			jCube(function(){
				if ( ASP.isModal) {
					
				} else {
					//jCube(':#eModalBtC').appendTo(jCube('::.Editor-new-item')[1]);
				}
				
				window.OnAfterSave	= function( req){
					
					if ( req.ret.insertId) {
						
						jCube('::input.gt8-form-post, input.gt8-form-post-send').setProperty('readonly', true);
						
						GT8.Spinner.show({
							label: 'Endereço criado com sucesso!',
							hideImage: true,
							hideAfter: 3000
						});
						
						var loc	= window.location +'';
						
						if ( loc.contains('modal=1', loc.indexOf('?'))) {
							//Editor.changes	= [
							//	['id', id],
							//	['type', jCube(':select[name=type]').getOptionValue()],
							//	['zip', jCube(':input[name=zip]').value],
							//	['stt', jCube(':select[name=stt]').getOptionValue()],
							//	['city', jCube(':input[name=city]').value],
							//	['district', jCube(':input[name=district]').value],
							//	['street', jCube(':input[name=street]').value],
							//	['number', jCube(':input[name=number]').value],
							//	['complement', jCube(':input[name=complement]').value],
							//	['reference', jCube(':textarea[name=reference]').value]
							//];
							//Modal.hide();
						} else {
							window.location	= '../'+ req.ret.insertId +'/';
						}
					} else {
						
					}
				}
				
				function CheckZip( zip) {
					var req	= CheckZip.req || new jCube.Server.HttpRequest({});
					req.abort();
					
					req.url	= '../?';
					req.addGet('zip', zip);
					req.addGet('opt', 'get');
					req.addGet('srv', 'zip');
					req.addGet('sql', 'zip');
					req.addGet('format', 'JSON');
					req.onLoad	 = function() {
						GT8.Spinner.hide();
						
						var results	= null;
						try {
							eval('results='+ this.responseText);
						} catch(e) {
							
						}
						
						if ( results) {
							jCube('::.Editor-new-item')[0].fadeOut({
								duration: 250,
								onComplete: function() {
									this.style.display	= 'none';
									jCube('::.Editor-new-item')[1].removeClass('hidden').setStyle({
										opacity: 0,
										display: 'block'
									}).fadeIn({
										duration: 800,
										onComplete: function() {
											
										}
									});
									jCube(':label input[name=zip]').setValue( zip).trigger('onkeyup');
									jCube(':label select[name=stt]').setValue( results.estado).trigger('updateValue');
									jCube(':label input[name=city]').setValue(results.cidade).trigger('onkeyup');
									jCube(':label input[name=district]').setValue(results.bairro).trigger('onkeyup');
									jCube(':label input[name=street]').setValue(results.logradouro).trigger('onkeyup');
									jCube(':label input[name=number]').setFocus();
								}
							});
							jCube('::#eModalBtC a.href-button').removeClass('href-button-disabled');
						}
					}
					req.onError	= function() {
						GT8.Spinner.hide();
					}
					req.start();
					GT8.Spinner.show({
						label: 'Buscando CEP'
					});
				}
				jCube(':label input[name=zip0]').setFixedMask('#####-###').addEvent('keyup', function(e){
					if ( e.key == 13 ) {
						CheckZip(this.value);
					}
				});
				jCube(':label input[name=zip]').setFixedMask('#####-###');
				window.setTimeout(function(){ jCube(':label input[name=zip0]').focus()}, 250);
				
				jCube(':label select[name=type]').selectedIndex	= 0;
			});
		//]]>
		</script>
		<style type="text/css" >
			.fluid-12 {
				max-width: 960px;
			}
			.overlay {
				position: fixed;
				left: 0px;
				top: 0px;
				width: 100%;
				height: 100%;
				background: rgba(150,150,150, 0.6);
				z-index: 1000;
				display: none;
			}
			header.admin {
				z-index: 1001;
			}
			footer.main {
				z-index: 1001;
			}
			#eLabelFST {
				line-height: 100px;
				font-size: 84px;
				font-weight: bolder;
			}
				.label-fst {
					font-size: 22px;
					margin-bottom: 20px;
					font-weight: normal;
					text-align: center;
				}
					#eLabelFST span input {
						line-height: 100px;
						font-size: 84px;
						height: 100px;
						font-weight: bolder;
						text-align: center;
					}
			body .hidden {
				display: none;
			}
			body a.href-button {
				line-height: 48px;
			}
			.modal-window #eLabelFST input {
				font-size: 50px;
				line-height: 50px;
			}
		</style>
	</head>
	<body class="{{modal-class}} overflow-auto" >
		<div id="eMain" >
			{{VIEW:admin.header-new}}
			<div class="row-12 fluid-12 clearfix-child" >
				<div class="Editor-new-item grid-12 border-radius input-validation" >
					<div class="padding-large clearfix-child" >
						<div class="label-fst" >Digite o cep</div>
						<label id="eLabelFST" class="clearfix-child" >
							<span class="grid-8 prefix-2" >
								<input type="text" value="" name="zip0" class="grid-12 gt8-update input-rounded-shadowed" />
							</span>
						</label>
						<input type="text" value="{{GET:idRef}}" name="idRef" class="gt8-update hidden" />
						<input type="text" value="<?php print(RegExp($_GET['tableRef'], '[a-zA-Z_\-\.]+')); ?>" name="tableRef" class="gt8-update hidden" />
					</div>
				</div>
				<div class="Editor-new-item margin-bottom-large grid-12 border-radius input-validation hidden" >
					<div class="padding-large clearfix-child" >
						<p class="grid-12">
							<label class="input-select prefix-4 grid-8 omega" >
								<strong class="grid-4 text-align-right">Tipo de endereço</strong>
								<span class="e-select grid-8 omega">
									<select class="gt8-form-post" name="type" >
										{{METHOD:getAddressTypes}}
										<option value="{{id}}" >##type##</option>
										{{/METHOD}}
									</select>
									<span class="button group-button" >
										<strong>Escolha uma opção</strong>
										<img alt="" src="{{CROOT}}imgs/gt8/arrow-down-mini.png" />
									</span>
								</span>
							</label>
						</p>
						<p class="grid-12">
							<label class="input-text prefix-4 grid-8 omega required" >
								<strong class="grid-4 text-align-right" >Código postal (CEP)</strong>
								<input type="text" value="" class="grid-8 omega gt8-form-post" name="zip" />
								<small>&nbsp;</small>
								<sub class="grid-4" >&nbsp;</sub>
							</label>
						</p>
						<p class="grid-12">
							<label class="input-select prefix-4 grid-8 omega required" >
								<strong class="grid-4 text-align-right">Estado</strong>
								<span class="e-select grid-8 omega">
									<select class="gt8-form-post" name="stt" >
										{{$this->setUfs()}}
										{{FOREACH:ufs}}
										<option value="{{uf}}" >##name##</option>
										{{/FOREACH}}
									</select>
									<span class="button group-button" >
										<strong>Escolha uma opção</strong>
										<img alt="" src="{{CROOT}}imgs/gt8/arrow-down-mini.png" />
									</span>
								</span>
								<sub class="grid-4" >&nbsp;</sub>
							</label>
						</p>
						<p class="grid-12">
							<label class="input-text prefix-4 grid-8 omega required" >
								<strong class="grid-4 text-align-right" >Cidade</strong>
								<input type="text" value="" class="grid-8 omega gt8-form-post" name="city" />
								<small>&nbsp;</small>
								<sub class="grid-4" >&nbsp;</sub>
							</label>
						</p>
						<p class="grid-12">
							<label class="input-text prefix-4 grid-8 omega required" >
								<strong class="grid-4 text-align-right" >Bairro</strong>
								<input type="text" value="" class="grid-8 omega gt8-form-post" name="district" />
								<small>&nbsp;</small>
								<sub class="grid-4" >&nbsp;</sub>
							</label>
						</p>
						<p class="grid-12">
							<label class="input-text prefix-4 grid-8 omega required" >
								<strong class="grid-4 text-align-right" >Logradouro</strong>
								<input type="text" value="" class="grid-8 omega gt8-form-post" name="street" />
								<small>&nbsp;</small>
								<sub class="grid-4" >&nbsp;</sub>
							</label>
						</p>
						<p class="grid-12">
							<label class="input-text prefix-4 grid-8 omega required" >
								<strong class="grid-4 text-align-right" >Número</strong>
								<input type="text" value="" class="grid-8 omega gt8-form-post" name="number" />
								<small>&nbsp;</small>
								<sub class="grid-4" >&nbsp;</sub>
							</label>
						</p>
						<p class="grid-12">
							<label class="input-text prefix-4 grid-8 omega" >
								<strong class="grid-4 text-align-right" >Complemento</strong>
								<input type="text" value="" class="grid-8 omega gt8-form-post" name="complement" />
								<small>&nbsp;</small>
								<sub class="grid-4" >&nbsp;</sub>
							</label>
						</p>
						<p class="grid-12">
							<label class="input-text prefix-4 grid-8 omega" >
								<strong class="grid-4 text-align-right" >Referência</strong>
								<input type="text" value="" class="grid-8 omega gt8-form-post" name="reference" />
								<small>&nbsp;</small>
								<sub class="grid-4" >&nbsp;</sub>
							</label>
						</p>
						<div class="grid-12 height-large" ></div>
						<div class="grid-12 marginless" >
							<label id="eModalBtC" class="grid-12" >
								<a class="grid-4 margin-left-m href-button href-button-cancel" ><span>Cancelar</span></a>
								<a class="grid-5m href-button gt8-form-post-save gt8formname-add-new-address" ><span>Concluir</span></a>
							</label>
						</div>
					</div>
				</div>
				<div class="overlay" >&nbsp;</div>
			</div>
			{{$this->getFooter()}}
		</div>
	</body>
</html>