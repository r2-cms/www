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
		be	= new jCube.Pluggins.BackgroundEffect({
			container: this,
			wait: 15000,
			onComplete: function() {
				jCube('::.slider-steps a').setStyle('opacity', 1);
			},
			onTimeEllapsing: function(t) {
				jCube(':.slider-steps a.active').setStyle('opacity', t/this.wait)
			}
		}).start();
		
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
});