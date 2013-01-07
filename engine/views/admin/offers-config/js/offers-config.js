jCube.Include('Time.Chronometer');
jCube.Include('UI.Sortables');

var sortables, lastValueSaved;
function SortablesInit( duration) {
	
	jCube(':.TabbedPane').getData('UI.TabbedPane').openTab(1);
	
	sortables	= new jCube.UI.Sortables({
		holder:		jCube(':#eSortablesC'),
		motionType:	jCube.UI.Sortables.MOTION_FORWARD,
		onDragStart: function() {
			sortables.isDragging	= true;
		},
		onDrag: function(e) {
		},
		onDragComplete:	function( e) {
			
			var s	= '';
			jCube('::#eSortablesC > div').each(function(){
				s	+= this.query(':.title').id +',';
			});
			s	= s.substring(0, s.length-1);
			
			if ( s != lastValueSaved) {
				lastValueSaved	= s;
				GT8.Spinner.request(
					new jCube.Server.HttpRequest({
						url: '?action=save-position&format=JSON&ids='+ s,
						position: 'upper right'
					})
				);
			}
			window.setTimeout(function(){ sortables.isDragging	= false;}, 500);
		},
		onCompleteSortableDragFinish: function(e) {
			
		}
	});
	sortables.organize(duration||0);
	
	if ( jCube(':#eSortablesC > div')) {
		jCube(':#eSortablesC').setStyle('height', jCube(':#eSortablesC > div').offsetHeight * jCube('::#eSortablesC > div').length + 20);
	}
}
jCube(function(){//GENERIC
	SortablesInit();
});
jCube(function(){//LIMIT
	var lastValueSaved;
	var chron	= new jCube.Time.Chronometer({
		onComplete: function(){
			var value	= jCube(':#eLimit input').value;
			if ( value != lastValueSaved) {
				lastValueSaved	= value;
				GT8.Spinner.request(
					new jCube.Server.HttpRequest({
						url: '?action=save-limit&format=JSON&limit='+ lastValueSaved,
						position: 'upper right'
					})
				);
			}
		}
	});
	
	jCube(':#eLimit input').addEvent('onchange', function(E){
		chron.start(2000);
	});
});
jCube(function(){//RANDOM
	jCube(':#eRandom a').addEvent('onclick', function(E){
		var isRandom;
		if ( this.className.contains('selected')) {
			isRandom	= 0;
			this.removeClass('selected');
		} else {
			this.addClass('selected');
			isRandom	= 1;
		}
		GT8.Spinner.request(
			new jCube.Server.HttpRequest({
				url: '?action=save-random-option&format=JSON&random='+ isRandom,
				position: 'upper right'
			})
		);
	});
});
jCube(function(){//DELETE product
	jCube(':.add-product').addEvent('onclick', function(E){//clone the template
		E.stop();
		
		var eTemplate	= jCube(jCube(':#eCardTemplate').cloneNode(true));
		eTemplate.id	= '';
		eTemplate.removeClass('hidden');
		eTemplate.appendTo('eSortablesC');
		SortablesInit();
		eTemplate.setStyle('top', (eTemplate.getPreviousSibling()?eTemplate.getPreviousSibling().offsetTop: -100));
		sortables.organize(1000);
		eTemplate.query(':.imgC').addEvent('onclick', window.SearchOfferCard);
	});
});
jCube(function(){//DELETE product
	jCube(':.del-product').addEvent('onclick', function(E){//show delete
		E.stop();
		
		if ( this.className.contains('selected')) {
			jCube(':#eSortablesC').removeClass('show-delete-button');
			this.removeClass('selected');
		} else {
			jCube(':#eSortablesC').addClass('show-delete-button');
			this.addClass('selected');
		}
	});
	jCube('::#eSortablesC .bt-delete').addEvent('onclick', function(E){//show confirm
		E.stop();
		
		if ( this.className.contains('selected')) {
			this.removeClass('selected');
			this.getParent().query(':.bt-delete-confirm').removeClass('selected');
		} else {
			this.addClass('selected');
			this.getParent().query(':.bt-delete-confirm').addClass('selected');
		}
	});
	jCube('::#eSortablesC .bt-delete-confirm').addEvent('onclick', function(E){//exclude
		E.stop();
		
		if ( this.className.contains('selected') ) {
			var bounds	= this.getOffset(document.body);
			this.getParent('.card').resizeTo({
				height: 1,
				duration: 850,
				onComplete: function() {
					GT8.poof( bounds.left, bounds.top);
					this.remove();
					sortables.organize(1000);
					sortables.onDragComplete();
				}
			});
		} else {
			this.addClass('selected');
			this.getParent().query(':.bt-delete-confirm').addClass('selected');
		}
	});
});
jCube(function(){//SEARCH PRODUCT
	window.SearchOfferCard	= function(E){
		if ( sortables.isDragging) {
			return;
		}
		var path	= this.getParent('.card').query(':.ref-explorer-path')? this.getParent('.card').query(':.ref-explorer-path').innerHTML: '@@@';
		var eImg	= this.query(':img');
		var eMain	= this.getParent();
		Modal.show({
			objRef: this,
			onChoose: function( eA, ASP, mado){
				if ( eA && eA.query) {
					if ( eA.query(':.hidden .path').innerHTML.split('/').length != 5) {
						return;
					}
					
					eImg.src	= window.ASP.CROOT + eA.query(':.hidden .path').innerHTML.replace('catalogo/', '') + eA.query(':.hidden .filename').innerHTML +'/?translate-img=1&preview';
					
					var brand	= eA.query(':.hidden .path').innerHTML.substringIndex('/', -2);brand	= brand.substring(0, brand.length-1);
					eMain.query(':.brand').innerHTML	= brand;
					
					eMain.query(':.price_selling').innerHTML	= 'R$ '+ eA.query(':.hidden .price_selling').innerHTML;
					eMain.query(':.title').innerHTML	= eA.query(':.hidden .title').innerHTML;
					eMain.query(':.title').id			= eA.id.substring(4);
					eMain.query(':.ref-explorer-path').innerHTML	= eA.query(':.hidden .path').innerHTML.replace('catalogo/', '');
					
					Modal.hide();
					sortables.onDragComplete();
				}
			},
			url: ASP.padmin +'explorer/catalogo/'+ path +'?headbar=0&locationbar=1&toolbar=0'
		});
	}
	jCube('::#eSortablesC > div > .imgC').addEvent('onclick', window.SearchOfferCard);
});

//END