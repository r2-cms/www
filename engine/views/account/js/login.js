GT8.analytics.url	= '/Admin/login/';
jCube.Include("Document.Cookie");
jCube.Include("Element.addClass");
jCube.Include("Element.getElementsBySelector");
jCube.Include("Element.getParent");
jCube.Include("Element.removeClass");
jCube.Include("Element.setClass");
jCube.Include("Element.setFocus");
jCube.Include("Element.setHTML");
jCube.Include("Element.setStyle");
jCube.Include("Element.setValue");
jCube.Include("Event.add");
jCube.Include("Event.trigger");
jCube.Include("Server.HttpRequest");
jCube.Include("String.contains");
jCube.Include("String.md5");
jCube.Include("Transition.fadeIn");
jCube.Include("Transition.fadeOut");
jCube.Include("Window");

jCube(function() {//access account
	jCube(':input[name=name]').setFocus();
	jCube('::input[name=name], input[name=password]').addEvent('keyup', function(e){
		if ( e.key == 13) {
			jCube(':a.submit').trigger('click');
		}
	});
	jCube('::input[name=name], input[name=password]').addEvent('blur', Validate);
	jCube(':a.submit').addEvent('click', function(E) {
		E.stop();
		
		if ( Validate('all')) {
			if ( !submitting) {
				GT8.Spinner.request(req, null, jCube(':.login-info-feedback'), jCube(':.login-spinner-feedback'));
			}
		} else {
			
		}
		
		return false;
	});
	function Validate(e) {
		var
			obj	= null,
			message	= null,
			invalid	= true
		;
		
		//login
		if ( e == 'all' || this.getParent().className.contains('name')) {
			if ( jCube(':input[name=name]').value.length < 2 ) {
				jCube(':input[name=name]').getParent('label').addClass('error');
				obj	= obj || jCube(':input[name=name]');
				invalid	= false;
			} else {
				jCube(':input[name=name]').getParent('label').removeClass('error').removeClass('positive').addClass('positive');
			}
		}
		//password
		if ( e == 'all' || this.getParent().className.contains('pass')) {
			if ( jCube(':input[name=password]').value.length < 2 ) {
				jCube(':input[name=password]').getParent('label').addClass('error');
				obj	= obj || jCube(':input[name=password]');
				invalid	= false;
			} else {
				jCube(':input[name=password]').getParent('label').removeClass('error').removeClass('positive').addClass('positive');
			}
		}
		if ( obj) {
			obj.setFocus();
			if ( obj.getParent('label')) {
				obj.getParent('label').addClass('trans-pulsate-left');
				window.setTimeout(function(){ obj.getParent('label').addClass('trans-pulsate-left'); }, 100);
			}
		}
		
		return invalid;
	}
	var cubCounter	= 0;
	var submitting	= false;
	var req	= new jCube.Server.HttpRequest({
		url: '?format=JSON&action=login',
		noCache: true,
		method: jCube.Server.HttpRequest.HTTP_POST,
		onStart: function() {
			var
				name	= jCube(':input[name=name]').value,
				pass	= jCube(':input[name=password]').value
				keepLogged	= jCube(':label.keepLogged input')? jCube(':label.keepLogged input').checked: false
			;
			submitting	= true;
			
			this.url	= '?format=JSON&action=login';
			this.content	= '';
			this.addGet('user', name);
			this.addGet('keepLogged', keepLogged);
			this.addGet('format', 'JSON');
			this.addPost('pass', (pass+ASP.tkn).md5().concat(ASP.sstart).md5());
			
			jCube('::.login-spinner-feedback').fadeIn();
			jCube('::.login-info-feedback').setHTML('Conferindo suas credenciais. Aguarde...');
			jCube('::.login-info-feedback').removeClass('error');
			
			cubCounter++;
		},
		onError: function() {
			jCube('::.login-spinner-feedback').fadeOut();
			jCube('::.login-info-feedback').addClass('error').setHTML('Erro ao acessar o sistema');
			submitting	= false;
		},
		onLoad: function(){
			var code	= Number(this.responseText.split('####')[0]);
			var ip		= this.responseText.split('####')[1];
			var dt		= this.responseText.split('####')[2];
			var agent	= this.responseText.split('####')[3];
			var ip2		= this.responseText.split('####')[4];
			var agent2	= this.responseText.split('####')[5];
			
			jCube('::.login-spinner-feedback').fadeOut();
			jCube('::.login-info-feedback').addClass('error');
			
			submitting	= false;
			
			if ( this.responseText == '') {
				window.location.reload();
			} else if ( this.ret.affected ) {
				jCube('::.login-info-feedback').setHTML('Acesso garantido');
				jCube('::.login-info-feedback').removeClass('error');
				window.setTimeout( function(){ window.location.reload();}, ((ip!=ip2||agent!=agent2)?500: 500));
			} else {
				
			}
			if ( cubCounter > 4) {
				window.location	= '{{CROOT}}{{GT8:account.forgotPassword.root}}?login='+ jCube(':label.name input').value;
			}
		}
	});
	req.label	= 'Consultando...';
	window.setTimeout( function(){jCube(':input[name=name]').focus()}, 250);
	window.setTimeout( function(){jCube(':input[name=name]').focus()}, 500);
	jCube('::input').setValue('');
});
