jCube(function(){//MODAL
	
	jCube('::div .icon-enabled-1, div .icon-enabled-0').addEvent('onclick', function(E){
		E.stop();
		
		var enabled	= this.className.contains('icon-enabled-1')? 0: 1;
		if ( this.className.contains('icon-enabled-1')) {
			this.removeClass('icon-enabled-1').addClass('icon-enabled-0');
			
		} else {
			this.removeClass('icon-enabled-0').addClass('icon-enabled-1');
			
		}
		
		
		var id	= (this.getParent('.gt8-update-id').id +'');
		id	= id? id.match(/[0-9]+$/): [0];
		if ( id && id[0]) {
			id	= id[0];
		} else {
			id	= 0;
		}
		GT8.Spinner.request(new jCube.Server.HttpRequest({
			url: '?action=update&format=JSON&field=enabled&value='+ enabled +'&id='+ id,
			noCache: true
		}));
	});
	
	var eModal		= jCube(':#eModalEditor');
	var eFeedback	= jCube(':#eModalEditor .feedback');
	var eBtConfirm	= jCube(':#eModalEditor a.href-button-ok');
	var eBtCancel	= jCube(':#eModalEditor a.href-button-cancel');
	
	eBtCancel.addEvent('onclick', function(E) {
		E.stop();
		
		eModal.showOverlay();
	});
	eBtConfirm.addEvent('onclick', function(E){
		E.stop();
		
		//request
		var req	= new jCube.Server.HttpRequest({
			url: '?action=insert&format=JSON',
			noCache: true,
			method: jCube.Server.HttpRequest.HTTP_POST,
			onLoad: function() {
				var ret	= GT8.onGeneralRequestLoad.call( this, {}, true);
				eFeedback.addClass('spinner-small-hidden').setHTML('&nbsp;');
				
				if ( ret.error) {
					eFeedback.addClass('error').addClass('spinner-small-hidden').setHTML(ret.error);
				} else {
					eModal.showOverlay();
					eFeedback.addClass('spinner-small-hidden').setHTML(ret.message +'&nbsp;');
					
					//window.location.reload();
				}
			},
			onError: function() {
				eFeedback.addClass('error').addClass('spinner-small-hidden').setHTML('Não foi possível alterar o valor agora!');
			}
		});
		jCube('::#eModalEditor input, #eModalEditor select').each(function(){
			if ( this.name) {
				req.addGet(this.name, this.value);
			}
		});
		
		req.start();
		eFeedback.removeClass('spinner-small-hidden').removeClass('error').setHTML('<img src="'+ ASP.CROOT +'imgs/gt8/spinner.gif" alt="" class="float-left" />Inserindo...');
	});
	jCube(':#eBtAdd').addEvent('onclick', function(E){
		E.stop();
		
		eModal.appendTo( document.body);
		eFeedback.setHTML('&nbsp;').removeClass('error');
		
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
});