<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Alterar endereço | Cadastro</title>
		{{VIEW:head}}
		<link rel="stylesheet" type="text/css" href="{{CROOT}}{{GT8:account.root}}css/account.css" />
		<script type="text/javascript" src="{{CROOT}}{{GT8:account.root}}js/account.js" ></script>
		<script type="text/javascript" src="{{CROOT}}js/Modal.js" ></script>
		<script type="text/javascript" >
		//<![CDATA[
			jCube(function(){
				jCube(':.links-menu a.enderecos').addClass('selected');
				
				var eModal	= jCube(':#eModalPassEdit'), initialized	= false;
				jCube(':#eBtDelAddress').addEvent('onclick', function(E){
					GT8.ShowModal({
						eModal: eModal,
						eRef: this,
						onShowOverlayComplete: function() {
							if ( !initialized) {
								jCube(':#eBtDelAddressConfirmed').addEvent('onclick', function(E){
									var req	= new jCube.Server.HttpRequest({
										url: '?action=delete-account-address',
										noCache: true,
										onComplete: function(){
											if ( this.ret.affected) {
												window.location	= '../';
											} else {
												
											}
										}
									});
									req.addGet('id', jCube(':input[name=id]').value);
									GT8.Spinner.request(req);
								});
								jCube(':#eBtDelAddressCancel').addEvent('onclick', function(E){
									GT8.ShowModal();
								});
								initialized	= true;
							}
							
						}
					});
				});
			});
			var OnAfterSave	= function(){
				window.location	= '../';
			}
		//]]>
		</script>
		<style type="text/css" >
			.modal-window {
				max-width: 440px !important;
			}
		</style>
	</head>
	<body class="account address-edit" >
		<div id="eMain" >
			{{VIEW:header}}
			{{VIEW:location}}
			<div class="account row-12 resume fluid-12" >
				<h1 class="margin-bottom grid-12" >Endereços</h1>
				<div class="grid-12 card bg-graylight shadow-small clearfix-child" >
					{{VIEW:account.menu}}
					<div class="grid-8 marginless bg-white sborder-left unfloat-phone" >
						<div class="padding-medium clearfix-child panel-right" >
							<h2 class="grid-12" >Endereço ##type##</h2>
							<input type="hidden" class="gt8-form-post" name="id" value="{{id}}" />
							<p class="grid-12" >
								<label class="input-select prefix-4 grid-8 omega" >
									<strong class="grid-4 text-align-right" >Tipo</strong>
									<span class="e-select grid-12 omega">
										<select name="type" class="gt8-form-post" >
											{{COMBO-OPTIONS:types|id|type|selected|utf8}}
										</select>
										<span class="button group-button"><strong>{{type}}</strong><img src="{{CROOT}}imgs/gt8/arrow-down-mini.png" alt="" /></span>
									</span>
								</label>
							</p>
							<p class="grid-12" >
								<label class="input-text prefix-4 grid-8 omega" >
									<strong class="grid-4 text-align-right" >CEP</strong>
									<input type="text" name="zip" class="grid-12 omega mask-zip gt8-form-post" value="{{zip}}" />
									<small>&nbsp;</small>
								</label>
							</p>
							<p class="grid-12" >
								<label class="input-text prefix-4 grid-8 omega" >
									<strong class="grid-4 text-align-right" >Rua</strong>
									<input type="text" name="street" class="grid-12 omega gt8-form-post" value="##street##" />
									<small>&nbsp;</small>
								</label>
							</p>
							<p class="grid-12" >
								<label class="input-text prefix-4 grid-8 omega" >
									<strong class="grid-4 text-align-right" >Número</strong>
									<input type="text" name="number" class="grid-12 omega gt8-form-post" value="{{number}}" />
									<small>&nbsp;</small>
								</label>
							</p>
							<p class="grid-12" >
								<label class="input-text prefix-4 grid-8 omega" >
									<strong class="grid-4 text-align-right" >Complemento</strong>
									<input type="text" name="complement" class="grid-12 omega gt8-form-post" value="##complement##" />
									<small>&nbsp;</small>
								</label>
							</p>
							<p class="grid-12" >
								<label class="input-text prefix-4 grid-8 omega" >
									<strong class="grid-4 text-align-right" >Referência</strong>
									<input type="text" name="reference" class="grid-12 omega gt8-form-post" value="##reference##" />
									<small>&nbsp;</small>
								</label>
							</p>
							<p class="grid-12" >
								<label class="input-text prefix-4 grid-8 omega" >
									<strong class="grid-4 text-align-right" >Bairro</strong>
									<input type="text" name="district" class="grid-12 omega gt8-form-post" value="##district##" />
									<small>&nbsp;</small>
								</label>
							</p>
							<p class="grid-12" >
								<label class="input-text prefix-4 grid-8 omega" >
									<strong class="grid-4 text-align-right" >Cidade</strong>
									<input type="text" name="city" class="grid-12 omega gt8-form-post" value="##city##" />
									<small>&nbsp;</small>
								</label>
							</p>
							<p class="grid-12" >
								<label class="input-select prefix-4 grid-8 omega" >
									<strong class="grid-4 text-align-right" >Estado</strong>
									<span class="e-select grid-12 omega">
										<select name="stt" class="gt8-form-post" >
											{{COMBO-OPTIONS:estados|1|0|3}}
										</select>
										<span class="button group-button"><strong>{{natureza}}</strong><img src="{{CROOT}}imgs/gt8/arrow-down-mini.png" alt="" /></span>
									</span>
								</label>
							</p>
							<div class="height-medium grid" ></div>
							<hr class="grid-12" />
							<p class="grid-12 clearfix-child margin-bottom-none omega" >
								<a href="#excluir" class="href-button href-button-red grid-2 alpha line-height-large" id="eBtDelAddress" >
									<span>Excluir</span>
								</a>
								<a href="../" class="href-button grid-2hm line-height-large margin-left-2m" >
									<span>Cancelar</span>
								</a>
								<a href="#salvar" class="href-button grid-5 omega line-height-large gt8-form-post-save gt8formname-save-account-address" >
									<span>Salvar</span>
								</a>
							</p>
						</div>
					</div>
				</div>
				<div class="clearfix" ></div>
			</div>
			{{VIEW:footer}}
		</div>
		<!-- CUSTOMS  -->
		<div id="eModalPassEdit" class="modal-window row-12 fluid-12" >
			<div class="padding clearfix-child" >
				<h3 class="grid-12" >
					Tem certeza que deseja excluir este endereço? <br /><small>Esta ação não poderá ser desfeita.</small>
				</h3>
				<p class="grid-12" >
					<a href="#excluir" class="href-button href-button-red grid-12 marginless line-height-large" id="eBtDelAddressConfirmed" >
						<span>Excluir</span>
					</a>
				</p>
				<p class="grid-12 margin-bottom-mini" >
					<a href="#cancelar" class="href-button grid-12 marginless line-height-large" id="eBtDelAddressCancel" >
						<span>Cancelar</span>
					</a>
				</p>
			</div>
		</div>
	</body>
</html>