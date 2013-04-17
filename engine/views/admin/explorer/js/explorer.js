//EXPLORER
jCube.Include('Array.getLast');
jCube.Include('Element.hide');
jCube.Include('Element.injectBefore');
jCube.Include('Element.setAttr');
jCube.Include('Element.setFocus');
jCube.Include('Element.show');
jCube.Include('Pluggins.showOverlay');
jCube.Include('String.toASCII');
jCube.Include('Time.format');

//CUSTOMS
jCube.Include('Pluggins.CreateContextMenu');

var Explorer	= {
	folderModal: null,
	strings: {
		label: '',
		title: '',
		filename: ''
	},
	showModalNew:	function( objRef, isFolder) {
		//elements
		Explorer.folderModal	= Explorer.folderModal || jCube(':#eFolderEdit');
		Explorer.folderModalLabel	= Explorer.folderModalLabel || jCube(':#eFolderEdit h3');
		Explorer.folderModalTitle	= Explorer.folderModalTitle || jCube(':#eFolderEdit .title-holder small');
		Explorer.folderModalFName	= Explorer.folderModalFName || jCube(':#eFolderEdit .filename-holder small');
		
		Explorer.bcreateFolder	= isFolder;
		
		Explorer.folderModalLabel.setHTML( Explorer.strings.label || (isFolder? 'Novo diretório de arquivos': 'Novo arquivo'));
		Explorer.folderModalTitle.setHTML( Explorer.strings.title||'Título');
		Explorer.folderModalFName.setHTML( Explorer.strings.filename||'Nome do arquivo');
		
		Explorer.folderModal.showOverlay({
			objRef: objRef,
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
				jCube(':#eNewFolderTitle').setFocus();
			}
		});
		
		return false;
	},
	createNew: function ( serverValidate, e) {
		
		var E	= jCube.Event(e);
		E.stop();
		
		var title		= jCube(':#eNewFolderTitle').value;
		var filename	= (serverValidate? (jCube(':#eNewFolderName').value || title.toLowerCase()): title.toLowerCase());
		
		filename	= filename.toASCII().replace(/\W/g, function(x){ return x=='.'? '.': (x=='@'?'@':'-'); });
		while( filename.contains('--')) {
			filename	= filename.replace(/\-\-/g, '-')
		}
		jCube(':#eNewFolderFeedback').removeClass('error').setHTML('&nbsp;');
		
		if ( jCube(':#eNewFolderName').value != filename) {
			jCube(':#eNewFolderName').value	= filename;
		}
		
		if ( !serverValidate) {
			jCube(':#eNewFolderName').trigger('onkeyup');
		}
		
		if ( !jCube(':#eNewFolderTitle').value ) {
			jCube(':#eNewFolderFeedback').addClass('error').setHTML('Digite um título.');
			jCube(':#eNewFolderTitle').setFocus();
			return false;
		}
		
		if ( jCube(':#eNewFolderTitle').value && jCube(':#eNewFolderName').value) {
			jCube(':#eFolderEdit .buttons a.href-button-ok').removeClass('href-button-disabled');
		} else {
			jCube(':#eFolderEdit .buttons a.href-button-ok').addClass('href-button-disabled');
		}
		
		if ( !serverValidate) {
			return false;
		}
		jCube(':#eNewFolderFeedback').removeClass('spinner-small-hidden').setHTML('Validando. Aguarde...');
		var req	= new jCube.Server.HttpRequest({
			url: '?edit=1&action=new-'+ (this.bcreateFolder? 'folder': 'file'),
			onLoad: function() {
				var response	= this.responseText;
				this.onError();
				
				if ( response.contains('//#affected rows:')) {
					var id = 0;
					try {
						id	= response.match(/\/\/folder id\: \[([0-9]+)\]/)[1];
					} catch(e) {
						
					}
					
					if ( Explorer.bcreateFolder) {
						window.location	= filename +'/';
					} else {
						window.location	= filename +'?edit';
					}
					return;
					
					//create a new DOM folder
					var A	= jCube(document.createElement('A')).addClass('card col-6').setAttr('title', title);
					A.appendChild(jCube(document.createElement('EM')).addClass('user')).setHTML(ASP.u);
					A.appendChild(jCube(document.createElement('STRONG')).addClass('title').setHTML(title));
					var icon	= jCube(document.createElement('SPAN')).addClass('imgC');
					icon.appendChild(jCube(document.createElement('IMG')).setAttr('src', ASP.CROOT +'imgs/gt8/folder-generic-large.png'));
					A.appendChild(icon);
					//A.appendChild(jCube(document.createElement('EM')).addClass('childs').setHTML('0 / 0'));
					A.appendChild(jCube(document.createElement('SPAN')).addClass('filename').setHTML(filename));
					//A.appendChild(jCube(document.createElement('SMALL')).addClass('creation').setHTML(jCube.Time.format('%Y/%m/%d %T')));
					A.appendChild(jCube(document.createElement('SMALL')).addClass('modification').setHTML(jCube.Time.format('%Y/%m/%d %T')));
					A.href	= filename +'/';
					A.injectBefore(jCube('::.SplitPane-horizontal .right-pane .card').getLast() || jCube('::.SplitPane-horizontal .right-pane div').getLast());
					Explorer.folderModal.showOverlay();
				} else {
					jCube(':#eNewFolderFeedback').addClass('error').addClass('spinner-small-hidden');
					if ( response.contains('//#error:')) {
						jCube(':#eNewFolderFeedback').setHTML( response.substring( response.indexOf('//#error:')+9, response.indexOf('\n', response.indexOf('//#error:')+9)));
					} else {
						jCube(':#eNewFolderFeedback').setHTML('Erro: não foi possível criar uma nova pasta agora. Tente novamente mais tarde');
					}
					jCube(':#eNewFolderTitle').setFocus();
				}
				
			},
			onError: function() {
				jCube(':#eNewFolderFeedback').addClass('spinner-small-hidden').setHTML('&nbsp;');
			},
			noCache: true
		});
		req.addGet('idDir', ASP.d);
		req.addGet('name', 'filename');
		req.addGet('filename', filename);
		req.addGet('title', title);
		req.start();
		
		return false;
	},
	deleteFile:	function() {
		
		var selecteds	= jCube('::.cards a.selected, .cards a.focused');
		var hasFolders	= false;
		
		jCube('::.cards a.selected span.hidden span.type, .cards a.focused span.hidden span.type').each(function(){
			if ( this.innerHTML == 'directory') {
				hasFolders	= true;
			}
		});
		
		if ( selecteds.length && confirm('Tem certeza que deseja excluir este'+ (selecteds.length>1?'s '+ selecteds.length +' arquivos?': ' arquivo?') + (hasFolders? '\nO(s) arquivo(s) dentro do diretório selecionado também será(ão) excluídos.': ''))) {
			
			var hasLock	= false;
			selecteds.each(function(){
				if ( this.className.contains('locked-1')) {
					hasLock	= true;
				}
			});
			if ( hasLock) {
				if ( selecteds.length > 1) {
					alert('Não é possível excluir os arquivos enquanto houver um bloqueado entre eles!');
				} else {
					alert('Não é possível excluir o arquivo porque ele está bloqueado!');
				}
				return false;
			}
			
			//remova os cards
			var index	= 0;
			function Poofer() {
				var crr	= selecteds[index++];
				if ( crr) {
					crr.setStyle('visibility', 'hidden');
					
					//exclua o arquivo no servidor
					var req	= new jCube.Server.HttpRequest({
						noCache: true,
						url: "?",
						onComplete: function() {
							if (this.responseText.contains('//#affected rows:')) {
								crr.remove();
							} else {
								crr.setStyle('visibility', '');
							}
						}
					});
					req.url	= '?action=delete&value='+ crr.id.substring(4);
					GT8.Spinner.request( req);
					
					GT8.poof( crr.getOffset(document.body).left + crr.offsetWidth/2, crr.getOffset(document.body).top+crr.offsetHeight/2, Poofer);
				}
			}
			Poofer();
		}
		
		return false;
	}
}
Pager.cardsC	= ':.cards';
Pager.onNavigation	= function() {
	var dif	= 0;
	
	//se o footer.top < card.bottom
	if ( (dif=jCube(':nav.directory').getOffset( document.body).bottom - Pager.cardNav.crr.getOffset(document.body).top) > 0) {
		dif	= window.getScrollTop() - dif - jCube(':.cards .card').getComputedStyle('margin-top').toInteger() - jCube(':.cards .card').getComputedStyle('margin-bottom').toInteger();
		
		jCube(document.body).scrollTo({
			duration: 400,
			x: 0,
			y: Math.max(0, dif)
		});
	} else if ( (dif=Pager.cardNav.crr.getOffset(document.body).bottom-jCube(':footer.main').getOffset( document.body).top) > 0) {
		dif	= window.getScrollTop() + dif + jCube(':.cards .card').getComputedStyle('margin-top').toInteger() + jCube(':.cards .card').getComputedStyle('margin-bottom').toInteger();
		
		jCube(document.body).scrollTo({
			duration: 400,
			x: 0,
			y: Math.max(0, dif)
		});
	}
}
jCube(function(){
	//CONTEXT MENU ON CARDS
	var selection	= [];
	var pathPrefix	= ASP.CROOT;
	var menuContext	= new jCube.Pluggins.CreateContextMenu({
		onClick: function( row, e) {
			var loc	= (window.location +'').toLowerCase();
			switch( row.query(':.title').innerHTML.toLowerCase()) {
				case 'abrir': {
					row.href	= selection[0].href;
					break;
				}
				case 'editar': {
					row.href	= selection[0].query(':span.filename').innerHTML +'?edit';
					break;
				}
				case 'visualizar': {
					row.href	= pathPrefix +'explorer/'+ selection[0].query(':span.hidden .dirpath').innerHTML + selection[0].query(':span.filename').innerHTML + (selection[0].query(':span.hidden .type').innerHTML=='directory'? '/': '');
					break;
				}
				case 'editar conteúdo': {
					row.href	= selection[0].query(':span.filename').innerHTML +'/';
					break;
				}
				case 'excluir': {
					row.onclick	= function() {
						return false;
					}
					menuContext.closeAll();
					Explorer.deleteFile();
					return false;
					break;
				}
				case 'selecionar tudo': {
					row.onclick	= function() {
						return false;
					}
					jCube('::.cards .card').addClass('selected');
					menuContext.closeAll();
					return false;
					break;
				}
			}
			
			return false;
		},
		useLinks: true,
		data: [
			{title: 'Abrir'},
			{title: 'Editar'},
			{title: 'Visualizar'},
			{},
			{title: 'Editar Conteúdo'},
			{},
			{title: 'Selecionar tudo'},
			{},
			{title: 'Bloquear'},
			{},
			{title: 'Excluir'}
		]
	});
	menuContext.addTriggerTo('::.cards a.card');
	menuContext.onMainOpen	= function( menu, obj, e) {
		selection	= [obj];
		if ( !jCube('::.cards a.selected').length) {
			Pager.cardNav({key:0, target:obj}, obj);
		}
		Pager.allowKeyboardNavigation	= false;
		e.stop();
		return false;
	}
	menuContext.onMainClose	= function() {
		Pager.allowKeyboardNavigation	= true;
	};
	(function() {//SQUARED SELECTION FOR CARDS
		var eSqrt	= jCube(document.createElement('DIV')).setStyle({
			opacity: 0.2
		}).addClass('squared-selection').setAttr('id', 'eSqrtSelection');
		
		var __defaultDocumentMousemove	= null;
		var __defaultDocumentMouseup	= null;
		jCube('::.SplitPane-horizontal .right-pane, .SplitPane-horizontal').addEvent('mousedown', function(e) {
			if ( e.target == jCube(':.SplitPane-horizontal') || e.target==jCube(':.SplitPane-horizontal .right-pane') ) {
				eSqrt.appendTo( document.body).setStyle({
					left: e.pageX,
					top: e.pageY,
					width: 0,
					height: 0
				});
				
				//lista das posições
				var bounds	= [];
				jCube('::.cards .card').each(function(){
					var b	= this.getOffset( document.body);
					bounds.push([this, b.left, b.top, b.right, b.bottom]);
				});
				
				//DESELECIONAMENTO
				var
					shifted	= e.shift,
					ctrled	= e.ctrl,
					meted	= e.meta
				;
				if ( !meted && !ctrled && !shifted) {
					jCube('::.cards .card').removeClass('selected');
				} else {
					//invert
					if ( shifted) {
						jCube('::.cards a.selected').each(function(){
							this.__selectInverse	= true;
						});
					}
				}
				
				var
					x		= e.pageX,
					y		= e.pageY
				;
				
				this.style.UserSelect		=
				this.style.MozUserSelect	=
				this.style.webkitUserSelect	= 'none';
				this.unselectable			= 'off';
				document.onmousemove	= __defaultDocumentMousemove;
				document.onmousemove	= function( Event) {
					var e = jCube.Event(Event);
					
					//POSICIONAMENTO DO SQUARE
					var l, t, w, h;
					if ( e.pageX > x) {
						l	= x;
						w	= e.pageX - x;
					} else {
						l	= x - (x - e.pageX);
						w	= x - e.pageX;
					}
					
					if ( e.pageY > y) {
						t	= y;
						h	= e.pageY - y;
					} else {
						t	= y - (y - e.pageY);
						h	= y - e.pageY;
					}
					eSqrt.setStyle('bounds', [l, t, w, h]);
					
					//SELEÇÃO DOS CARDS
					bounds.each(function(){
						//[obj, left, top, right, bottom]
						var selected	= this[0].__selectInverse==true;
						if ( this[3] > l) {
							if ( this[1] < l+w) {
								if ( this[4] > t) {
									if ( this[2] < t+h) {
										if ( shifted && selected) {
											selected	= false;
										} else {
											selected	= true;
										}
									}
								}
							}
						}
						if ( selected) {
							this[0].addClass('selected');
						} else {
							this[0].removeClass('selected');
						}
					});
					
					
					e.preventDefault();
					e.stop();
				}
				document.onmouseup	= __defaultDocumentMouseup;
				document.onmouseup	= function(e) {
					document.onmousemove	= __defaultDocumentMousemove || null;
					document.onmouseup		= __defaultDocumentMouseup || null;
					__defaultDocumentMousemove	= null;
					__defaultDocumentMouseup	= null;
					eSqrt.remove();
					jCube(':.SplitPane-horizontal .right-pane').style.UserSelect		=
					jCube(':.SplitPane-horizontal .right-pane').style.MozUserSelect		=
					jCube(':.SplitPane-horizontal .right-pane').style.webkitUserSelect	= null;
					jCube(':.SplitPane-horizontal .right-pane').unselectable			= '';
					
					jCube('::.cards .card').each(function(){
						this.__selectInverse	= null;
					});
				}
			}
		});
	})();
	(function() {//BOOKMARS ADD NEW BOOKMARK
		var crrUL	= null;
		var crrCard	= null;
		jCube('::.cards a.card').setAttr('draggable', 'true').addEvent('dragstart', function(e){
			e.dataTransfer.effectAllowed = 'copy';
			e.dataTransfer.setData('Text', this.id);
			crrCard	= this;
		});;
		jCube(':#eBookmarks').addEvent('ondragover', function(e) {
			if ( crrCard && e.target ) {
				jCube(':#eBookmarks').style.border	= '';
				crrUL	= e.target;
				e.dataTransfer.dropEffect = 'copy';
				e.stop();
				
				jCube(':#eBookmarks').style.border	= '1px solid #3468A1';
				
				return false;
			}
			return null;
		});
		jCube('::#eBookmarks ul, #eBookmarks li, #eBookmarks a').addEvent('ondragleave', function(e) {
			if ( crrUL) {
				jCube(':#eBookmarks').style.border	= '';
			}
		});
		jCube('::#eBookmarks').addEvent('ondrop', function(e) {
			if ( crrUL && crrCard) {
				e.stop();
				var req	= new jCube.Server.HttpRequest({
					noCache: true,
					url: "?",
					onComplete: function() {
						if (this.responseText.contains('//bookmarks inserted successfully!')) {
							if ( jCube(':body ul.folder li.empty')) {
								jCube(':body ul.folder li.empty').remove();
							}
						} else {
							alert('Não foi possível salvar o favorito agora. Tente novamente mais tarde!');
							LI.remove();
						}
					}
				});
				
				//clonando o objeto LI
				var LI	= jCube(document.createElement('LI')).
					setHTML('<a href="'+ crrCard.href +'" title="'+ crrCard.id.substring(4) +'" ><img alt="[favorite icon]" src="'+ jCube(crrCard).query(':.imgC img').src.replace('?regular', '?small').replace('?large', '?small') +'" class="left-icon-small" /><span>'+ crrCard.query(':strong.title').innerHTML +'</span><small class="ballon" >('+ (Number(crrCard.query(':.hidden .folders').innerHTML)+Number(crrCard.query(':.hidden .files').innerHTML)) +')</small></a>')
				;
				
				req.addGet('action', 'addToBookmarks');
				req.addGet('value', crrCard.id.substring(4));
				req.start();
				if ( this.query(':ul li.empty')) {
					LI.injectBefore(this.query(':ul li.empty'));
				} else {
					LI.appendTo(this.query(':ul'));
				}
			}
		});
		jCube('::.cards a.card').addEvent('ondragend', function(e) {
			if ( crrUL) {
				jCube(':#eBookmarks').style.border	= '';
				crrCard	=
				crrUL	= null;
			}
		});
	})();
	(function() {//BOOKMARS REMOVE BOOKMARK
		var crrA	= null;
		var origXY	= [];
		var w		= 0;
		var h		= 0;
		var imgDelete	= ASP.CROOT +'imgs/gt8/cancel-small.png';
		var imgOrig		= null;
		
		jCube('::#eBookmarks li').setDraggable({
			onStart: function(e) {
				origXY	= [e.pageX, e.pageY];
				w	= this.offsetWidth;
				h	= jCube('::#eBookmarks').offsetHeight;
			},
			onDrag: function(e, px, py) {
				e.stop();
			},
			onComplete: function(e, px, py) {
				if ( Math.abs(px) > w || Math.abs(py) > h ) {
					GT8.poof( this.getOffset( document.body).left, this.getOffset( document.body).top);
					var LI	= this;
					LI.style.visibility	= 'hidden';
					var req	= new jCube.Server.HttpRequest({
						noCache: true,
						url: "?",
						onComplete: function() {
							if (this.responseText.contains('//bookmarks deleted successfully!')) {
								LI.remove();
							} else {
								alert('Não foi possível excluir o favorito agora. Tente novamente mais tarde!');
								LI.style.visibility	= '';
							}
						}
					});
					req.addGet('action', 'removeFromBookmarks');
					req.addGet('value', LI.query(':A').title.substring(4));
					
					req.start();
				} else {
					this.moveTo(origXY[0], origXY[1]);
				}
			}
		});
	})();
	(function() {//URL REWRITE FOR PAGER.CLICK EVENT
		Pager.click._d	= function( eA, crr, goTo) {
			goTo	= Pager.parse('d', crr);
			return goTo;
		}
		Pager.click._img	= function( eA, crr, goTo) {
			if ( crr) {
				goTo	= Pager.parse( 'd', crr, true);
			} else {
				var qsa	= jCube.Document.getHttpVariables();
				goTo	= eA.getAttribute('href');
				qsa.each(function(){
					goTo	= Pager.parse( this[0], this[1], true, goTo);
				});
				
				//TRIGGER PARENT MODAL
				if ( parent && parent.window && parent.window!=window && parent.Modal && parent.Modal.onChoose) {
					parent.Modal.onChoose(eA, window.ASP, window);
				}
			}
			return goTo;
		}
	})();
});
