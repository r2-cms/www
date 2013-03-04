jCube.Include("Event");
jCube.Include("Pluggins.SplitPane");
jCube.Include("Server.HttpRequest");
jCube.Include("Window.DOMReady");

var jsAdmin	= {
	logout:	function(e) {
		e	= jCube.Event(e);
		
		new jCube.Server.HttpRequest({
			url: '?logout',
			noCache: true,
			onLoad: function() {
				window.location.reload();
			}
		}).start();
		
		if ( e && e.stop) {
			e.stop();
		}
		return false;
	}
}

jCube(function(){
	jCube('::#eMain .body, h1').fadeIn(450);
	var sp;
	(function(jCube){//SPLIT PANE
		if ( jCube(':.SplitPane-horizontal')) {
			sp	= new jCube.Pluggins.SplitPane({
				splitPane: ':.SplitPane-horizontal',
				onResize: function() {
					jCube.Document.Cookie.set(ASP.cardListerName +'-sp-w', this.leftPane.style.width.toInteger(this.leftPane.offsetWidth));
				}
			});
			sp.setDividerLocation( Number(jCube.Document.Cookie.get(ASP.cardListerName+'-sp-w')) || 300, 'absolute');
		}
		var dividerLocation	= 0;
		function WindowResize( e) {
			if ( jCube(':.SplitPane-horizontal')) {
				var h	= window.getHeight() - (jCube(':header.admin div.space').offsetHeight) - jCube(':footer.footer-clear-height').offsetHeight;
				jCube(':.SplitPane-horizontal').setStyle('height', h);
				jCube(':.SplitPane-horizontal .left-pane').setStyle('height', h-3);
				jCube.Document.Cookie.set(ASP.cardListerName +'-sp-height', h);
				jCube('::.SplitPane-horizontal .right-pane div.info').setStyle({
					lineHeight: h/1.25
				});
				
				if ( window.getWidth() < 800) {
					if ( !dividerLocation) {
						dividerLocation	= sp.leftPane.offsetWidth;
					}
					sp.setDividerLocation(0);
				} else if ( dividerLocation) {
					sp.setDividerLocation(dividerLocation);
					dividerLocation	= 0;
				}
			}
		}
		jCube(window).addEvent('resize', WindowResize);
		WindowResize(false);
		jCube(document).addEvent( 'keydown', function(e){
			if ( window.Pager && Pager.cardNav) {
				Pager.cardNav.call( this, e);
			}
		});
	})(jCube);
	(function(jCube){//TOOLBARS
		jCube('::header.admin nav.toolbar span.group-button').each(function(){
			this.onGroupButtonActive	= this.onGroupButtonActive || (function( crrA) {
				if ( jCube(':.SplitPane-horizontal .sidebar>section.'+ crrA.title)) {
					jCube('::.SplitPane-horizontal .sidebar>section').setStyle('display', 'none');
					jCube(':.SplitPane-horizontal .sidebar>section.'+ crrA.title).setStyle('display', '');
					jCube.Document.Cookie.set( ASP.cardListerName +'-sidebar-crrVisible', crrA.title);
				}
			});
		});
	})(jCube);
	(function(jCube){//GRID STATE
		if ( jCube(':.SplitPane-horizontal .Grid')) {
			//WIDTH ADJUSTMENTS
			var eSP			= jCube(':.SplitPane-horizontal');
			var eSPLeft		= jCube(':.SplitPane-horizontal .left-pane');
			var eSPKnob		= jCube(':.SplitPane-horizontal .knob-pane');
			var eSPRight	= jCube(':.SplitPane-horizontal .right-pane');
			var eGrid		= jCube(':.SplitPane-horizontal .Grid');
			function GridResize() {
				eSPRight.setStyle('width', window.getWidth() - eSPLeft.offsetWidth - eSPKnob.offsetWidth)
			}
			jCube(window).addEvent('onresize', function(e){
				GridResize();
			});
			GridResize();
			
			jCube(':.SplitPane-horizontal').addEvent('onSplitPaneResize', function(e) {
				eGrid.setStyle({
					width: window.getWidth() - eSPLeft.offsetWidth - eSPKnob.offsetWidth - 24//scrollbar
				});
			})
			
			window.setTimeout(function(){
				jCube(':.SplitPane-horizontal').trigger('onSplitPaneResize');
			}, 250);
			
			//GRID STATE RECORD
			eGrid.addEvent('onGridColResizeComplete', function( E, col){
				var gridState	= '';
				var crr;
				jCube('::.Grid .head .g-col').each(function(){
					crr	= this.title.split('|');
					gridState	+= '{{}}'+ crr[1] +'|'+ this.offsetWidth;
				});
				gridState	= gridState.substring(4);
				var req	= new jCube.Server.HttpRequest({
					url: '?action=save-grid-state',
					onComplete: function() {
						
					}
				});
				req.addGet('name', ASP.cardListerName);
				req.addGet('value', gridState);
				req.start();
			}).addEvent('onGridColDragComplete', function(E){
				this.trigger('onGridColResizeComplete', E);
			});
		}
		
	})(jCube);
	(function(){//SEARCH
		jCube('::#eFilters input[name=q]').addEvent('onkeydown', function(e){
			Pager.search( this, e.event);
		});
	})();
	(function(){//FOOTER - flex space
		var eFlexSpace	= jCube(':footer.main .paging > .flex-space');
		var isVisible	= true;
		jCube(window).addEvent('resize', function(){
			
			//flexspace
			if ( window.getWidth() < 900) {
				if ( eFlexSpace && isVisible) {
					eFlexSpace.addClass('hidden');
					isVisible	= false;
				}
			} else {
				if ( eFlexSpace && isVisible === false) {
					eFlexSpace.removeClass('hidden');
					isVisible	= true;
				}
			}
		});
		
	})();
});