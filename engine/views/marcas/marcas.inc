<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Marcas ##brand## | Salão do Calçado</title>
		{{VIEW:head}}
		<link rel="stylesheet" type="text/css" href="{{CROOT}}css/catalog.css" />
		<script type="text/javascript" src="{{CROOT}}js/catalog.js" ></script>
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
				<div class="grid-3 m-grid-3 clearfix-child" >
					<div class="grid-12 marginless links-menu card bg-white box-shadow" >
						<h3 class="text-indent-medium margin-bottom-none" >{{category-title}}</h3>
						<div class="linksC padding-medium padding-top-none padding-bottom-none" >
							{{FOREACH:categories}}
							<a href="?familia={{filename}}" class="{{selected}}" >##title##</a>
							{{/FOREACH}}
						</div>
					</div>
					<div id="eFilterSizes" class="grid-12 marginless links-menu card bg-white box-shadow" >
						<h3 class="text-indent margin-bottom-none" >Tamanhos</h3>
						<div class="padding-small padding-top clearfix-child" >
							{{FOREACH:filter-sizes}}
							<span class="grid-2h margin-left-m margin-right-m margin-bottom" ><a href="?tamanhos={{size}}" class="href-button line-height-large link-click-toggle {{selected}}" ><span>{{size}}</span></a></span>
							{{/FOREACH}}
						</div>
					</div>
					<div class="grid-12 marginless links-menu card bg-white box-shadow" >
						<h3 class="text-indent margin-bottom-none" >Preços</h3>
						<p class="padding" >
							<span class="display-block clearfix-child" >
								<span class="float-left" >De</span>
								<span class="float-right" >Até</span>
							</span>
							<span class="display-block clearfix-child" >
								<span class="float-left" id="eMinPriceValue" >R$ 50,00</span>
								<span class="float-right" id="eMaxPriceValue" >R$ 50,00</span>
							</span>
						</p>
						<div class="padding padding-top-none padding-bottom-none clearfix-child" >
							<div id="eSlider-H-min" class="Slider-h" >
								<div class="bar" ><div class="knob link-click-unique reset-index" >&nbsp;</div></div>
							</div>
							<div id="eSlider-H-max" class="Slider-h" >
								<div class="bar" ><div class="knob link-click-unique reset-index" >&nbsp;</div></div>
							</div>
						</div>
					</div>
					<div class="grid-12 marginless links-menu card bg-white box-shadow" id="eFilterColorsC" >
						<h3 class="text-indent margin-bottom-none" >Cores</h3>
						<div class="padding-small padding-top clearfix-child" >
							{{FOREACH:filter-colors}}
							<span class="grid-2h margin-left-m margin-right-m margin-bottom" title="{{color}}" ><a href="?cores={{color}}" class="href-button line-height-large link-click-toggle color color-{{color}} {{selected}}" ><span>&nbsp;</span></a></span>
							{{/FOREACH}}
						</div>
					</div>
					{{FOREACH:attributes}}<div class="grid-12 marginless links-menu card bg-white box-shadow" >
						<h3 class="text-indent-medium margin-bottom-none" >##attribute##</h3>
						<div class="linksC padding-medium padding-top-none padding-bottom-none click-toggle" >
							{{foreach-column:attribute}}<a href="?atributos=##attribute##-##value##" class="checkbox {{selected}}" >##value##<small>({{total}})</small></a>{{/foreach-column}}
						</div>
					</div>{{/FOREACH}}
				</div>
				{{$this->setCards()}}
				<div class="grid-9 m-grid-3 marginless clearfix-child" >
					<div class="grid-12 margin-bottom bg-white box-shadow clearfix-child" >
						<h3 class="clearfix-child margin-bottom-none hidden-{{found-0-rows}}" >
							<span class="mobile-hidden float-left text-indent font-size-small" >{{foundRows}} calçados encontrados.</span>
							<span class="e-select grid-3 m-grid-3 float-right font-size-small margin-top-mini margin-bottom-mini" >
								<select name="order" class="link-click-unique reset-index" >
									{{$this->getOrderOptions()}}
								</select>
								<span class="button group-button" ><strong>&nbsp;</strong><img src="{{CROOT}}imgs/gt8/arrow-down-mini.png" alt="" /></span>
							</span>
							<small class="float-right suffix-m mobile-hidden" ><small>Ordernar por:</small></small>
						</h3>
						<h3 class="clearfix-child margin-bottom-none hidden show-{{found-0-rows}}" >
							<span class="mobile-hidden float-left text-indent font-size-small" >Nenhum produto foi encontrado.</span>
						</h3>
					</div>
					<div class="grid-12 marginless clearfix-child" >
						{{FOREACH:rows}}
						<a title="##title##" href="{{CROOT}}{{l_path}}" class="grid-4 t-grid-3 m-grid-3 margin-bottom padding-bottom-small card bg-white box-shadow text-decoration-none text-align-center" >
							<strong class="brand text-uppercase off off-{{off}}" >##brand##</strong>
							<span class="bt-admin bt-admin-{{bt_admin}}" >&nbsp;</span>
							<span class="imgC height-2hm" >
								<img alt="[imagem ilustrativa]" src="{{CROOT}}{{varname}}?translate-img=1&preview" >
							</span>
							<span class="title" >##title##</span>
							<strong class="price_selling currency" >R$ $$price_selling$$</strong>
							<small class="price_parts currency" >{{price_parts}} x R$ $$price_finantial$$</small>
						</a>
						{{/FOREACH}}
					</div>
					<div class="grid-12 margin-bottom bg-white box-shadow clearfix-child hidden-{{found-0-rows}}" >
						<h3 class="clearfix-child margin-bottom-none" >
							<span class="paging m-grid-3 float-left margin-top-mini font-size-small click-unique" >
								{{page}}
							</span>
							<span class="e-select grid-3 m-grid-3 float-right font-size-small margin-top-mini" >
								<select name="order" class="link-click-unique reset-index" >
									{{METHOD:getOrderOptions}}{{/METHOD}}
								</select>
								<span class="button group-button" ><strong>&nbsp;</strong><img src="{{CROOT}}imgs/gt8/arrow-down-mini.png" alt="" /></span>
							</span>
							<small class="float-right suffix-m mobile-hidden" ><small>Ordernar por:</small></small>
						</h3>
					</div>
				</div>
			</div>
			{{VIEW:footer}}
		</div>
	</body>
</html>