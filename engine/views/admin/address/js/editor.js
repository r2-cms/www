jCube(function(){
	jCube('::#card-general label input[name=zip]').setFixedMask('#####-###');
	
	Editor.enabled	= true;
	
	Pager.click._nav	= function( eA, crr, goTo) {
		
		return eA;
	}
});
