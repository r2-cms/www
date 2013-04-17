jCube.Include('Element.addClass');
jCube.Include('Element.setHTML');
jCube.Include('Element.getOptionValue');
jCube.Include('Element.removeClass');
jCube.Include('Element.setFocus');
jCube.Include('Element.setSelection');
jCube.Include('Element.setValue');
jCube.Include('Event.add');
jCube.Include('Event.trigger');
jCube.Include('String.md5');
jCube(function(){
	
	jCube(':select[name=natureza]').addEvent('onchange', function(E){//NATUREZA
		if ( this.getOptionValue() == 'J') {
			jCube(':#eCPFCNPJ strong').setHTML('CNPJ');
			jCube(':#eCPFCNPJ input').setFixedMask('##.###.###/####-##');
			jCube(':#eDocument strong').setHTML('Inscrição');
		} else {
			jCube(':#eCPFCNPJ strong').setHTML('CPF');
			jCube(':#eCPFCNPJ input').setFixedMask('###.###.###-##');
			jCube(':#eDocument strong').setHTML('RG');
		}
	}).trigger('onchange');
	jCube(':input[name=zip]').addEvent('onchange', function(E){//ZIP
		GT8.Spinner.request(new jCube.Server.HttpRequest({
			url: '?action=get-zip&zip='+ this.value,
			noCache: false,
			label: 'Buscando endereço...',
			message: 'Concluído',
			onComplete: function(){
				var Zip	= GT8.meow(this.responseText);
				jCube(':input[name=street]').value		= Zip.logradouro;
				jCube(':input[name=district]').value	= Zip.bairro;
				jCube(':input[name=city]').value		= Zip.cidade;
				jCube(':select[name=stt]').setValue(Zip.estado).trigger('onchange');
			},
			hideAfter: 1
		}))
	});
	jCube(':input[name=mail]').addEvent('onchange', function(E){//MAIL
		GT8.Spinner.request(new jCube.Server.HttpRequest({
			url: '?action=check-mail&mail='+ this.value,
			noCache: false,
			label: 'Checando e-mail...',
			onComplete: function(){
				if ( this.responseText.toLowerCase().contains('e-mail já cadastrado')) {
					jCube(':#eMailMessage').removeClass('hidden');
				} else {
					jCube(':#eMailMessage').addClass('hidden');
				}
			},
			hideAfter: 1
		}))
	});
	window.OnBeforeSave	= function() {
		var eInput, message;
		
		if ( jCube(':input[name=name]').value == '' ) {
			message	= 'Por favor, escreva seu nome completo';
			eInput	= jCube(':input[name=name]');
			
		} else if ( jCube(':select[name=natureza]').value == 'F' && !jCube(':input[name=cpfcnpj]').value ) {
			message	= 'Por favor, informe seu CPF';
			eInput	= jCube(':input[name=cpfcnpj]');
			
		} else if ( jCube(':select[name=natureza]').value == 'J' && !jCube(':input[name=cpfcnpj]').value ) {
			message	= 'Por favor, informe seu CNPJ';
			eInput	= jCube(':input[name=cpfcnpj]');
			
		} else if ( jCube(':input[name=mail]').value == '' ) {
			message	= 'Por favor, informe seu e-mail';
			eInput	= jCube(':input[name=mail]');
			
		} else if ( jCube(':input[name=phone-home]').value == '' ) {
			message	= 'Por favor, informe seu número de telefone residencial';
			eInput	= jCube(':input[name=phone-home]');
			
		} else if ( jCube(':input[name=phone-mobile]').value == '' ) {
			message	= 'Por favor, informe seu número de telefone celular';
			eInput	= jCube(':input[name=phone-mobile]');
			
		} else if ( jCube(':input[name=street]').value == '' ) {
			message	= 'Por favor, informe o nome da sua rua/avenida';
			eInput	= jCube(':input[name=street]');
			
		} else if ( jCube(':input[name=number]').value == '' ) {
			message	= 'Por favor, informe o número da sua residência';
			eInput	= jCube(':input[name=number]');
			
		} else if ( jCube(':input[name=district]').value == '' ) {
			message	= 'Por favor, informe o nome de seu bairro';
			eInput	= jCube(':input[name=district]');
			
		} else if ( jCube(':input[name=city]').value == '' ) {
			message	= 'Por favor, informe o nome da sua cidade';
			eInput	= jCube(':input[name=city]');
			
		} else if ( jCube(':select[name=stt]').selectedIndex == 0 ) {
			message	= 'Por favor, informe o seu estado';
			eInput	= jCube(':select[name=stt]');
			
		} else if ( jCube(':input[name=pass1]').value == '' ) {
			message	= 'Por favor, digite uma senha';
			eInput	= jCube(':input[name=pass1]');
			
		} else if ( jCube(':input[name=pass1]').value === jCube(':input[name=pass2]').value) {
			jCube(':#ePass').value	= (jCube(':input[name=pass1]').value+ASP.tkn).md5();
			return true;
		
		} else {
			message	= 'As senhas digitadas não conferem!';
			eInput	= jCube(':input[name=pass1]');
			
		}
		
		GT8.Spinner.show({
			label: message,
			hideImage: true,
			type: 'error',
			position: 'center',
			hideAfter: 5000
		});
		eInput.setFocus().setSelection();
		
		return false;
	}
});

