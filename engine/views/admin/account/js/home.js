jCube(function() {
	jCube('::.right-pane .card .imgC2 span img').each(function() {
		this.getParent().setStyle({
			marginTop: ((this.getParent().offsetHeight - this.offsetHeight)/2)
		});
	});
	
	//repetimos aqui em caso da imagem não ser carregada e a altura dela ser calculada errada
	jCube(window).addEvent('onload', function(){
		jCube('::.right-pane .card .imgC2 span img').each(function() {
			this.getParent().setStyle({
				marginTop: ((this.getParent().offsetHeight - this.offsetHeight)/2)
			});
		});
	});
});