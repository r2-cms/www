jCube.Include('Array.removeDuplicates');
jCube.Include('Document.Cookie');

jCube(function(){
	var ckProds	= jCube.Document.Cookie.get('products-view');
	if ( ckProds)	 {
		ckProds	= ckProds.split(',');
	} else {
		ckProds	= [];
	}
	ckProds.push(ASP.id);
	jCube.Document.Cookie.set('products-view', ckProds.removeDuplicates().join(','), 60);
});
GT8.analytics.gets.push(['module', 'catalog::Product']);
GT8.analytics.gets.push(['action', 'addToHistory']);
GT8.analytics.gets.push(['idProduct', ASP.id]);
document.createElement('NAV');
