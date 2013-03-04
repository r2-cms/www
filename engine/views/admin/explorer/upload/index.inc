<?php
	require_once( SROOT .'engine/functions/convertFromBytes.php');
	$maxUploadSize	= convertFromBytes(@ini_get('upload_max_filesize'));
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>GT8 File Upload Manager</title>
		<link rel="stylesheet" type="text/css" href="{{AROOT}}css/main.css" />
		<link rel="stylesheet" type="text/css" href="{{CROOT}}css/gt8/uploader.css" />
		<script type="text/javascript" src="{{CROOT}}jCube/jCube.js" ></script>
		<script type="text/javascript" >
		//<![CDATA[
			
			{{$this->getServerJSVars()}}
			
			jCube.Import("{{CROOT}}js/gt8/uploader.js");
			jCube.Include("Element.addClass");
			jCube.Include("Element.removeClass");
			jCube.Include("Server.HttpRequest");
			jCube.Include("Window");
			jCube.Include("Window.DOMReady");
			function DeleteImage( n) {
				if ( n == 1) {
					jCube("eOverlayConfirm").removeClass("open");
					var req	= new jCube.Server.HttpRequest("?opt=delete");
					req.noCache	= true;
					req.addGet("id", {{$this->id}});
					req.start();
					
					jCube("img-preview").src	= "{{CROOT}}imgs/gt8/newfile-regular.png";
					ASP.src	= "imgs/gt8/newfile-regular.png";
				} else if ( n == 0) {
					jCube("eOverlayConfirm").removeClass("open");
				} else if ( n == 2) {
					jCube("eOverlayConfirm").addClass("open");
				}
			}
			
			jCube(function() {
				document.getElementById("img-delete-bt").style.visibility	= ASP.locked? "hidden": "";
				if ( parent && parent.Explorer && parent.Explorer.onModalUpload) {
					try {
						ASP.isUpload	= (window.location+"").indexOf("opt=upload") > -1;
						parent.Explorer.onModalUpload(ASP);
					} catch (e) {
						//alert("Error")
					}
				}
			});
		//]]>
		</script>
		<style type="text/css" >
			body div, body form, body input {
				width: {{$this->windowWidth}}px;
				height: {{$this->windowHeight}}px;
				font-size: <?php print(($Index->windowHeight*2)); ?>px;
				overflow: hidden;
			}
			body #label {
				width: {{$this->windowWidth}}px;
			}
			body form {
				font-size: {{$this->windowWidth}}px;
			}
			body img#img-preview {
				width: {{$this->windowWidth}}px;
				height: {{$this->windowHeight}}px;
				max-width: {{$this->windowWidth}}px;
				height: auto;
				max-height: {{$this->windowHeight}}px; 
			}
			body img#img-delete-bt {
				position: absolute;
				left: auto;
				top: auto;
				right: 0px;
				bottom: 0px;
				z-index: 10;
				opacity: 0;
				transition: all 0.8s;
				-moz-transition: all 0.8s;
				-webkit-transition: all 0.8s;
			}
				body:hover img#img-delete-bt {
					opacity: 1;
				}
			#eOverlayConfirm {
				position: absolute;
				top: 100%;
				width: 100%;
				height: 100%;
				margin: 0px;
				padding: 0px;
				z-index: 1000;
				background: #FCC;
				background: rgba( 255, 220, 220, 0.8);
				transition: all 0.8s;
				-moz-transition: all 0.8s;
				-webkit-transition: all 0.8s;
			}
			#eOverlayConfirm.open {
				top: 0;
			}
			#eOverlayConfirm h4, #eOverlayConfirm p, #eOverlayConfirm a, #eOverlayConfirm span {
				display: block;
				position: relative;
				width: 100%;
				height: auto;
				margin: 0;
				padding: 0;
				line-height: 20px;
				font-size: 16px;
				text-align: center;
			}
			#eOverlayConfirm h4 {
				padding: 10px 0;
				line-height: 30px;
			}
			#eOverlayConfirm p {
				padding: 10px 0;
				font-size: 16px;
				line-height: 25px;
			}
			#eOverlayConfirm .href-buttonC {
				position: absolute;
				bottom: 10px;
				top: auto;
				width: 100%;
				height: 80px;
				padding: 0;
			}
			#eOverlayConfirm .href-button-cancel {
				height: auto;
				line-height: 30px;
				margin: 0 10px 10px;
				width: auto;
			}
				#eOverlayConfirm .href-button-cancel span {
					height: auto;
					line-height: 30px;
				}
			#eOverlayConfirm .href-button-warning {
				width: auto;
				margin: 0 10px;
			}
				#eOverlayConfirm .href-button-warning span {
					height: auto;
					line-height: 30px;
				}
			/*********** SMALL *****************/
			body.small #eOverlayConfirm p {
				display: none;
			}
			body.small #eOverlayConfirm h4 {
				font-size: 14px;
				line-height: 20px;
			}
			body.small #eOverlayConfirm .href-buttonC {
				font-size: 14px;
			}
		</style>
	</head>
	<body class="<?php if ( isset($_GET['size'])){ print(RegExp($_GET['size'], '[a-zA-Z0-9\-_]+')); } ?>" >
		<div id="eFormC" >
			<form action="?teste&amp;opt=upload&amp;W={{$this->windowWidth}}&amp;H={{$this->windowHeight}}&amp;id={{$this->id}}&amp;noCache={{rand(1, 1000000)}}" method="post" enctype="multipart/form-data" >
				<input type="hidden" name="MAX_FILE_SIZE" value="<?php print($maxUploadSize); ?>" />
				<input name="img" type="file" onchange='jCube("img-preview").style.display="none"; jCube("spinner").style.display="block"; this.parentNode.submit();' />
			</form>
		</div>
		<img id="img-preview" src="{{$this->imgPath}}" alt="" />
		<img id="img-delete-bt" src="{{CROOT}}imgs/gt8/cancel-small.png" alt="" onclick="DeleteImage(2)" />
		<div id="label" >Adicionar</div>
		<div id="spinner" ></div>
		<div id="eOverlayConfirm" >
			<h4>Tem certeza que desejar excluir esta imagem?</h4>
			<p>Esta ação é irreversível.</p>
			<div class="href-buttonC" >
				<a onclick="DeleteImage(0); return false" href="#cancel" class="block href-button href-button-cancel" ><span>Cancelar</span></a>
				<a onclick="DeleteImage(1)" href="#delete" class="block href-button href-button-warning" ><span>Excluir</span></a>
			</div>
		</div>
	</body>
</html>