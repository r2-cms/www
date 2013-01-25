jCube.Include("Array.each");
jCube.Include("Document.Cookie");
jCube.Include("Element.addClass");
jCube.Include("Element.getElementsBySelector");
jCube.Include("Element.getNodeIndex");
jCube.Include("Element.getParent");
jCube.Include("Element.removeClass");
jCube.Include("Event");
jCube.Include("Event.add");
jCube.Include("Math.ruleOfThree");
jCube.Include("UI.Sortables");
jCube.Include("Server.HttpRequest");
jCube.Include("Time.Chronometer");
jCube.Include("Transition.moveBackground");
jCube.Include("Transition.moveTo");
jCube.Include("Window.DOMReady");

var sortables;

function SortablesInit() {
	function Set( container, childs, doEffect) {
		var eBtLeft		= jCube(':#eBtGoPreviousPage');
		var eBtRight	= jCube(':#eBtGoNextPage');
		var eDragLimitRight	= 0;
		var eDragOffset	= 0;
		var delay	= 0;
		var w		= 0;
		var containerChanged	= false;
		var lastValueSaved	= null;
		sortables	= new jCube.UI.Sortables({
			holder:		container,
			W:			1000,
			H:			1500,
			cols:		container.query('::div > .card').length>4? 4: container.query('::div > .card').length,
			motionType:	jCube.UI.Sortables.MOTION_FORWARD,
			onDragStart: function() {
				eDragOffset		= jCube(':.home-cards-container').offsetLeft;
				eDragLimitLeft	= eBtLeft.offsetWidth;
				eDragLimitRight	= eBtRight.offsetLeft>0? eBtRight.offsetLeft: 10000000;
				delay	= 0;
				w		= this.offsetWidth;
			},
			onDrag: function(e) {
				sortables.isDragging	= true;
				
				if ( (this.offsetLeft+eDragOffset+w) > eDragLimitRight ) {
					jCube(':#eBtGoPreviousPage').removeClass('selected');
					delay	= delay || new Date().getTime();
					
					if ( new Date().getTime()-delay > 250) {
						jCube(':#eBtGoNextPage').addClass('selected');
					} else {
						jCube(':#eBtGoNextPage').removeClass('selected');
					}
				} else if ( this.offsetLeft+eDragOffset < eDragLimitLeft ) {
					jCube(':#eBtGoNextPage').removeClass('selected');
					delay	= delay || new Date().getTime();
					
					if ( new Date().getTime()-delay > 250) {
						jCube(':#eBtGoPreviousPage').addClass('selected');
					} else {
						jCube(':#eBtGoPreviousPage').removeClass('selected');
					}
				} else {
					jCube(':#eBtGoPreviousPage').removeClass('selected');
					jCube(':#eBtGoNextPage').removeClass('selected');
				}
				//jCube(':h1').setHTML( (this.offsetLeft+eDragOffset)+" : "+ eDragLimitLeft+" : "+ (new Date().getTime()-delay));
			},
			onDragComplete:	function( e) {
				if ( jCube(':#eBtGoNextPage').className.contains('selected') ) {
					var pageIndex	= jCube(':footer.main .pages-holder > a.selected').getNodeIndex();
					var container	= jCube('::.home-cards-container')[ pageIndex+1];
					if ( container) {
						container.appendChild( this);
						containerChanged	= true;
					}
				} else if ( jCube(':#eBtGoPreviousPage').className.contains('selected') ) {
					var pageIndex	= jCube(':footer.main .pages-holder > a.selected').getNodeIndex();
					var container	= jCube('::.home-cards-container')[ pageIndex-1];
					if ( container) {
						container.appendChild( this);
						containerChanged	= true;
					}
				}
				jCube(':#eBtGoPreviousPage').removeClass('selected');
				jCube(':#eBtGoNextPage').removeClass('selected');
				
				var s	= '';
				jCube('::.page-container .home-cards-container .card').each(function(){
					if ( this.query(':.module-name')) {
						s	+= this.query(':.module-name').innerHTML +','+ this.getParent('.page-container').getNodeIndex() +','+ this.getNodeIndex() +'|';
					} else {
						//s	+= '';
					}
				});
				s	= s.substring(0, s.length-1);
				
				if ( s != lastValueSaved) {
					lastValueSaved	= s;
					(new jCube.Server.HttpRequest({
						url: '?action=save-admin-icon-position&format=JSON&value='+ s
					})).start();
				}
			},
			onCompleteSortableDragFinish: function(e) {
				if ( containerChanged) {
					this.style.left	=
					this.style.top	= null;
					containerChanged	= false;
					for ( var i=jCube("::.home-cards-container").length-1; i>-1; i--) {
						Set( jCube("::.home-cards-container")[i], jCube("::.home-cards-container")[i].query('::.card'));
					}
				}
			}
		});
		if ( doEffect) {
			SortablesInit.initialized	= true;
			sortables.childs.each(function(){
				this.setStyle("left", 375).setStyle("top", 200);
			});
			sortables.organize();
		} else {
			sortables.organize(0);
		}
		childs.addEvent('onclick', function(e) {
			if ( sortables.isDragging) {
				sortables.isDragging	= false;
				e.stop();
				return false;
			}
			return true;
		});
	}
	var count=0;
	for ( var i=jCube("::.home-cards-container").length-1; i>-1; i--) {
		Set( jCube("::.home-cards-container")[i], jCube("::.home-cards-container")[i].query('::.card'), !SortablesInit.initialized&&i==0);
	}
}
jCube(function() {
	SortablesInit();
	
	(function() {//creates page navigation on footer
		var html	= '';
		var savedIndex	= ASP.adminPageIndex;
		for ( var i=0, len=jCube('::.body .page-container').length; i<len; i++) {
			html	+= '<a href="#" '+ (savedIndex==i?'class="selected" ':'') +'>&nbsp;</a>';
		}
		jCube(':footer.main .pages-holder').setHTML(html);
	})();
	
});
jCube(function(){//PAGES
	var crrPageIndex	= 0;
	var totalPages		= jCube('::#eMain > .body .page-container').length - 1;
	var eBody	= jCube(':#eMain > .body');
	var bX		= 0;
	function MovePage( options) {
		options	= options || {};
		eBody.isScrolling	= true;
		eBody.moveTo({
			fromLeft: this.scrollLeft,
			left: Math.min( 0, -crrPageIndex*window.getWidth()),
			top: 0,
			duration: 950,
			onStart: function() {
				jCube(document.body).moveBackground({
					toX: Math.min( 0, -crrPageIndex*window.getWidth()) * 0.25,
					duration: 750
				});
				this.tmoveTo	= this.getData('transition::moveTo').timeout;
				this.isBGMoving	= true;
			},
			onChange: function() {
				this.scrollLeft	= this.style.left.toInteger() * -1;
			},
			onComplete: function() {
				window.setTimeout(function(){ eBody.isBGMoving	= false; }, 100);
			},
			transition: options.transition || jCube.Transition.DEFAULT
		});
		
		BtsFooterUpdate();
		SavePageIndex();
	};
	function BtsFooterUpdate() {//buttons visibility
		//optimization
		var eP	= this.eP || jCube('::#eBtGoPreviousPage');
		var eN	= this.eP || jCube('::#eBtGoNextPage');
		
		var crrA	= jCube('::footer.main > .pages-holder a').removeClass('selected')[crrPageIndex];
		
		if ( !crrA) {
			crrA	= jCube('::footer.main > .pages-holder a')[0];
			crrPageIndex	= 0;
		}
		if ( !crrA) {
			throw new Error('Indexaçao errada! Procure o webmaster!');
		}
		crrA.addClass('selected');
		eP.removeClass('hidden');
		eN.removeClass('hidden');
		if ( crrPageIndex == totalPages) {
			eN.addClass('hidden');
		}
		if ( crrPageIndex == 0) {
			eP.addClass('hidden');
		}
	}
	function SavePageIndex() {
		if ( SavePageIndex.lastValue != crrPageIndex) {
			SavePageIndex.lastValue	= crrPageIndex;
			(new jCube.Server.HttpRequest({
				url: '?action=save-admin-page-index-position&value='+ crrPageIndex
			})).start();
		}
	}
	(function(){//container's width
		jCube(':.overflow-view').setStyle('width', (jCube('::.overflow-view > .page-container').length*100 + 10)+'%');
		jCube('::.overflow-view > .page-container').setStyle('width', window.getWidth());
	})();
	(function(){//MOUSE GESTURE: body horizontal scroll
		var eBody	= jCube(':.body');
		var chron	= new jCube.Time.Chronometer({
			onComplete: SavePageIndex
		});
		eBody.addEvent('onscroll', function(E){
			if ( !this.isBGMoving) {
				jCube(document.body).setBackgroundPosition(
					Math.ruleOfThree( window.getWidth(), Math.min( 0, -1*window.getWidth()) * 0.25, this.scrollLeft),
					'bottom'
				);
				
				if ( this.tmoveTo) {
					window.clearTimeout(this.tmoveTo);
					this.tmoveTo	= 0;
				}
				
				this.setStyle('left', this.scrollLeft);
				BtsFooterUpdate();
				crrPageIndex	= Math.round(jCube(':.body').scrollLeft/window.getWidth());
				chron.start(2000);
			}
		});
	})();
	jCube('::#eBtGoPreviousPage').addEvent('onclick', function(e) {
		if ( crrPageIndex > 0 ) {
			crrPageIndex--;
			MovePage();
		}
	});
	jCube('::#eBtGoNextPage').addEvent('onclick', function(e) {
		if ( crrPageIndex < totalPages) {
			crrPageIndex++;
			MovePage();
		}
	});
	jCube('::footer.main > .pages-holder').addEvent('onclick', function(e) {
		var obj	= e.target;
		
		if ( jCube(obj).nodeName == 'A') {
			e.stop();
			crrPageIndex	= obj.getNodeIndex();
			MovePage();
		}
	});
	jCube(':#eBtAddPages').addEvent('onclick', function(e) {//ADD PAGE
		e.stop();
		
		//crie uma página
		var ePage	= jCube(document.createElement('A')).appendTo(jCube(':footer.main > .pages-holder')).addClass('new').setHTML('&nbsp;');
		window.setTimeout(function(){ ePage.removeClass('new');}, 250);
		
		//crie um container
		var eHolder	= jCube(document.createElement('DIV')).appendTo(jCube(':.body .overflow-view')).addClass('page-container');
		eHolder.appendChild( jCube(document.createElement('DIV')).addClass('home-cards-container row'));
		
		totalPages++;
		jCube('::#eBtGoNextPage').removeClass('hidden');
	});
	
	//others
	crrPageIndex	= ASP.adminPageIndex;
	MovePage();
	jCube(document.body).setBackgroundPosition( Math.min( 0, -crrPageIndex*window.getWidth()) * 0.25, 'bottom');
	
});
