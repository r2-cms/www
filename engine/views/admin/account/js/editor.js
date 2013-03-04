jCube.Include('Element.addClass');
jCube.Include('Element.appendTo');
jCube.Include('Element.removeClass');
jCube.Include('Element.setFixedMask');
jCube.Include('Element.setFocus');
jCube.Include('Element.setHTML');
jCube.Include('Element.setSelection');
jCube.Include('Element.setValue');
jCube.Include('Event.add');
jCube.Include('Event.trigger');

jCube(function(){
	
	Pager.allowKeyboardNavigation	= false;
	
	(function() {//PASS MODAL
		var eModal	= null;
		jCube(':#eChangePassBt').addEvent('onclick', function(e) {
			e.stop();
			
			eModal	= eModal || jCube(':#eModalPassEdit');
			
			eModal.appendTo( document.body);
			jCube(':#ePassInput').value		= '';
			jCube(':#ePassConfirm').value	= '';
			jCube(':#eModalPassEdit .feedback').setHTML('').removeClass('error');
			
			eModal.showOverlay({
				objRef: this,
				duration: 1000,
				transition: jCube.Transition.DEFAULT,
				transitionIn: [0.3, 1.06, 0.5, 1.19],
				overlay: {
					border : 'none',
					background:'white',
					borderRadius: '7px',
					boxShadow: '0 0 80px 5px #333344',
					opacity: 0.8
				},
				glassPane: {
					transition: true,
					background: 'black',
					opacity: 0.5
				},
				onShowOverlayComplete: function() {
					jCube(':#ePassInput').setFocus();
				}
			});
			
			return false;
		});
		jCube(':#ePassInput').addEvent('onkeydown', function(e) {
			if ( e.key == 13 ) {
				if ( !this.value) {
					jCube(':#eModalPassEdit .feedback').setHTML('Por favor, insira uma senha').addClass('error');
				} else {
					window.setTimeout(function(){ jCube(':#ePassConfirm').setFocus().setSelection(); }, 50);
				}
			}
		});
		jCube(':#ePassConfirm').addEvent('onkeydown', function(e){
			if ( e.key == 13) {
				window.setTimeout(function(){ jCube(':#eModalPassEdit a.href-button-ok').trigger('onclick'); }, 50);
			}
		});
		jCube(':#eModalPassEdit a.href-button-ok').addEvent('onclick', function(e){
			e.stop();
			
			if ( jCube(':#ePassInput').value == '' ) {
				jCube(':#eModalPassEdit .feedback').setHTML('Por favor, insira uma senha').addClass('error');
				return;
			} else if ( jCube(':#ePassConfirm').value != jCube(':#ePassInput').value ) {
				jCube(':#eModalPassEdit .feedback').setHTML('As senhas digitadas não conferem!').addClass('error');
				return;
			}
			//request
			var req	= new jCube.Server.HttpRequest({
				url: '?action=set-pass&format=JSON',
				noCache: true,
				method: jCube.Server.HttpRequest.HTTP_POST,
				onLoad: function() {
					var ret	= GT8.onGeneralRequestLoad.call( this, true);
					jCube(':#eModalPassEdit .feedback').addClass('spinner-small-hidden').setHTML('');
					
					if ( ret.affected) {
						eModal.showOverlay();
					} else if ( ret.message) {
						jCube(':#eModalPassEdit .feedback').addClass('error').addClass('spinner-small-hidden').setHTML(ret.message);
					}
				},
				onError: function() {
					jCube(':#eModalPassEdit .feedback').addClass('spinner-small-hidden').setHTML('Não foi possível definir uma nova senha agora!');
				}
			});
			req.addPost('pass', (jCube(':#ePassInput').value + ASP.token).md5());//not include:.concat(ASP.tstart).md5()
			req.start();
			jCube(':#eModalPassEdit .feedback').removeClass('spinner-small-hidden').removeClass('error').setHTML('Definindo nova senha...');
		});
		jCube(':#eModalPassEdit a.href-button-cancel').addEvent('onclick', function(e) {
			e.stop();
			
			eModal.showOverlay();
		});
	})();
	Editor.deleteContact	= function( obj) {//CONTACT DELETE
		
		if ( confirm('Tem certeza que deseja excluir esta informação de contato?')) {
			var eParent	= jCube(obj).getParent('div');
			
			var id	= (jCube(obj).getParent('div').id+'').substringIndex('-', -1);
			if ( isNaN(Number(id))) {
				alert('Por favor, contate o administrador.\nEsta estrutura foi alterada e não é possível alterar as informações deste contato!');
				return false;
			}
			var eInput	= jCube(document.createElement('INPUT')).addClass('gt8-update').setProperty('name', 'users.contact.delete');
			eInput.setData('Editor::validate::firstTime', true);
			eInput.getParent	= function() {
				return eParent;
			}
			var onComplete	= Editor.onRequestComplete;
			Editor.onRequestComplete	= function( response, ok) {
				if ( response && response.contains('affected rows:')) {
					var bounds	= eParent.getOffset(document.body);
					GT8.poof( bounds.left + eParent.offsetWidth/2 - 15, bounds.top)
					eParent.remove();
					Editor.onRequestComplete	= onComplete;
				}
			}
			Editor.updateField.call( eInput);
		}
		return true;
	};
	Editor.insertContact	= function() {//CONTACT INSERT
		var eTemplate	= jCube(jCube(':#eContactTemplate').cloneNode( true));
		
		var idTemp	= new Date().getTime();
		eTemplate.injectBefore(jCube(':#eContactTemplate')).id = idTemp;
		
		var req	= new jCube.Server.HttpRequest({
			method: jCube.Server.HttpRequest.HTTP_POST,
			noCache: true,
			url: "?opt=users.contact.insert&format=JSON&idUser="+ ASP.id,
			onLoad:	function() {
				if ( this.ret.insertId) {
					var id	= this.ret.insertId;
					eTemplate.id	= 'eContactId-'+ id;
					
					Editor.setEvents('::#eContactId-'+ id +' select.gt8-update, #eContactId-'+ id +' input.gt8-update');
					jCube("::#eContactId-"+ id +" span.e-select select").each(function(){
						this.addEvent('updateValue', function() {
							this.getNextSibling().getFirstChild().innerHTML	= this.getOption().innerHTML;
						});
						this.addEvent('onchange', function(E) {
							this.trigger('updateValue');
							Editor.changeContactMask( this, this.getParent('div'));
						});
						Editor.changeContactMask( this, this.getParent('div'));
					});
					
				} else {
					var bounds	= eTemplate.getOffset(document.body);
					GT8.poof(bounds.left + eTemplate.offsetWidth/2 - 15, bounds.top);
					eTemplate.remove();
				}
			}
		});
		GT8.Spinner.request( req);
		
	}
	Editor.onBeforeUpdate	= function(name, value, obj, req) {//UPDATE
		//CONTACT
		if ( ['contact.value', 'contact.channel', 'contact.type'].contains(name) || name == 'users.contact.delete') {
			if ( name == 'users.contact.delete') {
				req.url	= '?opt=users.contact.delete&format=JSON';
			}
			
			var eParent	= jCube(obj).getParent('div');
			var id	= ((eParent||{}).id+'').substringIndex('-', -1);
			if ( isNaN(Number(id))) {
				alert('Por favor, contate o administrador.\nEsta estrutura foi alterada e não é possível alterar as informações deste contato!');
				return false;
			} else {
				req.addGet('idContact', id);
			}
		}
		return [ name, value];
	};
	(function(){//CONTACTS
		var ChangeMask	= function( eSelect, eParent) {
			var eValue	= eParent.query(':input[name=contact.value]');
			var channel	= eParent.query(':select[name=contact.channel]').getOptionValue();
			var type	= eParent.query(':select[name=contact.type]').getOptionValue();
			
			if ( eValue.unmask) {
				eValue.unmask();
			}
			if ( ['telefone', 'celular', 'fax'].contains((eSelect.getOptionValue()+'').toLowerCase())) {
				eValue.setFixedMask('(##) ####-####');
			}
		}
		Editor.changeContactMask	= ChangeMask;
		
		jCube('::#card-contact select.gt8-update[name=contact.channel]').addEvent('onchange', function(e){
			var eParent	= this.getParent('div');
			ChangeMask( this, eParent);
		}).each(function(){
			var eParent	= this.getParent('div');
			ChangeMask( this, eParent);
		})
	})();
	
	jCube('::.TabbedPane input[name=birth]').setFixedMask('##/##/####');
	
	jCube(':.TabbedPane select[name=natureza]').addEvent('onchange', function() {
		if ( jCube(':.TabbedPane select[name=natureza]').getOptionValue() == 'F') {
			jCube(':.TabbedPane input[name=cpfcnpj]').setFixedMask('###.###.###-##');
		} else {
			jCube(':.TabbedPane input[name=cpfcnpj]').setFixedMask('##.###.###/####-##');
		}
	});
	jCube(':.TabbedPane select[name=natureza]').trigger('onchange');
	
	Editor.enabled	= true;
	
	var aRoot	= ASP.contactsAPath;
	
	jCube('::#card-address a.card').each(function(){//CARDS settings
		this.onclick	= function(E) {
			var e	= jCube.Event(E);
			
			if ( e.target && e.target.className.contains('delete-button')) {
				return false;
			}
			
			Modal.show({
				objRef: this,
				url: aRoot + this.getAttribute('href').substring(0, this.getAttribute('href').length-1) + '?modal=1&locationbar=0'
			});
			return false;
		}
	});
	(function() {//NEW CARD
		var add	= jCube(document.createElement('A')).addClass('address card card-border col-7 new-card').appendTo(':#card-address > .cards');
		add.innerHTML	= '<em class="zip" >&nbsp;</em><span class="imgC" ><img src="'+ ASP.CROOT +'imgs/gt8/add-regular.png" alt="[imagem]" /></span><strong class="estado" >Adicionar endereço</strong><span class="title" >&nbsp;</span><span class="stt hidden " >&nbsp;</span><span class="city hidden " >&nbsp;</span><span class="street hidden " >&nbsp;</span><span class="number hidden " >&nbsp;</span><span class="district hidden " >&nbsp;</span>';
		add.href		= '#';
		add.onclick	= function() {
			Modal.show({
				objRef: this,
				url: ASP.contactsAPath +'?action=new&idContact='+ ASP.id
			});
			return false;
		}
		GT8.adjustImgSize(add.query(':.imgC img'));
	})();
	Modal.onClose	= function( name, id, changes) {
		if ( name == 'address-editor' ) {
			if ( Modal.crrOpener == jCube(':#card-address a.new-card')) {
				//novo endereço
				var newCard	= jCube(jCube(':#card-address a.new-card').cloneNode(true));
				Modal.crrOpener	= newCard;
				Modal.crrOpener.injectBefore(jCube(':#card-address a.new-card'));
				
				GT8.Spinner.show({
					label: 'Novo endereço adicionado com sucesso!',
					hideAfter: 3000
				});
				Modal.crrOpener.id		= 'address-'+ id;
				Modal.crrOpener.href	= id +'/';
				Modal.crrOpener.onclick	= function(e) {
					Modal.show({
						objRef: this,
						url: aRoot + this.getAttribute('href').substring(0, this.getAttribute('href').length-1) + '?modal=1&locationbar=0'
					});
					
					return false;
				}
			}
			
			var done	= [];
			var crr;
			changes.reverse().each(function(){
				if ( !done.contains(this[0])) {
					crr	= Modal.crrOpener.query(':.'+ this[0]);
					if ( this[0] == 'type') {
						Modal.crrOpener.query(':.imgC img').src	= ASP.CROOT +'imgs/gt8/address/'+ this[1] +'.png';
					} else if ( crr) {
						crr.innerHTML	= this[1];
					}
					done.push(this[0]);
				}
			});
			Modal.crrOpener.query(':.title').setHTML(
				Modal.crrOpener.query(':.street').innerHTML +', '+
				Modal.crrOpener.query(':.number').innerHTML +'. '+
				Modal.crrOpener.query(':.district').innerHTML
			);
		}
	};
	(function() {//ADDRESS
		Pager.createDeleteButtons('::#card-address .cards .card', { url: ASP.contactsAPath +'?action=delete'});
		jCube('::#card-address a.card .delete-button').removeClass('hidden');
	})();
});
