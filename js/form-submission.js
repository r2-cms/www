jCube.Include("Document.Cookie");
jCube.Include("Element.addClass");
jCube.Include("Element.getElementsBySelector");
jCube.Include("Element.getOptionValue");
jCube.Include("Element.getParent");
jCube.Include("Element.removeClass");
jCube.Include("Event.add");
jCube.Include("Server.HttpRequest");
jCube.Include("String.contains");

jCube(function(){
	
	jCube("::.gt8-form").each(function(){
		if ( jCube.Document.Cookie.get("mail-sent-"+ this.id)) {
			this.query("::.gt8-before-post").fadeOut({
				duration: 3000,
				opacity: 0.3
			});
			this.query("::.submit").setStyle("visibility", "hidden");
			
			this.query("::.gt8-posted").setStyle({
				visibility: 'visible',
				display: 'block'
			});
		}
	});
	
	function Send( e) {
		var btSub		= this;
		var eForm		= this.getParent('.gt8-form');
		var eSpinner	= this.query(':img.spinner');
		
		var req	= new jCube.Server.HttpRequest({
			url: '?opt=updt',
			method: jCube.Server.HttpRequest.HTTP_POST,
			noCache: true,
			onComplete: function() {
				eSpinner.addClass('hidden');
				
				if ( this.responseText.contains('//#affected rows') ) {
					GT8.Spinner.show({label:'<br />Mensagem enviada com sucesso! <br />Obrigado. Em breve entraremos em contato.<br /><br />', hideAfter:5000, hideImage:true});
					
					eForm.query("::.gt8-before-post").fadeOut({
						duration: 3000,
						opacity: 0.3
					});
					eForm.query("::.submit").setStyle("visibility", "hidden");
					
					eForm.query("::.gt8-posted").setStyle({
						opacity: 0,
						visibility: 'visible',
						display: 'block'
					}).fadeIn({
						duration: 3000,
						delay: 2000
					});
					if ( eForm.query(":input[name=gt8-timeout]")) {
						jCube.Document.Cookie.set("mail-sent-"+ eForm.id, new Date().getTime(), Number(eForm.query(":input[name=gt8-timeout]").value));
					}
					
					eForm.trigger('onPostSuccessfully', this);
				} else {
					this.onError();
				}
			},
			onError: function() {
				eSpinner.addClass('hidden');
				GT8.Spinner.show({label:'<br />Ops! <br />Falha no envio da mensagem. Por favor, tente mais tarde.<br /><br />', hideAfter:5000, hideImage:true});
			}
		});
		var fields	= [];
		var value;
		var message;
		var eForms	= this.getParent('.gt8-form').query('::.gt8-update, .gt8-post');
		for ( var i=0, crr; i<eForms.length; i++) {
			crr	= eForms[i];
			
			if ( crr.name) {
				value	= null;
				if ( crr.type == 'checkbox') {
					value	= crr.checked? '1': '0';
				} else if ( crr.nodeName == 'SELECT' ) {
					value	= crr.getOptionValue();
				} else {
					value	= crr.value;
				}
				
				if ( crr.className.contains('gt8-post')) {
					req.addPost( crr.name, value);
				} else {
					req.addGet(crr.name, value);
				}
				
				if ( value ) {
					fields.push(crr.name);
				} else {
					if ( crr.className.contains('gt8-required')) {
						var title	= crr.title;
						var crrObj	= crr;
						while( !title && crrObj) {
							crrObj	= crrObj.parentNode;
							title	= crrObj.title;
						}
						
						GT8.Spinner.show({label:'O campo "'+ title +'" é obrigatório', hideAfter:5000, hideImage:true});
						crr.setFocus().setSelection();
						e.stop();
						return false;
					}
				}
			}
		}
		eSpinner.removeClass('hidden');
		
		req.send();
		e.stop();
		
		return false;
	}
	jCube('::a.submit').addEvent('onclick', Send);
	
});
