jCube.Include('Element.getOffset');
jCube.Include('Number.format');
jCube.Include('Pluggins.Increaser');
jCube.Include('Transition.resizeTo');

jCube(function(){
	function CheckCart() {
		if ( jCube(':.cart .items .product .remove-item') ) {
			
		} else {
			
		}
	}
	//REMOVE CART ITEM
	(function(){
		jCube('::.cart .items .product .remove-item a').addEvent('onclick', function(e){
			function Update( value) {
				window.setTimeout(function(){ 
						if ( value) {
							jCube('::.cart .items .qty input[name=qty]')[0].trigger('onChange');
							req.crrRow.getParent('.id-container').remove();
						} else {
							req.crrRow.getParent('.id-container').setStyle({
								height: 'auto',
								display: ''
							});
						}
						if ( !jCube('::.cart .items .product .remove-item a').length ) {
							jCube(':.empty-cart').style.display	= 'block';
							jCube(':.cart').style.display		= 'none';
						}
				}, 1000);
			}
			var crrRow	= this;
			var href	= this.getAttribute('href') +'&format=JSON&nocache='+ (new Date().getTime());
			var req	= new jCube.Server.HttpRequest({
				url: href,
				onLoad: function() {
					var ret	= GT8.onGeneralRequestLoad.call( this);
					Update( ret.affected);
				},
				onError: Update
			});
			req.crrRow	= crrRow;
			req.start();
			crrRow.getParent('.id-container').resizeTo( null, 2, 800, null, function() {
				var pos	= crrRow.getOffset( document.body);
				GT8.poof( pos.left, pos.top);
				this.style.display	= 'none';
			});
			e.stop();
			return false;
		});
	})();
	//UPDATE CART QTY
	(function(){
		function Update() {
			var total	= 0;
			var qty		= 0;
			var crrQty, crrPrice;
			jCube('::.cart .items .qty input[name=qty]').each(function(){
				crrQty	= Number(this.value);
				crrPrice	= Number(this.getParent('.id-container').query(':.price').title);
				this.getParent('.id-container').query(':.total').innerHTML	= 'R$ '+ (crrQty*crrPrice).format(2);
				
				total		+= crrQty*crrPrice;
				qty			+= crrQty;
			});
			jCube(':#eTotalQty').innerHTML	= qty;
			//coupon
			var couponValue	= Number(jCube(':#eCoupon').getParent().title);
			total	-= couponValue;
			jCube(':#eTotalPrice').innerHTML	= 'R$ '+ total.format(2);
		}
		jCube('::.cart .items .qty input[name=qty]').each(function(){
			new jCube.Pluggins.Increaser({
				obj: this,
				min: 1,
				max: 150,
				value: this.value,
				img: '../imgs/cart/increaser.png',
				step: 1,
				onChange: function() {
					var req	= new jCube.Server.HttpRequest({
						url: '?action=setCartItem&value='+ this.value +'&format=JSON&idProduct='+ this.getParent('.id-container').id.substring(8) +'&nocache='+ (new Date().getTime()),
						noCache: true,
						onLoad: function() {
							GT8.onGeneralRequestLoad.call( this, this.responseText);
						}
					});
					req.start();
					Update();
				},
				onError: function() {
					Update();
				}
			});
		});
	})();
	//COUPON
	(function(){
		function Update( value) {
			jCube(':#eCoupon').getParent().title	= value;
			jCube(':#eCupomValue').innerHTML	= 'R$ '+ value.format(2);
			jCube('::.cart .items .qty input[name=qty]')[0].trigger('onChange');
		}
		jCube(':#eCoupon').addEvent('onchange', function(e){
			var sp	= GT8.Spinner.show({
				label: 'Validando cupom...'
			});
			var req	= new jCube.Server.HttpRequest({
				url: '?action=getCouponValue&value='+ this.value +'&format=JSON&nocache='+ (new Date().getTime()),
				noCache: true,
				onLoad: function() {
					var ret	= GT8.onGeneralRequestLoad.call( this, {prefix:'Valor do cupom: ', spinner:false});
					sp.obj.query(':span').innerHTML = 'Valor do cupom: '+ ret.value;
					Update( ret.value);
					window.setTimeout(function(){ sp.hide(); }, 3000);
				},
				onError: function() {
					sp.hide();
					Update();
				}
			});
			req.start();
		}).addEvent('onkeyup', function(e){
			if ( e.key==13) {
				this.trigger('onchange');
			}
		});
	})();
});
