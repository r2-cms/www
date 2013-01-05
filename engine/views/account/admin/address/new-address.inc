<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Adição de endereço | GT8 Admin</title>
		<link rel="stylesheet" type="text/css" href="{{CROOT}}css/main.css" />
		<link rel="stylesheet" type="text/css" href="{{CROOT}}{{GT8:admin.root}}css/main.css" />
		<link rel="stylesheet" type="text/css" href="{{CROOT}}{{GT8:admin.root}}{{GT8:admin.address.root}}css/home.css" />
		<script type="text/javascript" src="{{CROOT}}jCube/jCube.js" ></script>
		<script type="text/javascript" src="{{CROOT}}js/gt8/Pager.js" ></script>
		<script type="text/javascript" src="{{CROOT}}js/gt8/Editor.js" ></script>
		<script type="text/javascript" src="{{CROOT}}{{GT8:admin.root}}{{GT8:admin.address.root}}js/Editor.js" ></script>
		<script type="text/javascript" >
		//<![CDATA[
			{{JS_SERVER_VARS}}
			
			jCube.Import( ASP.padmin +"js/main.js");
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
					jCube(':#eModalBtC').appendTo(jCube('::.Editor-new-item')[1]);
				}
				
				Editor.enabled	= true;
				Editor.createNew.onLoad	= function( response) {
					var id	= 0;
					if ( response.contains('//#id inserted successfully')) {
						id	= response.match(/\#id inserted successfully \(([0-9]+)\)/);
						id	= id && id[1]? id[1]: 0;
					}
					if ( id) {
						ASP.id	= id;
						Editor.setAllReadOnly( true);
						
						GT8.Spinner.show({
							label: 'Endereço criado com sucesso!',
							hideImage: true,
							hideAfter: 3000
						});
						
						var loc	= window.location +'';
						
						if ( loc.contains('modal=1', loc.indexOf('?'))) {
							Editor.changes	= [
								['id', id],
								['type', jCube(':select[name=type]').getOptionValue()],
								['zip', jCube(':input[name=zip]').value],
								['stt', jCube(':select[name=stt]').getOptionValue()],
								['city', jCube(':input[name=city]').value],
								['district', jCube(':input[name=district]').value],
								['street', jCube(':input[name=street]').value],
								['number', jCube(':input[name=number]').value],
								['complement', jCube(':input[name=complement]').value],
								['reference', jCube(':textarea[name=reference]').value]
							];
							Modal.hide();
						} else {
							window.location	= '../'+ id +'/';
						}
					} else {
						GT8.Spinner.show({
							label: 'Não foi possível adicionar o endereço agora<br />Por favor, tente mais tarde.',
							hideImage: true,
							hideAfter: 10000
						});
					}
				}
				
				function CheckZip( zip) {
					var req	= CheckZip.req || new jCube.Server.HttpRequest({});
					req.abort();
					
					req.url	= '?';
					req.addGet('zip', zip);
					req.addGet('opt', 'get');
					req.addGet('srv', 'zip');
					req.addGet('sql', 'zip');
					req.addGet('format', 'JSON');
					req.onLoad	 = function() {
						GT8.Spinner.hide();
						
						var results	= null;
						try {
							eval(this.responseText);
						} catch(e) {
							
						}
						
						if ( results) {
							jCube('::.Editor-new-item')[0].fadeOut({
								duration: 250,
								onComplete: function() {
									this.style.display	= 'none';
									jCube('::.Editor-new-item')[1].setStyle({
										opacity: 0,
										display: 'block'
									}).fadeIn({
										duration: 800,
										onComplete: function() {
											
										}
									});
									
									jCube(':label input[name=zip]').setValue( zip).trigger('onkeyup');
									jCube(':label select[name=stt]').setValue( results.estado).trigger('updateValue').trigger('onkeyup');
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
					})
				}
				jCube(':label input[name=zip0]').setFixedMask('#####-###').addEvent('keyup', function(e){
					if ( e.key == 13 ) {
						CheckZip(this.value);
					}
				});
				jCube(':label input[name=zip]').setFixedMask('#####-###');
				window.setTimeout(function(){ jCube(':label input[name=zip0]').focus()}, 250);
				
				jCube(':label select[name=type]').selectedIndex	= 0;
				jCube(':label input[name=number]').getParent('label').query(':em').setStyle({right: (ASP.isModal?-240: -600)});
			});
		//]]>
		</script>
		<style type="text/css" >
			.Editor-new-item {
				padding: 30px;
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
				#eLabelFST strong {
					line-height: 50px;
					font-size: 22px;
					height: 50px;
					font-weight: normal;
					text-align: center;
				}
				#eLabelFST span  {
					float: none;
					margin: 10px auto;
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
				line-height: 64px;
			}
			.modal-window #eLabelFST input {
				font-size: 50px;
				line-height: 50px;
			}
		</style>
	</head>
	<body class="{{modal-class}}" >
		<div id="eMain" >
			<?php $this->printHeader(array(
				"h1" 		=> 'Novo endereço',
				'directory'	=> $this->getDirLocation(),
				'toolbar'	=> $this->toolbarItems
			)); ?>
			<div class="Editor-new-item padding-top col-22 border-radius input-validation" >
				<label id="eLabelFST" >
					<strong>Digite o cep</strong>
					<span class="col-13" >
						<input type="text" value="" name="zip0" class="gt8-update input-rounded-shadowed" />
					</span>
				</label>
				<input type="text" value="<?php print((integer)$_GET['idRef']); ?>" name="idRef" class="gt8-update" style="display:none; " />
				<input type="text" value="<?php print(RegExp($_GET['tableRef'], '[a-zA-Z_\-\.]+')); ?>" name="tableRef" class="gt8-update" style="display:none; " />
			</div>
			<div class="Editor-new-item padding-top col-22 border-radius input-validation hidden" >
				<?php $this->writeSelectLabeled('type', 'Tipo', 'required', array(array('', 'Escolha uma opção'), array('Business','Comercial'),array('Delivery', 'Entrega'),array('Friend', 'Amigos'),array('Home', 'Casa'),array('Office', 'Escritório'),array('Relative', 'Parentes'),array('Vacation', 'Férias'),array('Work', 'Trabalho'),array('Other', 'Outro')), array('colWidth'=>6)); ?>
				<?php $this->writeInputLabeled('zip', 'CEP', 'required', '', '', array('colWidth'=>6)); ?>
				<?php $this->writeSelectLabeled('stt', 'Estado', 'required', $this->getStt()); ?>
				<?php $this->writeInputLabeled('city', 'Cidade', 'required', '', '', array('colWidth'=>($this->isModal?8:12))); ?>
				<?php $this->writeInputLabeled('district', 'Bairro', 'required', '', '', array('colWidth'=>($this->isModal?8:12))); ?>
				<?php $this->writeInputLabeled('street', 'Logradouro', 'required', '', '', array('colWidth'=>($this->isModal?8:12))); ?>
				<?php $this->writeInputLabeled('number', 'Número', 'required','','', array('colWidth'=>3)); ?>
				<?php $this->writeInputLabeled('complement', 'Complemento', '', '', '', array('colWidth'=>($this->isModal?5:9),'noEM'=>true, 'noSTRONG'=>true, 'styleLABEL'=>'clear:none;')); ?>
				<?php $this->writeTextareaLabeled('reference', 'Referência', '', '', 250, array('colWidth'=>12, 'styleTA'=>'height:80px;')); ?>
			</div>
			<div class="overlay" >&nbsp;</div>
			<div class="line" >
				<label id="eModalBtC" class="line" >
					<a href="javascript: Modal.hide( true); " class="col-5 href-button href-button-cancel" ><span>Cancelar</span></a>
					<a href="javascript: Editor.createNew();" class="col-<?php print($this->isModal?10:12); ?> href-button href-button-ok href-button-disabled" ><span>Concluir</span></a>
				</label>
			</div>
			<?php $this->printFooter(); ?>
		</div>
	</body>
</html>