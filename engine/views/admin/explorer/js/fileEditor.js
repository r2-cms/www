jCube.Include("Array.getLast");
jCube.Include("Document.Cookie");
jCube.Include("Element.getData");
jCube.Include("Element.getNextSibling");
jCube.Include("Element.injectBefore");
jCube.Include("Element.setAttr");
jCube.Include("Element.setFocus");
jCube.Include("Event.trigger");
jCube.Include("Number.format");
jCube.Include("Pluggins.Increaser");
jCube.Include("String.endsWith");
jCube.Include("String.right");
jCube.Include("String.startsWith");
jCube.Include("String.toASCII", 'UTF-8');
jCube.Include("String.trim");
jCube.Include("Time.format");
jCube.Include("Window.DOMReady");

var Explorer	= {
	onModalUpload: function( props) {
		if ( !Explorer.onModalUpload.initialized) {
			Explorer.onModalUpload.initialized	= true;
			return;
		}
		
		jCube(':#eSize').innerHTML		= (ASP.size = props.fileSize).format(0) + ' B';
		
		if ( jCube(':#eWidth')) {
			jCube(':#eWidth').innerHTML		= ASP.width		= props.width;
			jCube(':#eHeight').innerHTML	= ASP.height	= props.height;
		}
		
		jCube(':#eModification').innerHTML	= jCube.Time.format('%Y/%m/%d %T');
		
		//altera a extensão do arquivo automaticamente. Indesejável em algumas situações
		if ( 0 && ASP.type != 'directory') {//se for diretório, não deve haver alteração de nome ao fazer upload
			ASP.fileExtension	= props.fileExtension? props.fileExtension: ASP.fileExtension;
			if ( !jCube(':input[name=approved]').checked) {
				jCube(':input[name=filename]').value	= jCube(':input[name=title]').value + (ASP.fileExtension?'.'+ ASP.fileExtension: '');
				jCube(':input[name=filename]').value	= Explorer.validadeFilename();
				jCube(':input[name=filename]').trigger('onchange');
			}
		}
		//remove e insere para evitar problemas com o histórico, visto que é feito um POST durante o upload e se o cliente clicar em VOLTAR a página não volta, mas sim o frame
		if ( props.isUpload) {
			jCube(':iframe').remove();
			Explorer.createFrameUpload();
		}
		if ( props.src) {
			Editor.changes.push([ 'src', props.src]);
		}
	},
	createFrameUpload: function() {
		var
			w	= jCube(':.imgC').offsetWidth || jCube(':.imgC').getComputedStyle('width').toInteger(),
			h	= jCube(':.imgC').offsetHeight|| jCube(':.imgC').getComputedStyle('height').toInteger()
		;
		var iFrame	= jCube(document.createElement('IFRAME'));
		iFrame.allowTransparency	= "true";
		iFrame.frameBorder			= 0;
		iFrame.border				= 0;
		iFrame.setStyle("bounds", [0,0, w-2, h-2]).setStyle("position", "relative").appendTo( ':.imgC');
		iFrame.src	= ASP.padmin +'explorer/upload/?id='+ ASP.id +'&nocache='+ (new Date().getTime()) +'&W='+ w +'&H='+ h;
	},
	validadeFilename: function() {
		var value	= jCube(':input[name=filename]').value.toLowerCase().toASCII().replace(/\W/g, function(x){ return x=='.'? '.': (x=='@'?'@':'-'); });
		while( value.contains('--')) {
			value	= value.replace(/\-\-/g, '-')
		}
		//remoção do hífen inicial e final
		if ( value.startsWith('-')) {
			value	= value.substring(1);
		}
		if ( value.endsWith('-')) {
			value	= value.substring( 0, value.length-1);
		}
		
		if ( jCube('::nav.directory a').getLast()) {
			jCube('::nav.directory a').getLast().innerHTML	= value;
		}
		if ( jCube(':.imgC a.preview')) {
			jCube(':.imgC a.preview').href	= '../../explorer/'+ ASP.path + value + (ASP.type == 'directory'? '/': '');
		}
		return value;
	}
}
jCube(function(){
	jCube(':input[name=title]').setFocus();
	Editor.onRequestComplete	= function( response) {
		Editor.setAllReadOnly();
	}
	Explorer.createFrameUpload();
	
	//FILENAME AUTO
	var eFnRep	= jCube(jCube(':input[name=filename]').getParent().cloneNode(false)).addClass('auto-filename').setStyle({
		position: 'absolute',
		right: 3,
		top: 9,
		width: 'auto',
		fontSize: '11px',
		color: 'gray',
		textDecoration: 'underline',
		zIndex: 10,
		cursor: 'pointer',
		UserSelect: 'none',
		MozUserSelect: 'none',
		WebkitUserSelect: 'none'
	}).setHTML('editar').addEvent('onclick', function(e){
		
		if ( jCube(':input[name=filename]').readOnly == true) {//supõe-se que o primeiro clique venha por este 
			jCube(':input[name=filename]').readOnly	= false;
			
			this.setStyle({
				textDecoration: 'none',
				color: 'blue'
			}).getParent('label').removeClass('readonly');
			jCube(':input[name=filename]').removeClass('readonly');
			jCube(':input[name=filename]').value	= jCube(':input[name=title]').value + (ASP.fileExtension?'.'+ ASP.fileExtension: '');
			jCube(':input[name=filename]').value	= Explorer.validadeFilename();
			jCube(':input[name=filename]').trigger('onchange');
		} else {
			jCube(':input[name=filename]').readOnly	= true;
			
			this.setStyle({
				textDecoration: 'underline',
				color: 'gray'
			}).getParent('label').addClass('readonly');
			jCube(':input[name=filename]').addClass('readonly');
		}
	}).injectBefore( jCube(':input[name=filename]')).getParent();
	
	Editor.onBeforeUpdate = function(  name, value, obj, req) {
		switch( name) {
			case 'filename': {
				value	= Explorer.validadeFilename();
				break;
			}
		}
		return [ name, value ];
	}
	
	//LOCK - switch button event
	jCube(':#ePublishC .Switch input[name=locked]').addEvent('onchange', function(){
		var eInput	= this;
		//a small delay to avoid IE`s bug
		window.setTimeout(function(){
			ASP.locked = eInput.checked;
			jCube('::.attributes .attr-value .editable-1 input').each(function(){
				if ( ASP.locked) {
					this.getParent('label').addClass('readonly');
					this.setAttribute('disabled', 'disabled');
				} else {
					this.getParent('label').removeClass('readonly');
					this.removeAttribute('disabled');
				}
			});
		}, 50);
		
	});
	
	if ( ASP.type != 'directory') {//UPDATE ATTRIBUTES
		jCube('::.attributes .attr-value .editable-0 input').each(function(){
			this.getParent('label').addClass('readonly');
			this.setAttribute('disabled', 'disabled');
		});
		jCube('::.attributes .attr-value .editable-1 input, .attributes .attr-value .editable-1 select').addEvent('onchange', function(e) {
			e.stop();
			
			if ( ASP.locked) {
				GT8.Spinner.show({
					hideImage: true,
					type: 'warning',
					label: 'O arquivo está bloqueado!<br />Para editar o atributo, desbloqueie primeiro o arquivo.'
				});
				return null;
			}
			var pai	= this.getParent('label').id;
			var idAttribute	= pai.substring( 7, pai.lastIndexOf('-'));
			var idValue	= pai.substring( pai.lastIndexOf('-')+1);
			var req	= new jCube.Server.HttpRequest({
				url: '?action=update-attribute-value&id='+ idAttribute +'&idFile='+ idValue,
				noCache: true,
				onComplete: function() {
					
				}
			});
			req.addGet('value', this.value);
			GT8.Spinner.request(req);
			
			return null;
		});
		
		if ( ASP.locked) {
			jCube('::.attributes .attr-value .editable-1 input').each(function(){
				this.getParent('label').addClass('readonly');
				this.setAttribute('disabled', 'disabled');
			});
		}
	}
	
	//from now on, only directory
	if ( ASP.type != 'directory') {
		return null;
	};
	
	(function(){//Attributes (modal)
		var eModal	= jCube(':#eModalAttributeNew');
		jCube(':#eAttrShowModal').addEvent('onclick', function(e, objRef) {//show modal
			e.stop();
			if ( jCube(':.attributes > .card').className.contains('delete-visible')) {
				return null;
			}
			eModal.showOverlay({
				objRef: objRef || this,
				duration: 1000,
				transition: jCube.Transition.DEFAULT,
				transitionIn: [0.3, 1.06, 0.5, 1.19],
				doNotRemoveOnHiding: true,
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
					jCube(':#eAttrProperty').setFocus();
				}
			});
			
			if ( jCube(':#eAttrChooser select').getOptionValue() == 'enum') {
				
			}
			
			//se não houver objRef, o clique veio do direto do botão.
			//Neste caso, crie um novo atributo e redefina todos os campos
			if ( !objRef) {
				jCube(':#eAttrEnumChooser a').trigger('onclick');//o evento click esconde a caixa de texto e exibe a combo
				jCube(':#eAttrProperty').setValue('').trigger('onkeyup');
				jCube(':#eAttrChooser select').setValue("0").trigger('updateValue');
				jCube(':#eAttrLevelChooser select').setValue("0").trigger('updateValue');
			}
			idAttribute	= 0;
			jCube(':#eAttrFeedback').setHTML('&nbsp;');
			
			return null;
		});
		jCube(':#eAttrChooser select').addEvent('onchange', function() {
			if ( this.getOptionValue() == 'enum' ) {
				this.getParent().addClass('hidden');
				jCube(':#eAttrEnumChooser').removeClass('hidden').query(':input').setFocus();
			}
		});
		jCube(':#eAttrEnumChooser a').addEvent('onclick', function(e) {
			e.stop();
			jCube(':#eAttrChooser').removeClass('hidden');
			this.getParent().addClass('hidden');
		});
		jCube(':#eModalAttributeNew .href-button-cancel').addEvent('onclick', function(e) {
			e.stop();
			eModal.showOverlay();
		});
		var sending	= false;
		var idAttribute	= 0;
		var crrAttributeInEdition	= null;
		jCube(':#eModalAttributeNew .href-button-ok').addEvent('onclick', function(e) {//SEND
			e.stop();
			if ( sending) {
				return null;
			}
			//validação
			if ( !jCube(':#eAttrProperty').value) {
				jCube(':#eAttrFeedback').setHTML('O nome do atributo é obrigatório!');
				jCube(':#eAttrProperty').setFocus();
				
				return null;
			} else if ( !jCube(':#eAttrChooser select').selectedIndex) {
				jCube(':#eAttrFeedback').setHTML('Escolha a definição do valor!');
				jCube(':#eAttrChooser select').setFocus();
				
				return null;
			}
			var url	= idAttribute? '?action=update-attribute&id='+ idAttribute: '?action=new-attribute&id='+ ASP.id;
			var req	= new jCube.Server.HttpRequest({
				url: url,
				onComplete: function() {
					sending	= false;
					var ret	= GT8.onGeneralRequestLoad.call( this, null, true);
					
					jCube(':#eAttrFeedback').addClass('spinner-small-hidden').removeClass('error');
					
					if ( ret.affected) {
						jCube(':#eAttrFeedback').setHTML(ret.message);
						window.setTimeout( function(){//closes the modal with delay
							eModal.showOverlay();
						}, 1000);
						
						if ( crrAttributeInEdition) {
							crrAttributeInEdition.query(':.att span').setHTML(jCube(':#eAttrProperty').value);
							crrAttributeInEdition.query(':.lvl').setHTML(jCube(':#eAttrLevelChooser select').getOption().value);
							if ( jCube(':#eAttrChooser select').getOptionValue() == 'enum') {
								crrAttributeInEdition.query(':.typ').setHTML(jCube(':#eAttrEnumChooser input').value);
							} else {
								crrAttributeInEdition.query(':.typ').setHTML(jCube(':#eAttrChooser select').getOption().value);
							}
							crrAttributeInEdition.query(':.pfx').setHTML(jCube(':#eAttrPrefix').value);
							crrAttributeInEdition.query(':.sfx').setHTML(jCube(':#eAttrSuffix').value);
						} else {//crie um idêntico
							var div	= jCube(document.createElement('DIV')).addClass('line cursor-pointer');
							div.id	= 'e-attr-'+ ret.insertId;
							var att	= '<div class="att col-4" ><img src="'+ ASP.CROOT +'imgs/delete-aqua-small.png" width="24" height="24" alt="[excluir]" /><span>'+ jCube(':#eAttrProperty').value +'</span></div>';
							var lvl	= '<div title="'+ jCube(':#eAttrLevelChooser select').getOption().innerHTML +'" class="lvl col-3" >'+ jCube(':#eAttrLevelChooser select').getOption().innerHTML +'</div>';
							var typ	= '<div title="'+ jCube(':#eAttrChooser select').getOptionValue() +'" class="typ col-3" >'+ jCube(':#eAttrChooser select').getOptionValue() +'</div>';
							var pfx = '<div class="pfx col-2" >'+ jCube(':#eAttrPrefix').value +'</div>';
							var sfx = '<div class="sfx col-2" >'+ jCube(':#eAttrSuffix').value +'</div>';
							div.setHTML( att + lvl + typ + pfx + sfx +'<div class="clear"></div>');
							div.appendTo(':.attributes > .card');
							
							//events
							div.addEvent('onclick', __rowOnClick);
							div.query(':.att img').addEvent('onclick', __attImgOnClick);
						}
					} else {
						jCube(':#eAttrFeedback').addClass('error').setHTML(ret.message || ret.error || 'Não foi possível realizar a operação agora. Tente mais tarde.');
					}
					crrAttributeInEdition	= null;
				},
				onError: function() {
					sending	= false;
					jCube(':#eAttrFeedback').addClass('spinner-small-hidden error').setHTML('Erro inesperado. Por favor, tente mais tarde.');
				}
			});
			req.addGet('attribute', jCube(':#eAttrProperty').value);
			req.addGet('type', (jCube(':#eAttrChooser select').getOptionValue()!='enum'? jCube(':#eAttrChooser select').getOptionValue(): jCube(':#eAttrEnumChooser input').value));
			req.addGet('level', jCube(':#eAttrLevelChooser select').getOptionValue());
			req.addGet('prefix', jCube(':#eAttrPrefix').value);
			req.addGet('suffix', jCube(':#eAttrSuffix').value);
			req.start();
			sending	= true;
			
			jCube(':#eAttrFeedback').removeClass('spinner-small-hidden').removeClass('error').setHTML('Processando...');
			return null;
		});
		var __rowOnClick	= function(e) {
			//atributo
			jCube(':#eAttrProperty').setValue(this.query(':.att span').innerHTML).trigger('onkeyup');
			
			//prefixo
			jCube(':#eAttrPrefix').setValue(this.query(':.pfx').innerHTML).trigger('onkeyup');
			
			//sufixo
			jCube(':#eAttrSuffix').setValue(this.query(':.sfx').innerHTML).trigger('onkeyup');
			
			//type
			if ( ['string','integer','float'].contains(this.query(':.typ').title)) {
				jCube(':#eAttrChooser select').setValue(this.query(':.typ').title).trigger('updateValue');
				jCube(':#eAttrChooser select')
				jCube(':#eAttrEnumChooser a').trigger('onclick');
			} else {//enum
				jCube(':#eAttrChooser select').setValue('enum').trigger('onchange');
				jCube(':#eAttrEnumChooser input').setValue(this.query(':.typ').innerHTML).trigger('onkeyup');
			}
			//level
			jCube(':#eAttrLevelChooser select').setValue(this.query(':.lvl').title).trigger('updateValue');
			
			jCube(':#eAttrShowModal').trigger('onclick', this);
			crrAttributeInEdition	= this;
			idAttribute	= Number(this.id.substring(7));
		}
		jCube('::.attributes .line.cursor-pointer').addEvent('onclick', __rowOnClick);
		
		/***************************
		 *                         *
		 *	Attributes (exclusion) *
		 *                         *
		***************************/
		var eButtonRemove	= jCube(document.createElement('DIV')).setHTML('<a href="#" class="href-button href-button-warning col-3" ><span>Apagar</span></a>').getFirstChild();
		
		jCube(':#eAttrEdit').addEvent('onclick', function(e) {//visibility
			e.stop();
			if ( jCube(':.attributes > .card').className.contains('delete-visible')) {
				jCube(':.attributes > .card').removeClass('delete-visible');
				this.query(':span').setHTML('Editar');
				jCube('::.attributes > .card .delete-confirm').removeClass('delete-confirm').removeClass('error');
				if ( eButtonRemove.parentNode) {
					eButtonRemove.remove();
				}
			} else {
				jCube(':.attributes > .card').addClass('delete-visible');
				this.query(':span').setHTML('Concluir');
				jCube('::.attributes .line.cursor-pointer .delete').removeClass('delete-confirm');
			}
		});
		var __attImgOnClick	= function(e) {//confirmation
			e.stop();
			var pai	= this.getParent('.att').getParent();
			if ( pai.className.contains('delete-confirm')) {
				pai.removeClass('delete-confirm').removeClass('error');
				eButtonRemove.remove();
			} else {
				eButtonRemove.appendTo( pai.query(':.typ'));
				window.setTimeout(function(){ pai.addClass('delete-confirm error'); }, 50);
			}
		}
		jCube('::.attributes .att img').addEvent('onclick', __attImgOnClick, true);
		eButtonRemove.addEvent('onclick', function(e) {//exclusion
			e.stop();
			
			var pai	= this.getParent().getParent();
			var req	= new jCube.Server.HttpRequest({
				url: '?action=delete-attribute&id='+ pai.id.substring(7),
				onComplete: function() {
					if ( this.ret.affected) {
						pai.remove();
					} else {
						eSpinner.remove();
						pai.removeClass('delete-confirm').removeClass('error');
						eButtonRemove.remove();
					}
				}
			});
			GT8.Spinner.request(req);
			var eSpinner	= jCube(document.createElement('IMG'));
			eSpinner.src	= ASP.CROOT +'imgs/spinner.gif';
			eSpinner.className	= 'spinner';
			eButtonRemove.getParent().appendChild( eSpinner);
			eButtonRemove.remove();
			crrAttributeInEdition	= null;
		});
	})();
	
	return null;
});
