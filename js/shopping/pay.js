jCube.Include('Element.setFixedMask');
jCube.Include('Element.setFocus');
jCube.Include('Transition.fadeIn');
jCube.Include('Transition.fadeOut');

jCube(function(){
	
	jCube('::input[name=card-number]').setFixedMask('####.####.####.####');//MASK in card number
	jCube('::input[name=security-code]').setFixedMask('###');//MASK in card security code
	
	var payMethod	= null;
	(function(){//PAY: PAY CHOOSER OVER
		var eChoosed	= null;
		function CheckBtPay() {
			
			if ( payMethod == 'boleto') {
				jCube(':#eBodyCreditCard').addClass('hidden');
				jCube(':#eBodyBoleto').setStyle('opacity', 0).removeClass('hidden').fadeIn();
			} else if ( jCube(':#eBodyCreditCard').className.contains('hidden')) {
				jCube(':#eBodyBoleto').addClass('hidden');
				jCube(':#eBodyCreditCard').setStyle('opacity', 0).removeClass('hidden').fadeIn();
				jCube(':input[name=card-name]').setFocus();
			}
		}
		jCube('::#ePayMethodChooser .chooser .item').addEvent('onmouseover', function(e){
			this.addClass('not-me');
			jCube('::#ePayMethodChooser .chooser .item:not(.not-me)').fadeOut({
				opacity: 0.2
			});
			if ( this.className.contains('no-select')) {
				this.fadeIn();
			}
			
		}).addEvent('onmouseout', function(e){
			this.removeClass('not-me');
			jCube('::#ePayMethodChooser .chooser .item:not(.not-me):not(.no-select)').fadeIn();
			
			if ( this.className.contains('no-select')) {
				this.fadeOut({
					opacity: 0.2
				});
			}
			
		}).addEvent('onclick', function(E){
			jCube('::#ePayMethodChooser .chooser .item').removeClass('selected').addClass('no-select');
			this.addClass('selected').removeClass('no-select');
			eChoosed	= this;
			
			if ( this.className.contains('boleto')) {
				payMethod	= 'boleto';
			} else {
				payMethod	= 'card';
				
				
				if ( this.className.contains('diners-card')) {
					jCube(':input[name=card-type]').value	= 'diners';
				} else if ( this.className.contains('hiper-card')) {
					jCube(':input[name=card-type]').value	= 'hipercard';
				} else if ( this.className.contains('master-card')) {
					jCube(':input[name=card-type]').value	= 'mastercard';
				} else if ( this.className.contains('visa-card')) {
					jCube(':input[name=card-type]').value	= 'visa';
				} else if ( this.className.contains('jcb-card')) {
					jCube(':input[name=card-type]').value	= 'jcb';
				} else if ( this.className.contains('sorocred-card')) {
					jCube(':input[name=card-type]').value	= 'sorocred';
				} else if ( this.className.contains('aura-card')) {
					jCube(':input[name=card-type]').value	= 'aura';
				}
				
			}
			CheckBtPay();
		});
		
		
		jCube('::#eBodyCreditCard input, #eBodyCreditCard select').addEvent('onchange', function(E){
			if ( 1) {
				
			}
		});
		//
	})();
	(function(){//PAY WITH CREDIT CARD
		window.OnBeforeSave	= function(){
			if ( payMethod != 'boleto' ) {
				if ( !jCube(':input[name=card-name]').value) {
					alert('Por favor, informe o nome impresso no cartão.');
					jCube(':input[name=card-name]').setFocus();
					
					return false;
				} else if ( !jCube(':input[name=card-number]').value) {
					alert('Por favor, informe o número do cartão.');
					jCube(':input[name=card-number]').setFocus();
					
					return false;
				} else if ( !jCube(':input[name=security-code]').value) {
					alert('Por favor, informe o código de segurança, de 3 dígitos, do cartão.');
					jCube(':input[name=security-code]').setFocus();
					
					return false;
				} else if ( !jCube(':select[name=expire-month]').selectedIndex) {
					alert('Por favor, informe o mês de vencimento do cartão.');
					jCube(':select[name=expire-month]').setFocus();
					
					return false;
				} else if ( !jCube(':select[name=expire-year]').selectedIndex) {
					alert('Por favor, informe o ano de vencimento do cartão.');
					jCube(':select[name=expire-year]').setFocus();
					
					return false;
				}
			}
			
			return true;
		};
	})();
});
