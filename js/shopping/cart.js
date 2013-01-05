jCube.Include('Element.getOffset');
jCube.Include('Number.format');
jCube.Include('Transition.fadeIn');
jCube.Include('Transition.fadeOut');
jCube.Include('Transition.resizeTo');

var Cart	= {
	SaveQty: function( eInput){
		if ( eInput) {
			
		}
		var eMain		= eInput.getParent('.cart-items');
		var idProduct	= eMain.query(':.id').innerHTML;
		var qty			= eInput.value.toInteger();
		
		var req	= new jCube.Server.HttpRequest({
			url: '?action=definir-quantidade&format=JSON&produto='+ idProduct +'&quantidade='+ qty,
			noGrowl: true,
			onComplete: function() {
				
			}
		});
		GT8.Spinner.request(req);
		
		var unit	= parseFloat(eMain.query(':.unit').innerHTML.replace('.', '').replace(',', '.').replace('R$ ', ''));
		//sum subtotal
		eMain.query(':.subtotal').innerHTML	= 'R$ '+ (unit*qty).format(2);
		
		Cart.SumTotal();
	},
	SumTotal: function(){
		var total	= 0;
		jCube('::.subtotal').each(function(){
			total	+= parseFloat(this.innerHTML.replace('.', '').replace(',', '.').replace('R$ ', ''));
		});
		jCube(':#eTotal').setHTML( 'R$ '+ total.format(2));
	}
}
jCube(function(){//QTY update
	jCube('::label.increaser input').addEvent('onchange', function(E){
		Cart.SaveQty(this);
	});
});
jCube(function(){//REMOVE cart item
	
	jCube('::.bt-close').addEvent('onclick', function(E){
		this.getParent().query(':.e-bts-hrefs').addClass('show');
	});
	jCube('::.cart-items .bt-close-cancel').addEvent('onclick', function(E){
		E.stop();
		
		this.getParent('.e-bts-hrefs').removeClass('show');
	});
	jCube('::.cart-items .bt-close-confirm').addEvent('onclick', function(E){
		E.stop();
		
		var idProduct	= this.getParent('.cart-items').query(':.id').innerHTML;
		
		var eMain		= this.getParent('.cart-items');
		var eBtClose	= this;
		var req	= new jCube.Server.HttpRequest({
			
			url: '?action=remover-produto&produto='+ idProduct +'&format=JSON',
			onComplete: function(){
				if ( this.ret.affected || 1) {
					eMain.resizeTo({
						height: 1,
						onComplete: function(){
							var bounds	= eBtClose.getOffset(document.body);
							eMain.remove();
							GT8.poof( bounds.left, bounds.top);
							Cart.SumTotal();
						}
					});
				}
			}
		});
		GT8.Spinner.request(req);
	});
	
});
jCube(function(){//DELIVERY: CARDS OVER
	jCube('::.address-holder a.card').addEvent('onmouseover', function(e){
		this.addClass('not-me');
		jCube('::.address-holder a.card:not(.not-me)').fadeOut({
			opacity: 0.2
		});
	}).addEvent('onmouseout', function(e){
		this.removeClass('not-me');
		jCube('::.address-holder a.card:not(.not-me)').fadeIn();
	});
});
