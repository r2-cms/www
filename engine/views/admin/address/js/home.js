jCube.Include('Element.setFixedMask');

//URL REWRITE FOR PAGER.CLICK EVENT
jCube(function(){
	Pager.click._uf	= function( eA, crr, goTo) {
		goTo	= Pager.parse( 'uf', crr, true);
		return goTo;
	}
	//Pager.click._address	= function( eA, crr, goTo) {
	//	return ??;
	//}
	jCube('::#eFilters label input[name=zip]').each(function(){
		var value	= this.value;
		this.setFixedMask('#####-###').setValue(value);
	});
	//DELETE
	Pager.createDeleteButtons();
});
