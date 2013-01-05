<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Salão do Calçado | HOME</title>
		<link rel="stylesheet" type="text/css" href="{{CROOT}}css/home.css" />
		{{VIEW:head}}
		<script type="text/javascript" src="{{CROOT}}js/home.js" ></script>
	</head>
	<body>
		<div id="eMain" >
			{{VIEW:header}}
			<div class="banner background-effect" >
				<div class="img images-step" >
					{{$this->setBannersData(home,banners-home,banners-home-rows)}}
					{{FOREACH:banners-home-rows}}
					<div class="img bg-home" style="background-image:url({{GT8:explorer.root}}{{l_path}}{{bg}}); " >
						<div class="row-12 fluid-12 clearfix-child" >
							<a class="grid-12 shadow" href="{{link}}" style="{{style-banner}}" >
								<img src='{{GT8:explorer.root}}{{l_path}}' alt='[Imagem ilustrativa]' width='100%' />
							</a>
						</div>
					</div>
					{{/FOREACH}}
				</div>
				<div class="hidden lazy-load" ></div>
				<div class="enunciation" >
					<div class="title titles-step row-12 fluid-12" >
						{{FOREACH:banners-home-rows}}<h5 class="grid-11 prefix-m suffix-m margin-bottom-small" >##title##</h5>{{/FOREACH}}
					</div>
					<div class="text texts-step row-12 fluid-12" >
						{{FOREACH:banners-home-rows}}<p class="grid-11 prefix-m suffix-m" >##sumary##{{/FOREACH}}
					</div>
				</div>
				<div class="slider-steps" >
					<div class="row-12 fluid-12" >
						<div class="grid-12" >
							<div class="abs-right abs-top" >
								{{FOREACH:banners-home-rows}}<a href='link-1/' >&nbsp;</a>{{/FOREACH}}
							</div>
						</div>
					</div>
				</div>
				<div class="row-12 fluid-12 clearfix-child" >
					<div class="grid-12" >
						<img src="imgs/home-banner-emulator.gif" class="emulator" alt="" width="100%" />
						<div class="bar-indicator" ></div>
					</div>
				</div>
			</div>
			<div class="brands-highlights margin-bottom" >
				<div class="shadow-large-top" >
					<div class="row-12 fluid-12 cards clearfix-child padding-top-large" >
						<a href="busca/avanzato/" class="grid-3 card-transparent text-align-center height-2" >
							<span class="paragraph imgC height-1" >
								<img src="imgs/brands/avanzato.png" alt="[marca Avanzato]" width="200" height="48" />
							</span>
							<span class="hr" >&nbsp;</span>
						</a>
						<a href="busca/daleph/" class="grid-3 card-transparent text-align-center height-2" >
							<span class="paragraph imgC height-1" >
								<img src="imgs/brands/daleph.png" alt="[marca Daleph]" width="197" height="66" />
							</span>
							<span class="hr" >&nbsp;</span>
						</a>
						<a href="busca/carolina-castro/" class="grid-3 card-transparent text-align-center height-2" >
							<span class="paragraph imgC height-1" >
								<img src="imgs/brands/carolinacastro.jpg" alt="[marca Carolina Castro]" width="200" height="84" />
							</span>
							<span class="hr" >&nbsp;</span>
						</a>
						<a href="busca/emporio-naka/" class="grid-3 card-transparent text-align-center height-2" >
							<span class="paragraph imgC height-1" >
								<img src="imgs/brands/emporionaka.jpg" alt="[marca Empório Naka]" width="200" height="39" />
							</span>
							<span class="hr" >&nbsp;</span>
							<span class="paragraph" >
								&nbsp;
							</span>
						</a>
					</div>
				</div>
			</div>
			<div class="row-12 fluid-12 margin-bottom clearfix-child hidden-0" >
				<h3 class="grid-12 bg-halflife box-shadow clearfix-child margin-bottom-none border-radius" >
					<span class="display-block text-indent font-size-small" >
						{{$this->getSpecialOffersField(home,title)}}
					</span>
				</h3>
			</div>
			<div class="offers-highlights" >
				<div class="row-12 fluid-12 cards clearfix-child" >
					{{FOREACH:special-offers-home-rows}}
					<a href="{{CROOT}}{{l_path}}" class="grid-3 card box-shadow text-align-center margin-bottom-medium" >
						<span class="border display-block clearfix-child height-3h padding-small" >
							<strong class="grid-12 line-height-large" >##brand##</strong>
							<span class="imgC grid-12 height-hm margin-bottom" >
								<img alt="[imagem ilustrativa]" src="{{CROOT}}{{varname}}?translate-img=1&preview" />
							</span>
							<span class="grid-12 hr" >&nbsp;</span>
							<span class="grid-12 line-height" >##title##</span>
						</span>
					</a>
					{{/FOREACH}}
				</div>
			</div>
			{{VIEW:footer}}
		</div>
	</body>
</html>