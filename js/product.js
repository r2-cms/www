jCube.Include('Element.addClass');
jCube.Include('Element.removeClass');
jCube.Include('Element.setValue');
jCube.Include('Math.between');
jCube.Include('Math.getLinearEquation');
jCube.Include('Pluggins.BackgroundEffect');
jCube.Include('Pluggins.TabbedPane');
jCube.Include('Transition.fadeIn');
jCube.Include('Transition.fadeOut');
jCube.Include('Transition.moveTo');
jCube(function(){
	var be;
	
	jCube("::.TabbedPane").each(function(){//CARDTAB
		new jCube.Pluggins.TabbedPane({
			container: this
		})
	});
	jCube('::.background-effect').each(function(){//BACKGROUND-EFFECT
		var eIndicator	= jCube(':.slider-steps .bar-indicator');
		be	= new jCube.Pluggins.BackgroundEffect({
			container: this,
			wait: 15000,
			onComplete: function() {
				eIndicator.setStyle('width', '100%');
			},
			onTimeEllapsing: function(t) {
				eIndicator.setStyle('width', 100 - (t/this.wait) * 100 +'%');
			}
		}).start();
		be.getChron().fps	= 20;
	});
	(function(){//MAGNIFIER
		var magActive	= false;
		var holder	= jCube(':#eMagnifierC');
		var eImg1	= jCube('::#eMagnifierC img')[0];
		var eImg2	= jCube('::#eMagnifierC img')[1];
		var eInfo	= jCube(':.banner .info');
		var W		= null;
		var H		= null;
		var w		= null;
		var h		= null;
		var b		= null;
		jCube(':.banner').addEvent('onmouseover', function(E){//set mag vars
			W		= holder.offsetWidth;
			H		= holder.offsetHeight;
			w		= eImg2.offsetWidth;
			h		= eImg2.offsetHeight;
			b		= holder.getOffset(document.body);
		});
		jCube(document.body).addEvent('onmousemove', function(E){//move mag
			
			var x		= E.clientX;
			var y		= E.clientY;
			var destX	= 0;
			var destY	= 0;
			if ( w > W) {
				destX	= (-Math.getLinearEquation(0, 0, W, w-W, Math.between(0, x-b.left, holder.offsetWidth)));
				//eImg.style.left	= destX +'px';
			}
			if ( h > H) {
				destY	= (-Math.getLinearEquation(0, 0, H, h-H, Math.between(0, y-b.top, holder.offsetHeight)));
				//eImg.style.top	= destY +'px';
			}
			eImg1.setStyle({
				left: destX,
				top: destY
			});
			eImg2.setStyle({
				left: destX,
				top: destY
			});
		});
		jCube(':.background-effect .images-step').addEvent('onclick', function(E){//mag activation
			holder.setStyle({
				opacity: 0,
				zIndex: 2
			}).fadeIn(850).addClass('out');
			
			be.pause();
			var eCrrImg	= jCube(':.background-effect .images-step').getLastChild().getPreviousSibling();
			eImg1.src	= eCrrImg.src;
			eImg2.src	= eCrrImg.src.replace('?preview', '').replace('&preview', '');
			
			window.setTimeout(function(){
				//eImg.src	= eCrrImg.src.replace('?preview', '');
				be.pause();
			}, 750);
			
			magActive	= true;
		});
		document.body.addEvent('onclick', function(E){//mag deactivation
			if ( magActive) {
				holder.fadeOut({
					duration: 850,
					onComplete: function(){
						this.setStyle({
							zIndex: -1
						}).removeClass('out');
						eImg1.src	= jCube.root = '../imgs/gt8/blank.gif';
						eImg2.src	= jCube.root = '../imgs/gt8/blank.gif';
						be.resume();
					}
				});
				magActive	= false;
			}
		});
	})();
	(function(){//SIZE SELECT
		jCube('::.sizes-chooser > a').addEvent('onclick', function(E){
			E.stop();
			
			jCube('::.sizes-chooser > a').removeClass('selected');
			this.addClass('selected');
			
			if ( jCube(':.bt-buy-overlay')) {
				jCube(':.bt-buy-overlay').trigger('onclick');
			}
		});
	})();
	(function(E){//BUY BT and OVERLAY
		var eOverlay	= jCube(document.createElement('DIV')).
			addClass('pos-overlay bt-buy-overlay').
			addEvent('onclick', function(){
				this.fadeOut({
					onComplete: function(){
						this.remove();
						jCube(':.sizes-chooser-holder').setStyle('z-index', 1).removeClass('highlighted');
					}
				}
			)
		});
		jCube(':a#eBtBuy').addEvent('onclick', function(E){
			if ( jCube(':.sizes-chooser .selected')) {
				this.href	= jCube(':.sizes-chooser .selected').href;
			} else {
				//create overlay
				E.stop();
				
				jCube(':.sizes-chooser-holder').setStyle({
					zIndex: 2000
				}).addClass('highlighted');
				eOverlay.appendTo(document.body).setStyle('opacity', 0).fadeIn(850);
			}
		});
	})();
	(function(){//SHOES GRID
		if ( !jCube('::.sizes-chooser a').length) {
			return;
		}
		var crrSize	= 33;
		var eSizes	= null;
		var greatest	= [];
		for ( var i=0, j=0, crr, eA, found, sizes=[33,34,35,36,37,38,39]; i<sizes.length; i++) {
			crr		= sizes[i];
			found	= false;
			greatest	= [null, 0];
			eSizes	= jCube('::.sizes-chooser a');
			for( j=0; j<eSizes.length; j++) {
				
				if ( eSizes[j].innerHTML == crr) {
					found	= true;
					break;
				}
				if ( eSizes[j].innerHTML.toInteger() > greatest[1] && eSizes[j].innerHTML.toInteger() < crr) {
					greatest	= [ eSizes[j], eSizes[j].innerHTML.toInteger()];
				}
			}
			if ( !found ) {
				eA	= jCube(document.createElement('A')).
					addClass('unavaiable text-stroke').
					setProperty('title', 'Estoque esgotado!').
					addEvent('onclick', function(){ return null; }).
					setHTML(crr+'').
					setProperty('href', '#')
				;
				if ( greatest[1] ) {
					eA.injectAfter( greatest[0]);
				} else {
					eA.prependTo( eSizes[0].parentNode);
				}
			}
		}
	})();
	(function(){//Stock avaibility
		if ( jCube('::.sizes-chooser a:not(.unavaiable)').length > 2) {
			jCube(':#eStockInfo').addClass('hidden');
		} else {
			jCube(':#eStockInfo').setHTML('(Últimas unidades)');
		}
	})();
	(function(){//ATTRIBUTES
		jCube('::.card .attributes .attr-name').each(function(){
			if ( this.innerHTML.toLowerCase() == 'altura do salto' ) {
				var eL	= this.getParent().query(':.attr-value');
				eL.innerHTML	= (eL.innerHTML.trim().toInteger()/10).round(2).toString().replace('.', ',') + ' cm'
			}
		});
	})();
});