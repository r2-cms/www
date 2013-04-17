<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>##title## | Salão do Calçado</title>
		{{VIEW:head}}
		<link rel="stylesheet" type="text/css" href="{{CROOT}}css/product.css" />
		<script type="text/javascript" src="{{CROOT}}js/product.js" ></script>
	</head>
	<body class="catalog home" >
		<div id="eMain" itemscope itemtype="http://data-vocabulary.org/Product" >
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
									<img class="img" id="eBaseImage" src="?translate-img=1&amp;preview" alt="[pré-visualização]" itemprop="image" />
									{{FOREACH:thumbs}}<img class="img" src="{{varname}}{{imgname}}/?preview" alt="[imagem ilustrativa]" />{{/FOREACH}}
								</div>
								<div class="hidden lazy-load" ></div>
								<div class="enunciation" >
									<div class="title titles-step" >
										<h5 class="margin-bottom-small active" >&nbsp;</h5>
										{{FOREACH:thumbs}}<h5 class="margin-bottom-small" >##title##</h5>{{/FOREACH}}
									</div>
									<div class="text texts-step" >
										<p class=" active" >##title##</p>
										{{FOREACH:thumbs}}<p>##title##</p>{{/FOREACH}}
									</div>
								</div>
								<div class="slider-steps" >
									<a href='link-1/' class="active" >
										<img src="?translate-img=1&amp;small" alt="" width="48" height="32" />
									</a>
									{{FOREACH:thumbs}}
									<a href='{{varname}}{{imgname}}/?preview' class="active" >
										<img src="{{varname}}{{imgname}}/?small" alt="" width="48" height="32" />
									</a>
									{{/FOREACH}}
									<div class="bar-indicator" ></div>
								</div>
							</div>
						</div>
					</div>
					<div class="info grid-5 clearfix-child" >
						<h1 class="margin-top-small" itemprop="name" >##title## {{bt-admin}}</h1>
						<div class="hidden" >
							<div itemprop="brand" class="brand" >##brand##</div>
						</div>
						<p class="price margin-bottom-large hidden-{{no-stock}}" itemprop="offerDetails" itemscope itemtype="http://data-vocabulary.org/Offer" >
							<meta itemprop="currency" content="BRL" />
							<strong><small>R$</small> <span itemprop="price" >$$price_selling$$</span></strong>
						</p>
						<p class="item line-height-large clearfix-child margin-bottom-none hidden-{{no-stock}}" >
							<span class="grid pay-methods" >Parcelamento: até {{price_parts}}x de R$ <strong>$$financiamento$$</strong> sem juros</span>
						</p>
						<p class="item margin-bottom-large line-height-large clearfix-child item-separator sizes-chooser-holder hidden-{{no-stock}}" >
							<span class="grid sizes-chooser-label" >Escolha seu tamanho:</span>
							<span class="grid sizes-chooser clearfix-child" >
								{{FOREACH:sizes}}
								<a href="{{CROOT}}{{GT8:cart.root}}?action=adicionar-produto&amp;produto={{id}}&amp;clean-qsa=1" title="{{stock}} em estoque" rel="nofollow" >{{crr_size}}</a>
								{{/FOREACH}}
							</span>
							<em id="eStockInfo" class="grid-12 text-align-center" >&nbsp;</em>
						</p>
						<a id="eBtBuy" href="?clean-qsa=1" rel="nofollow" class="bt-buy href-button href-button-blue line-height-large grid-12 marginless margin-bottom hidden-{{no-stock}}" >
							<span>Comprar</span>
						</a>
						{{IF: $this->isUnavaiable}}
						<p class="item line-height-large hidden show-{{no-stock}} margin-top-large" >
							<span class="grid-12 gt8-debug-error" >
								<span class="padding display-block" >Produto esgotado</span>
							</span>
						</p>
						{{/IF}}
					</div>
					<div class="grid-12" id="eMagnifierC" >
						<img src="{{CROOT}}imgs/gt8/blank.gif" alt="[imagem ampliada]" width="1920" height="1440" />
						<img src="{{CROOT}}imgs/gt8/blank.gif" alt="[imagem ampliada]" width="1920" height="1440" />
						<img class="bt-close" src="{{CROOT}}imgs/bt-close-small.png" alt="[botão fechar]" width="32" height="32" />
					</div>
				</div>
			</div>
			<div class="row-12 fluid-12 clearfix-child" >
				<div class="TabbedPane grid-12" >
					<div class="header" >
						<div class="tab" ><div>Descrição</div></div>
						<div class="tab" ><div>Características</div></div>
						{{IF:$_SESSION['login']['level']>8}}<div class="tab" ><div>DB data</div></div>{{/IF}}
						<div class="clear"></div>
					</div>
					<div class="body" >
						<div class="card line-height" >
							<div class="padding" itemprop="description" >
								##description##
							</div>
						</div>
						<div class="card line-height" >
							<div class="padding clearfix-child attributes" >
								<div class="clearfix-child container" >
									{{FOREACH:attributes|value}}
									<div class="grid-12 clearfix-child" >
										<div class="attr-name grid-4" >##attribute##</div>
										<div class="attr-value grid-8" >##prefix## ##value## ##suffix##</div>
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
			<meta itemprop="identifier" content="mpn:{{id}}" />
			<meta itemprop="category" content="##family##" />
			{{IF: $this->isUnavaiable}}<meta itemprop="availability" content="out_of_stock" />{{/IF}}
			{{IF: !$this->isUnavaiable}}<meta itemprop="availability" content="in_stock" />{{/IF}}
			{{VIEW:footer}}
		</div>
	</body>
</html>