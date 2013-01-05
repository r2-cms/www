window.onload	= function() {
	if ( ASP.id && ASP.index) {
		var eImg	= jCube("img-preview");
		
		if ( eImg.offsetWidth > eImg.offsetHeight ) {
			eImg.style.top	= (windowH/2 - eImg.offsetHeight/2) +"px";
		} else {
			//eImg.style.left	= (windowW/2 - eImg.offsetWidth/2) +"px";
		}
		
		if ( parent.Uploader && parent.Uploader.onLoad) {
			parent.Uploader.onLoad( ASP)
		}
	}
	if ( !ASP.hasImage) {
		jCube("label").style.display	= "block";
		jCube("img-preview").className	= "img-add-more";
	} else {
		jCube("img-delete-bt").style.display	= "block";
	}
}
