

jCube(function(){
	function CheckFoundRows( force) {
		if ( !force && jCube('::.shopping-content .items > a').length < ASP.foundRows) {
			
		} else {
			jCube(':#eBtMore').addClass('hidden');
		}
	}
	jCube(':#eOrder select').onchange	= function(e) {
		var E	= jCube.Event(e);
		E.stop();
		jCube.Document.Cookie.set('catalog-order', this.getOptionValue());
		window.location.reload();
	}
	jCube(':#eBtMore').addEvent('onclick', function(e){
		e.stop();
		
		ASP.index	= ASP.index==0? 2: ASP.index + 1;
		
		jCube(':#eBtMore .spinner').removeClass('hidden');
		var req	 = new jCube.Server.HttpRequest({
			url: '?action=request&format=JSON&index='+ ASP.index,
			onLoad: function() {
				jCube(':#eBtMore .spinner').addClass('hidden');
				
				if ( this.responseText) {
					var div	= document.createElement('DIV');
					div.innerHTML	= this.responseText;
					
					var eHolder		= jCube(':.shopping-content .items');
					var eLastChild	= jCube(':.shopping-content .items').getLastChild();//suppose to be an Element.clear
					eLastChild.remove();
					
					var len	= div.childNodes.length, crr, count=0;
					while ( (crr = jCube(div).getFirstChild())) {
						eHolder.appendChild( crr);
						crr.query(':.imgC img').onload	= function() {
							GT8.adjustImgSize( this);
						}
						GT8.adjustImgSize(crr.query(':.imgC img'));
						count++;
					}
					eHolder.appendChild( eLastChild);
					
					CheckFoundRows( count<ASP.limit);
				} else {
					CheckFoundRows();
				}
			},
			onError: function() {
				jCube(':#eBtMore .spinner').addClass('hidden');
				CheckFoundRows();
			}
		});
		if ( ASP.keywords) {
			req.addGet('q', ASP.keywords);
		}
		req.start();
	});
});