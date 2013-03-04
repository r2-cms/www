GT8.analytics.url	= '/Admin/login/';
jCube.Include("Document.Cookie");
jCube.Include("Element.addClass");
jCube.Include("Element.getElementsBySelector");
jCube.Include("Element.getParent");
jCube.Include("Element.removeClass");
jCube.Include("Element.setClass");
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

jCube(function() {
	//jCube(':#eLoginWindow').setStyle({
	//	top: window.getHeight()/2 - jCube(':#eLoginWindow').offsetHeight/2
	//});
	jCube('::input').addEvent('keyup', function(e){
		if ( e.key == 13) {
			jCube(':a.submit').trigger('click');
		}
	});
	jCube('::input').addEvent('blur', Validate);
	jCube(':a.submit').addEvent('click', function(E) {
		E.stop();
		
		if ( Validate('all')) {
			if ( !submitting) {
				GT8.Spinner.request(req, null, jCube(':h3'), jCube(':.infos img.spinner'));
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
			if ( jCube(':label.name input').value.length < 2 ) {
				jCube(':label.name').addClass('error');
				obj	= obj || jCube(':label.name input');
				invalid	= false;
			} else {
				jCube(':label.name').removeClass('error').removeClass('positive').addClass('positive');
			}
		}
		//password
		if ( e == 'all' || this.getParent().className.contains('pass')) {
			if ( jCube(':label.pass input').value.length < 2 ) {
				jCube(':label.pass').addClass('error');
				obj	= obj || jCube(':label.pass input');
				invalid	= false;
			} else {
				jCube(':label.pass').removeClass('error').removeClass('positive').addClass('positive');
			}
		}
		if ( obj) {
			obj.focus();
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
				name	= jCube(':label.name input').value,
				pass	= jCube(':label.pass input').value
				keepLogged	= jCube(':label.keepLogged input').checked
			;
			submitting	= true;
			
			this.url	= '?format=JSON&action=login';
			this.content	= '';
			this.addGet('user', name);
			this.addGet('keepLogged', keepLogged);
			this.addGet('format', 'JSON');
			this.addPost('pass', (pass+'{{GT8:account.token}}').md5().concat('-{{SESSION:GT8.tstart}}').md5());
			
			jCube(':.infos img.spinner').setStyle('display', 'block').setStyle('opacity', 0).fadeIn();
			jCube(':h3').innerHTML	= 'Conferindo suas credenciais. Aguarde...';
			jCube(':h3').setClass('');
			
			cubCounter++;
		},
		onError: function() {
			jCube(':.infos img.spinner').setStyle('display', 'block').fadeOut();
			jCube(':h3').setClass('error').innerHTML	= 'Erro ao acessar o sistema';
			submitting	= false;
		},
		onLoad: function(){
			var code	= Number(this.responseText.split('####')[0]);
			var ip		= this.responseText.split('####')[1];
			var dt		= this.responseText.split('####')[2];
			var agent	= this.responseText.split('####')[3];
			var ip2		= this.responseText.split('####')[4];
			var agent2	= this.responseText.split('####')[5];
			
			jCube(':.infos img.spinner').setStyle('display', 'block').fadeOut();
			jCube(':h3').setClass('error');
			
			submitting	= false;
			
			if ( this.responseText == '') {
				window.location.reload();
			} else if ( this.ret.affected ) {
				jCube(':h3').innerHTML	= 'Acesso garantido.<br />Último acesso:<small><br />Data: '+ dt +'<br /><span style="'+ (ip!=ip2?'color:#F00':'') +'" >IP: '+ ip +'</span><br /><span style="'+ (agent!=agent2?'color:#F00':'') +'" >Agent: '+ agent +'</span></small>';
				jCube(':h3').setClass('');
				window.setTimeout( function(){ window.location.reload();}, ((ip!=ip2||agent!=agent2)?500: 500));
			} else {
				
			}
			if ( cubCounter > 4) {
				window.location	= '../{{GT8:account.forgotPassword.root}}?login='+ jCube(':label.name input').value;
			}
		}
	});
	req.label	= 'Consultando...';
	window.setTimeout( function(){jCube(':label.name input').focus()}, 250);
	window.setTimeout( function(){jCube(':label.name input').focus()}, 500);
	jCube('::input').setValue('');
});
