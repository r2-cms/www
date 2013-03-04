<?php $ForgotPassword	= new ForgotPassword(); ?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Redefinição de senha | GT8</title>
		<link rel="stylesheet" type="text/css" href="<?php print(CROOT); ?>css/main.css" />
		<link rel="stylesheet" type="text/css" href="<?php print(CROOT); ?>css/header.css" />
		<script type="text/javascript" src="<?php print(CROOT); ?>jCube/jCube.js" ></script>
		<script type="text/javascript" src="<?php print(CROOT); ?>js/main.js" ></script>
		<script type="text/javascript" src="<?php print(CROOT); ?>js/header.js" ></script>
		<script type="text/javascript" >
		//<![CDATA[
			
			jCube.Include('Element.getData');
			jCube.Include('Element.setData');
			//#affected rows: 1
			jCube(function(){
				jCube(':#eBtReset').addEvent('onclick', function(e) {
					e.stop();
					
					if ( !jCube(':#eLogin').value) {
						
					}
					
					if ( this.getData('last-send')==0) {
						
					} else {
						var req	= new jCube.Server.HttpRequest({
							url:	'?format=JSON&action=reset-request&login='+ jCube(':#eLogin').value,
							noCache: true,
							onLoad: function() {
								var ret	= GT8.onGeneralRequestLoad.call( this);
								jCube(':#eStep-1').setStyle('display', 'none');
								jCube(':#eStep-2').setStyle({
									display: 'block',
									opacity: 0
								}).fadeIn();
								jCube(':h1 img.spinner').setStyle('display', 'none');
							},
							onError: function() {
								jCube(':h1 img.spinner').setStyle('display', 'none');
							}
						});
						req.start();
						this.setData('last-send', new Date().getTime());
						
						jCube(':h1 img.spinner').setStyle('display', 'block');
					}
					return false;
				});
				window.setTimeout( function(){jCube(':#eLogin').focus()}, 250);
				window.setTimeout( function(){jCube(':#eLogin').focus()}, 550);
			});
		//]]>
		</script>

		<style type="text/css" >
			#eLeftPanel {
				height: 320px;
			}
		</style>
	</head>
	<body>
		<div id="eMain" >
			<?php $ForgotPassword->printHeader(); ?>
			<div class="wrapper" >
				<div class="row" >
					<div class="cardbox-white col-24" >
						<h1 class="title-bgbox" >
							<a href="<?php print(CROOT); ?>" class="logo" ><img alt="[logotipo]" src="<?php print(CROOT); ?>imgs/logo-regular.png" /></a>
							<small>Redefinição de senha</small>
							<img class="spinner" src="<?php print(CROOT); ?>imgs/spinner-large.gif" alt="" />
						</h1>
						<div class="row" >
							<div id="eLeftPanel" class="col-10 box-bglight" >
								<div class="col-padding" >
									<h2>Redefina sua senha</h2>
									<p>Se você esqueceu sua senha, informe sua identificação (login) e clique em prosseguir.</p>
								</div>
							</div>
							<div id="eStep-1" class="col-14" >
								<div class="col-padding input-validation" >
									<h3>Informe sua identificação</h3>
									<label class="line required col-9 col-center align-center margin-top" >
										<span>
											<input id="eLogin" type="text" value="<?php $ForgotPassword->printLogin(); ?>" class="input-rounded-shadowed" />
											<small>Identificação <?php print($GT8['title']); ?></small>
										</span>
									</label>
									<div class="height-30" ></div>
									<div class="height-30" ></div>
									<label class="line col-9 col-center" >
										<span class="row" >
											<a class="col-3 href-button href-button-cancel" href="<?php print(CROOT.$GT8['account']['root']); ?>" ><span>Cancelar</span></a>
											<a id="eBtReset" class="col-6 href-button href-button-ok" href="?reset" ><span>Prosseguir</span></a>
											<div class="clear" ></div>
										</span>
									</label>
									<div class="clear" ></div>
								</div>
							</div>
							<div id="eStep-2" class="col-14 hidden" >
								<div class="col-padding input-validation" >
									<h3>Um e-mail foi enviado para sua caixa de e-mails</h3>
									<p>
										<img src="<?php print(CROOT); ?>imgs/account/mail.png" alt="" class="float-left" />Ao receber o e-mail com as informações de login, siga as instruções para redefinir sua senha.
									</p>
									<div class="clear" ></div>
								</div>
							</div>
							<div class="clear" ></div>
						</div>
					</div>
					<div class="clear" ></div>
				</div>
			</div>
		</div>
	</body>
</html>