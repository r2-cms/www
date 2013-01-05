jCube.Include('Array.each');
jCube.Include('Element.addClass');
jCube.Include('String.endsWith');
jCube.Include('String.substringIndex');

jCube(function(){
	var loc	= window.location +'';
	loc	= loc.substring(0, loc.lastIndexOf('/')+1);
	loc	= loc.substringIndex('/', -2);
	loc.substring(0, loc.length-1);
	
	var found	= false;
	jCube('::ul#eInstitucionalMenu li > a.href-button-cancel').each(function(){
		if ( this.href.endsWith(loc)) {
			this.addClass('href-button-orange');
			found	= true;
			this.href	= '../';
		}
	});
	if ( !found) {
		//jCube(':ul#eInstitucionalMenu a.href-button-cancel').addClass('href-button-orange');
	}
	jCube('::.cards > a.card').addEvent('onmouseover', function(e){
		this.addClass('not-me');
		jCube('::.cards > a.card:not(.not-me)').fadeOut({
			opacity: 0.2
		});
	}).addEvent('onmouseout', function(e){
		this.removeClass('not-me');
		jCube('::.cards > a.card:not(.not-me)').fadeIn();
	});
});