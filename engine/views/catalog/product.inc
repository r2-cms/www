<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>##title## | Salão do Calçado</title>
		{{VIEW:head}}
		<link rel="stylesheet" type="text/css" href="{{CROOT}}css/product.css" />
		<script type="text/javascript" src="{{CROOT}}js/product.js" ></script>
		<script type="text/javascript" >
		//<![CDATA[
			{{JS_SERVER_VARS}}
			jCube(function(){
			});
		//]]>
		</script>
	</head>
	<body class="catalog home" >
		<div id="eMain" >
			{{VIEW:header}}
			{{VIEW:location}}
			<div class="row-12 fluid-12 cards shopping-content clearfix-child" >
			</div>
			<div class="banner margin-bottom-large" >
				<div class="row-12 fluid-12 clearfix-child" >
					<div class="grid-7 image-preview clearfix-child" >
						<div class="img-preview-holder" >
							<div class="background-effect" >
								<div class="img images-step" >
									<img class="img" id="eBaseImage" src="?translate-img=1&preview" alt="[pré-visualização]" width="100%" />
									{{FOREACH:thumbs}}<img class="img" src="{{varname}}{{imgname}}/?preview" alt="[imagem ilustrativa]" width="100%" />{{/FOREACH}}
								</div>
								<div class="hidden lazy-load" ></div>
								<div class="enunciation" >
									<div class="title titles-step" >
										<h5 class="margin-bottom-small active" >Dakota</h5>
										{{FOREACH:thumbs}}<h5 class="margin-bottom-small" >##title##</h5>{{/FOREACH}}
									</div>
									<div class="text texts-step" >
										<p class=" active" >##title##</p>
										{{FOREACH:thumbs}}<p>##title##</p>{{/FOREACH}}
									</div>
								</div>
								<div class="slider-steps" >
									<a href='link-1/' class="active" ><img src="{{CROOT}}imgs/gt8/blank.gif" alt="" width="16" height="16" /></a>
									{{FOREACH:thumbs}}<a href='{{varname}}{{imgname}}/?preview' class="active" ><img src="{{CROOT}}imgs/gt8/blank.gif" alt="" width="16" height="16" /></a>{{/FOREACH}}
								</div>
							</div>
						</div>
					</div>
					<div class="info grid-5 clearfix-child" >
						<h1 class="margin-top-small" >##title##{{bt-admin}}</h1>
						<p class="price margin-bottom-large hidden-{{no-stock}}" >
							<strong><small>R$</small> $$price_selling$$</strong>
						</p>
						<p class="item line-height-large clearfix-child margin-bottom-none hidden-{{no-stock}}" >
							<span class="grid pay-methods" >Parcelamento: até {{price_parts}}x de R$ $$financiamento$$ sem juros</span>
						</p>
						<p class="item margin-bottom-large line-height-large clearfix-child item-separator sizes-chooser-holder hidden-{{no-stock}}" >
							<span class="grid" >Escolha seu tamanho:</span>
							<span class="grid sizes-chooser clearfix-child" >
								{{FOREACH:sizes}}<a href="{{CROOT}}{{GT8:cart.root}}?action=adicionar-produto&produto={{id}}&clean-qsa=1" title="{{stock}}" rel="no-follow" >{{crr_size}}</a>{{/FOREACH}}
							</span>
						</p>
						<a id="eBtBuy" href="?clean-qsa=1" rel="no-follow" class="bt-buy href-button href-button-green line-height-large grid-12 marginless margin-bottom hidden-{{no-stock}}" ><span>Comprar</span></a>
						<p class="item line-height-large hidden show-{{no-stock}} margin-top-large" >
							<span class="grid-12 gt8-debug-error" >
								<span class="padding display-block" >Produto esgotado</span>
							</span>
						</p>
					</div>
					<div class="grid-12" id="eMagnifierC" >
						<img src="{{CROOT}}imgs/gt8/blank.gif" alt="[imagem ampliada]" width="1920" height="1440" />
						<img src="{{CROOT}}imgs/gt8/blank.gif" alt="[imagem ampliada]" width="1920" height="1440" />
						<img class="bt-close" src="{{CROOT}}imgs/bt-close-small.png" alt="[botão fechar]" width="32" height="32" />
					</div>
				</div>
			</div>
			<div class="row-12 fluid-12 clearfix-child" >
				<div class="TabbedPane" >
					<div class="header" >
						<div class="tab" ><div>Descrição</div></div>
						<div class="tab" ><div>Características</div></div>
						{{IF:$_SESSION['login']['level']>8}}<div class="tab" ><div>DB data</div></div>{{/IF}}
						<div class="clear"></div>
					</div>
					<div class="body" >
						<div class="card line-height" >
							<div class="padding" >
								##description##
							</div>
						</div>
						<div class="card line-height" >
							<div class="padding clearfix-child attributes" >
								<div class="clearfix-child container" >
									{{FOREACH:attributes|value}}
									<div class="grid-12 clearfix-child" >
										<div class="grid-4" >##attribute##</div>
										<div class="grid-8" >##prefix## ##value## ##suffix##</div>
									</div>
									{{/FOREACH}}
								</div>
							</div>
						</div>
						{{IF:$_SESSION['login']['level']>8}}<div class="card line-height" >
							<div class="padding" >
								{{METHOD:getDBARow}}{{/METHOD}}
							</div>
						</div>{{/IF}}
					</div>
				</div>
			</div>
			{{VIEW:footer}}
		</div>
	</body>
</html>