jCube.Include('Array.each');
jCube.Include('Element.addClass');
jCube.Include('Element.getOptionValue');
jCube.Include('Element.removeClass');
jCube.Include('Server.HttpRequest');
jCube.Include('String.endsWith');
jCube.Include('String.substringIndex');

jCube(function(){//location select
	var loc	= window.location +'';
	loc	= loc.substring(0, loc.lastIndexOf('/')+1);
	loc	= loc.substringIndex('/', -2);
	loc.substring(0, loc.length-1);
	
	var found	= false;
	jCube('::.links-menu > a').each(function(){
		if ( this.href.endsWith(loc)) {
			this.addClass('selected');
			found	= this;
		}
	});
	if ( !found) {
		
	}
});
