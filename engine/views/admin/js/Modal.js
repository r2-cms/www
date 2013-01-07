/*
	Modal
	Objs:
		crrOpener
	Listeners:
		onClose({})
*/
var Modal	= {
	getFrame: (function() {
		var iframe;
		return function() {
			if ( !iframe) {
				iframe	= jCube(document.createElement("IFRAME"));
				iframe.allowTransparency	= "true";
				iframe.frameBorder			= 0;
				iframe.border				= 0;
				iframe.setStyle({
					position: "fixed",
					boxShadow: '0px 0px 42px #000'
				});
			}
			return iframe;
		}
	})(),
	/**
	 * options:
	 * 	width, height, left, top, onPageLoad, onChoose, headbar, toolbar, locationbar, statusbar, gets[name, value]
	*/
	show: function( options) {
		//argumentos
		options.width	= options.width || 640;
		options.height	= options.height || Math.min(440, window.getHeight()-100);
		options.left	= options.left!=null? options.left: window.getWidth()/2 - options.width/2;
		options.top		= options.top!=null? options.top: window.getHeight()/2 - options.height/2;
		
		
		options.url	= options.url.contains('?')? options.url + '&modal=1': options.url+'?modal=1';
		options.url	= options.url + '&noCache='+ new Date().getTime();
		
		if ( options.headbar != null) {
			options.url	= options.url +'&headbar='+ (options.headbar||0);
		}
		if ( options.toolbar != null) {
			options.url	= options.url +'&toolbar='+ options.toolbar;
		}
		if ( options.locationbar != null) {
			options.url	= options.url +'&locationbar='+ options.locationbar;
		}
		if ( options.statusbar) {
			options.url	= options.url +'&statusbar='+ options.statusbar;
		}
		
		Modal.onChoose	= options.onChoose || null;
		
		Modal.crrOpener	= options.objRef;
		
		this.getFrame().appendTo( document.body).setStyle({
			opacity: 0,
			width: options.width,
			height: options.height
		}).setProperty('src', options.url).showOverlay({
			objRef: options.objRef,
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
		return false;
	},
	hide:	function( isCancelAction){
		var Closing	= function(){
			if (isCancelAction) {
				parent.Modal.getFrame().showOverlay();
				return;
			}
			if ( !Editor.isRequesting) {
				if ( Modal.onChoose) {
					Modal.onChoose(ASP.cardListerName +'-editor', ASP.id, Editor.changes);
				}
				
				if ( parent.Modal.onClose) {
					parent.Modal.onClose( ASP.cardListerName +'-editor', ASP.id, Editor.changes);
				}
				parent.Modal.getFrame().showOverlay();
			}
		}
		if ( Editor.isRequesting) {
			GT8.Spinner.show({
				label: 'Salvando alterações',
				showGlassPane: true,
				glassPaneOptions: { background: 'rgba(50,50,100, 0.2)'}
			});
			window.setInterval(Closing, 250);
		} else {
			Closing();
		}
		return false;
	},
	close: function() {
		
	},
	choose: function( id, eCard) {
		
		parent.Modal.choose(id);
		parent.Modal.close();
		
		return false;
	}
}
