jCube(function(){
	
	if ( ASP.isModal) {
		
	}
	(function(){//reseting fields
		//login
		jCube(':label input[name=login]').value	= '';
		jCube(':label input[name=login]').getNextSibling().removeClass('hidden');
		//name
		jCube(':label input[name=name]').value	= '';
		jCube(':label input[name=name]').getNextSibling().removeClass('hidden');
	})();
	(function(){//input.login
		var validLogins	= [];
		var invalidLogins	= [];
		var invalidLoginsMessages	= [];
		var eLabel	= jCube(':label input[name=login]').getParent('label');
		eLabel.messageDefault	= 'Use somente letras, números, ponto, hífen e underline';
		eLabel.messageOk		= '';
		function KeyEvent(E) {
			
			if ( eLabel.className.contains('spinning')) {
				eLabel.query(':em').setHTML('Validando...');
			} else if ( validLogins.contains(this.value) ) {
				eLabel.addClass('positive').removeClass('waiting');
				eLabel.query(':em').setHTML('');
			} else if ( invalidLogins.contains(this.value) ) {
				eLabel.removeClass('positive').addClass('error');
				eLabel.query(':em').setHTML(invalidLoginsMessages[this.value]);
			} else {
				eLabel.removeClass('positive').addClass('waiting');
			}
			
			if ( E.key == 13) {
				E.stop();
				this.trigger('onblur', E);
			}
		}
		var req, valueChecking='';
		jCube(':label input[name=login]').
			addEvent('onkeyup', KeyEvent).
			addEvent('onkeydown', KeyEvent).
			addEvent('onblur', function(E){
				var login	= this.value;
				var eLabel	= this.getParent('label');
				var eLogin	= this;
				
				if ( this.value == '' ) {
					//as funções em js/gt8/Editor.js já cuidam desta situação
				} else if ( validLogins.contains(this.value) ) {
					eLabel.addClass('positive').removeClass('waiting');
					eLabel.query(':em').setHTML('');
				} else if ( invalidLogins.contains(this.value) ) {
					eLabel.removeClass('positive').addClass('error');
					eLabel.query(':em').setHTML(invalidLoginsMessages[this.value]);
				} else if ( valueChecking == this.value ) {
					//ignore
					eLabel.query(':em').setHTML('Validando...');
				} else {
					if ( req) {
						req.abort();
					}
					req	= new jCube.Server.HttpRequest({
						url: '?action=check-login&login='+ escape(login),
						noCache: true,
						onComplete: function(){
							if ( this.ret.affected) {
								validLogins.push(login);
							} else {
								invalidLogins.push(login);
								invalidLoginsMessages[login]	= this.ret.error || 'Login inválido';
							}
							eLogin.trigger('onkeyup', E);
						}
					});
					valueChecking	= login;
					GT8.Spinner.request( req, this.getParent('label'));
					eLabel.query(':em').setHTML('Validando...');
					E.stop();
				}
			})
		;
	})();
	(function(){//button OK: submiting
		var all	= jCube('::label input, label select');
		var eBt	= jCube(':#eModalBtC .href-button-ok');
		all.each(function(){
			this.eParentLabel	= this.getParent('label');
		});
		all.addEvent('onkeyup', function(E){
			//E.stop();
			var validated	= true;
			for ( var i=0; i<all.length; i++) {
				if ( !all[i].eParentLabel.className.contains('positive')) {
					validated	= false;
					break;
				}
			}
			if ( validated) {
				if ( !eBt.validated) {
					eBt.removeClass('href-button-disabled');
				}
				if ( E.key==13 && this!=jCube(':label input[name=login]')) {
					Editor.createNew();
					eBt.addClass('focused');
					window.setTimeout(function(){ eBt.removeClass('focused'); }, 250);
				}
			} else {
				if ( eBt.validated) {
					eBt.addClass('href-button-disabled');
				}
			}
			eBt.validated	= validated;
		});
	})();
	(function(){//after submit
		Editor.enabled	= true;
		Editor.createNew.onLoad	= function( response, id) {
			if ( this.ret.error) {
				
			} else {
				window.setTimeout(function(){
					window.location	= (window.location+'').replace('new-user-account', jCube(':label input[name=login]').value);
				}, 2000);
			}
		}
	})();
	
	window.setTimeout(function(){ jCube(':label input[name=login]').focus()}, 250);
});
