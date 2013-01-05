jCube.Include("Number.round");
jCube.Include("Pluggins.Slider");

jCube(function(){//MIN n MAX PRICES
	var crrSlider	= null;
	var onSliderChange	= function(){
		if ( crrSlider == jCube(":#eSlider-H-min")) {
			if ( s1.value > s2.value) {
				window.setTimeout(function(){s2.setValue( s1.value);}, 20);
			}
		} else if ( crrSlider == jCube(":#eSlider-H-max") ) {
			if ( s2.value < s1.value) {
				window.setTimeout(function(){s1.setValue( s2.value);}, 20);
			}
		}
		jCube("eMinPriceValue").innerHTML	= 'R$ '+ s1.value.round(0).toString() +',00';
		jCube("eMaxPriceValue").innerHTML	= 'R$ '+ s2.value.round(0).toString() +',00';
	}
	var s1	= new jCube.Pluggins.Slider({
		slider: jCube(":#eSlider-H-min"),
		min: 50,
		max: 1000,
		value: ASP.priceMin || 50,
		orientation: jCube.UI.Slider.HORIZONTAL_ORIENTATION,
		onStart: function() {
			crrSlider	= this;
		},
		onSlide: onSliderChange,
		onComplete: function( e, v) {
			if (  Math.abs( (v+0) - (ASP.priceMin-10)) > 10 ) {
				jCube(":#eSlider-H-min .knob").name	= 'preco-minimo';
				jCube(":#eSlider-H-min .knob").value	= (v+0)<s1.min+10? '': (v+0).round(0);
				jCube(":#eSlider-H-min .knob").trigger('click', {eventChange:true});
			}
		}
	});
	var s2	= new jCube.Pluggins.Slider({
		slider: jCube(":#eSlider-H-max"),
		min: 50,
		max: 1000,
		value: ASP.priceMax || 1000,
		orientation: jCube.UI.Slider.HORIZONTAL_ORIENTATION,
		onStart: function() {
			crrSlider	= this;
		},
		onSlide: onSliderChange,
		onComplete: function( e, v) {
			if (  Math.abs( (v+0) - (ASP.priceMax-10)) > 10 ) {
				jCube(":#eSlider-H-max .knob").name	= 'preco-maximo';
				jCube(":#eSlider-H-max .knob").value	= (v+0)>s1.max-20? '': (v+0).round(0);
				jCube(":#eSlider-H-max .knob").eventChange	= function() {}//apenas para o evento abaixo permitir a execução. (vide o evento em /js/main.js)
				jCube(":#eSlider-H-max .knob").trigger('click', {eventChange:true});
			}
		}
	});
});
jCube(function(){
	//fix ASCII names
	jCube('::#eFilterColorsC span > a.color').each(function(){
		if ( this.parentNode.title.toLowerCase().contains('onca')) {
			this.parentNode.title	= this.parentNode.title.replace('onca', 'onça');
		} else if ( this.parentNode.title.toLowerCase().contains('poas')) {
			this.parentNode.title	= this.parentNode.title.replace('poas', 'poás');
		}
	});
	jCube('::.card > .bt-admin-1').addEvent('onclick', function(E){
		//this.getParent('a').href
		
		var href	= this.getParent('a').href + '';
		var orig	= href;
		href		= ASP.padmin + 'explorer/catalogo/'+ href.substringIndex('/', -5);
		href		= href.substring(0, href.length-1) +'?edit';
		this.getParent('a').href	= href;
		
		//restaura o link, caso precise ser usado novamente, como no caso em que usamos ALT+CLICK
		var eA	= this.getParent('a');
		window.setTimeout(function(){ eA.href	= orig;}, 250);
	});
});