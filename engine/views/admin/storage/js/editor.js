jCube(function(){//CHANGE STATUS
	
	var eModal	= null;
	var href	= '';
	var crrBtStatus	= null;
	jCube('::#eMailActionC a').addEvent('onclick', function(E){
		E.stop();
		
		href	= this.href;
		crrBtStatus	= this;
		eModal	= eModal || jCube(':#eModalChangeStatus');
		
		eModal.appendTo( document.body);
		
		eModal.showOverlay({
			objRef: this,
			duration: 1000,
			transition: jCube.Transition.DEFAULT,
			transitionIn: [0.3, 1.06, 0.5, 1.19],
			overlay: {
				border : 'none',
				background:'white',
				borderRadius: '7px',
				boxShadow: '0 0 80px 5px #333344',
				opacity: 0.8
			},
			glassPane: {
				transition: true,
				background: 'black',
				opacity: 0.5
			},
			onShowOverlayComplete: function() {
				
			}
		});
	});
	jCube(':#eModalChangeStatus a.href-button-ok').addEvent('onclick', function(E){
		E.stop();
		var req	= new jCube.Server.HttpRequest({
			url: href + '&format=JSON',
			noCache: true,
			method: jCube.Server.HttpRequest.HTTP_POST,
			onLoad: function() {
				eModal.showOverlay();
				if ( this.ret.affected) {
					jCube('::#eMailActionC a.href-button-ok').removeClass('href-button-ok').addClass('href-button-cancel');
					crrBtStatus.removeClass('href-button-cancel').addClass('href-button-ok');
					jCube(':#eSumaryStatus').setHTML( crrBtStatus.getFirstChild().innerHTML);
				}
			},
			onError: function() {
				eModal.showOverlay();
			}
		});
		GT8.Spinner.request(req);
	});
	jCube(':#eModalChangeStatus a.href-button-cancel').addEvent('onclick', function(E){
		E.stop();
		
		eModal.showOverlay();
	});
	jCube('::#eMailActionC a').each(function(){
		if ( jCube(':#eSumaryStatus').innerHTML.contains( '('+this.id.substring(4)+')') ) {
			this.addClass('href-button-ok').removeClass('href-button-cancel');
		}
	});
});