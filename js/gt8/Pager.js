jCube.Include("Array.append");
jCube.Include("Array.contains");
jCube.Include("Array.each");
jCube.Include("Array.hasDuplicates");
jCube.Include("Array.map");
jCube.Include("Array.removeDuplicates");
jCube.Include("Document.Cookie");
jCube.Include("Document.getHttpVariables");
jCube.Include("Element.addClass");
jCube.Include("Element.getComputedStyle");
jCube.Include("Element.getElementsBySelector");
jCube.Include("Element.getFirstChild");
jCube.Include("Element.getLastChild");
jCube.Include("Element.getNextSibling");
jCube.Include("Element.getOffset");
jCube.Include("Element.getOptionValue");
jCube.Include("Element.getPreviousSibling");
jCube.Include("Element.injectAfter");
jCube.Include("Element.injectBefore");
jCube.Include("Element.removeClass");
jCube.Include("Element.setDraggable");
jCube.Include("Element.setStyle");
jCube.Include("Event");
jCube.Include("String.endsWith");
jCube.Include("String.startsWith");
jCube.Include("String.substringIndex");
jCube.Include("Pluggins.SplitPane");
jCube.Include("Transition.moveTo");
jCube.Include("Transition.resizeTo");
jCube.Include("Transition.scrollTo");
jCube.Include("Window");

var Pager	= {
	cardsC: ':.cards',
	allowKeyboardNavigation: true,
	getCardsCols: function() {
		var eCards	= jCube(Pager.cardsC);
		var value	= 0;
		if ( eCards) {
			var card	= eCards.query(':a.card');
			if ( card) {
				value	= Math.floor(eCards.offsetWidth / ( card.offsetWidth + card.getComputedStyle('margin-left').toInteger() + card.getComputedStyle('margin-right').toInteger()));
			}
			if ( !value) {
				alert('não tem cards para determinar a quantidade de colunas:P\n\n\nPS: Não se esqueça de remover esta mensagem!')
			}
		}
		return value;
	},
	getCardRows: function() {
		var eCards	= jCube(Pager.cardsC);
		var value	= 0;
		if ( eCards) {
			var card	= eCards.query(':a.card');
			if ( card) {
				var H	= window.getHeight() - (jCube(':header.admin div.space').offsetHeight) - jCube(':footer.footer-clear-height').offsetHeight;
				var h	= card.offsetHeight + card.getComputedStyle('margin-top').toInteger() + card.getComputedStyle('margin-bottom').toInteger();
				value	= Math.floor(H / h);
			}
			if ( !value) {
				alert('não tem cards para determinar a quantidade de linhas:P\n\n\nPS: Não se esqueça de remover esta mensagem!')
			}
		}
		return value;
	},
	click: function(e) {
		var reg			= this.href.match(/\?([\D]{1,20})\=(.*)/) || [];
		var crr			= reg[2]? reg[2]: '';
		var goTo		= ASP.padmin;
		var loc			= window.location +'';
		var qs			= loc.contains('?')? loc.substring(loc.indexOf('?')): '';
		var charCode	= '';
		
		if ( (this.id+'').contains('-')) {
			charCode	= this.id.substringIndex('-');
		} else if ( (this.href+'').substring((this.href+'').lastIndexOf('?')+1).contains('-')) {
			charCode	= (this.href+'').substring((this.href+'').lastIndexOf('?')+1).substringIndex('-');
		} else if ( (this.title+'').contains('-')) {
			charCode	= this.title.substringIndex('-');
		}
		
		if ( ASP.marcas && ASP.marcas.removeDuplicates) {
			ASP.marcas	= ASP.marcas.removeDuplicates();
		}
		//alert(-111 +"\n"+ charCode)
		if ( Pager.click['_'+ charCode] ) {
			if ( !crr) {
				crr	= (this.href.match(/\?([\D]+)\-(.*)/) || [])[2];
			}
			goTo	= Pager.click['_'+ charCode]( this, crr, goTo, qs, jCube.Event(e));
		} else {
			//alert(33 +"\ngoTo: \t"+ goTo +"\ncrr:  \t"+ crr +"\nqs:   \t"+ qs +"\nhref:\t"+ this.href +"\nattrb:\t"+ this.getAttribute('href'))
			var b	= false;
			if ( (this.getAttribute('href')+'').contains('?')) {
				var vars	= jCube.Document.getHttpVariables( this.getAttribute('href'));
				if ( vars && vars[0]) {
					goTo	= Pager.parse( vars[0][0], vars[0][1], true, qs);
					b		= true;
				}
			}
			if ( !b) {
				goTo	= Pager.parse( 0, 0, true, this.getAttribute('href')+qs);
			}
			
		}
		
		if ( goTo) {
			this.href	= goTo.replace(/\/\//g, '/');
		}
		//alert(89 +"\n"+ this.href)
		return true;
	},
	parse: function( name, value, resetIndex, loc) {
		loc	= (loc || '') +'';
		//alert(11111 +"\n"+ name+" : "+ value +"\n"+ resetIndex +"\n"+ loc)
		var vars	= jCube.Document.getHttpVariables( loc);
		var found	= false;
		vars.map( function() {
			if ( resetIndex && this[0] =='index') {
				return ['index', 1];
			}
			if ( this[0] == name) {
				found	= true;
				return [name, value];
			} else {
				return this;
			}
		}, null, true);
		if ( !found) {
			vars.push([name, value]);
		}
		var sVars	= '?';
		vars.each(function(){
			if ( this[1] && !(this[0]=='index' && this[1]==1) ) {
				sVars	+= '&'+ this[0] +'='+ this[1];
			}
		});
		loc	= loc.substringIndex('?') + sVars;
		if ( loc.endsWith('/?') ) {
			loc	= loc.substringIndex('/?') + '/';
		}
		//alert(333 +"\n"+ loc)
		if ( loc.endsWith('?') ) {
			loc	= loc.substring(0, loc.length-1);
		}
		loc	= loc.replace('?&', '?');
		
		//alert(44444 +"\n"+ loc);
		if ( loc == '') {
			loc	= './';
		}
		//alert(9999 +"\n"+ loc)
		return loc;
	},
	goTo: function( eA, index) {
		eA.href	= this.parse('index', index);
		return true;
	},
	goPrevious: function( eA) {
		this.goTo( eA, Number(jCube.Document.getHttpVariables().get('index') || 1) - 1);
	},
	goNext: function( eA) {
		this.goTo( eA, Number(jCube.Document.getHttpVariables().get('index') || 1) + 1);
	},
	limit: function( eSelect) {
		var value	= jCube(eSelect).getOptionValue();
		
		if ( value == 'auto') {
			value	= Pager.getCardRows() * Pager.getCardsCols();
			if ( !value && jCube(':.SplitPane-horizontal .Grid') && jCube(':.SplitPane-horizontal .Grid .body .g-col > div')) {
				
				var H	= window.getHeight() - (jCube(':header.admin div.space').offsetHeight) - jCube(':footer.footer-clear-height').offsetHeight - jCube(':.SplitPane-horizontal .Grid .head').offsetHeight;
				var h	= jCube(':.SplitPane-horizontal .Grid .body .g-col > div').offsetHeight;
				value	= Math.floor(H / h);
			}
		}
		var bReload	= jCube.Document.Cookie.get( ASP.cardListerName+'-limit') != value;
		jCube.Document.Cookie.set( ASP.cardListerName +'-limit', value);
		if ( bReload) {
			window.location.reload();
		}
	},
	order: function( eSelect) {
		jCube(eSelect);
		window.location	= Pager.parse( 'order', eSelect.getOptionValue(), true);
	},
	cardNav: function(e, card) {
		
		if ( !Pager.allowKeyboardNavigation) {
			return false;
		}
		
		var crr	= Pager.cardNav.crr;
		var eCards	= jCube(Pager.cardsC);
		
		//may the stage viewer is in other mode than CARD view
		if ( !eCards) {
			return null;
		}
		//"0" é para se alguém quiser chamar a função cardNav de forma manual, basta enviar Pager.cardNav({ e.key:0, target:someDIV})
		if ( ![0,9,13,35,36,37,38,39,40].contains(e.key) || ['SELECT', 'TEXTAREA', 'INPUT'].contains(e.target.nodeName)) {
			return null;
		}
		
		if ( card) {
			crr	= crr || card;
			ChangeFocus(card);
		}
		if ( !crr ) {
			Pager.cardNav.crr = eCards.query(':a.card');
			if ( Pager.cardNav.crr) {
				Pager.cardNav.crr.addClass('focused');
			}
			return null;
		}
		function ChangeFocus( newCard, avoidEvent) {
			crr.removeClass('focused');
			Pager.cardNav.crr	= crr	= newCard.addClass('focused');
			
			if ( !avoidEvent) {
				jCube('::.cards .card').removeClass('selected');
			}
			if ( Pager.onNavigation ) {
				Pager.onNavigation(e);
			}
		}
		switch( e.key) {
			case 37: {
				if ( Pager.cardNav.crr.getPreviousSibling( true) ) {
					ChangeFocus(Pager.cardNav.crr.getPreviousSibling(true));
				}
				break;
			}
			case 38: {
				var next	= eCards.query('::a.card')[ crr.getNodeIndex() - Pager.getCardsCols() - 1];
				if ( next ) {
					ChangeFocus(next);
				}
				break;
			}
			case 39: {
				if ( Pager.cardNav.crr.getNextSibling( true) ) {
					ChangeFocus(Pager.cardNav.crr.getNextSibling(true));
				}
				break;
			}
			case 40: {
				var next	= eCards.query('::a.card')[ crr.getNodeIndex() + Pager.getCardsCols() - 1];
				if ( next ) {
					ChangeFocus(next);
				}
				break;
			}
			case 36: {
				if ( eCards.getFirstChild(true) ) {
					ChangeFocus(eCards.getFirstChild(true));
				}
				break;
			}
			case 35: {
				if ( eCards.getLastChild(true) ) {
					ChangeFocus(eCards.getLastChild(true));
				}
				break;
			}
			case 13: {
				if ( Pager.cardNav.crr) {
					Pager.parse.call( Pager.cardNav.crr);
					window.location	= Pager.cardNav.crr.href;
				}
				break;
			}
			case 0: {
				ChangeFocus(card, true);
				break;
			}
			default: {
				return null;
				break;
			}
		}
		if ( e.stop) {
			e.stop();
		}
		return false;
	},
	search: function( eInput, e) {
		var q	= (eInput.value+'');
		if ( e.keyCode == 13) {
			window.location	= this.parse( eInput.name, q, true);
		}
	},
	searchIn: function( eInput, e) {
		var q	= (eInput.value+'');
		jCube('::.SplitPane-horizontal .Grid .body .g-col > div, ::.SplitPane-horizontal .Grid .body .g-col > a').each(function(){
			if ( q && this.innerHTML.toLowerCase().contains(q.toLowerCase())) {
				var dif	= Math.round(230 - (q.length / this.innerHTML.length) * 60);
				this.setStyle({
					background: 'rgb(255, '+ dif +', '+ dif +')'
				});
			} else {
				this.setStyle({
					color: '',
					background: ''
				});
			}
		});
	},
	/*
		options:
			label
			url
			id
			name
			value
			gets[]		formato: [][name, value]
			onLoad
			labelError
	*/
	createDeleteButtons: function(selector, options) {
		var eDel	= jCube( document.createElement('DIV')).addClass('delete-button hidden');
		var crr;
		options	= options || {};
		
		jCube(selector||'::.right-pane a.card').each(function(){
			crr	= jCube(eDel.cloneNode(true));
			crr.onclick	= function(E) {
				if ( confirm(options.label || 'Tem certeza que deseja excluir este item?') ) {
					var crrCard	= this.getParent();
					var req	= new jCube.Server.HttpRequest({
						url: options.url || '?opt=delete',
						noCache: true,
						onLoad: function() {
							
							if ( this.responseText.contains('//#affected rows: ')) {
								crrCard.resizeTo( 35, 35, 700, null, function(){
									var bounds	= crrCard.getOffset(document.body);
									GT8.poof( bounds.left , bounds.top);
									crrCard.remove();
								});
							} else {
								var serverEMessage	= this.responseText.match(/error\: .*/);
								serverEMessage	= serverEMessage && serverEMessage[1]? serverEMessage[1]: '';
								
								GT8.Spinner.show({
									label: (options.labelError || 'Não foi possível excluir o item agora!') + (serverEMessage?'<br /><br />'+ serverEMessage:''),
									position: 'upper right',
									hideAfter: 10000,
									hideImage: true
								});
							}
							
							if ( options.onLoad) {
								options.onLoad( this.responseText);
							}
						}
					});
					if ( options.id) {
						req.addGet('id', options.id);
					} else if ( !isNaN(Number(this.getParent().id.substringIndex('-', -1)))) {
						options.id	= Number(this.getParent().id.substringIndex('-', -1));
						req.addGet('id', options.id);
					}
					
					if ( options.name) {
						req.addGet('name', options.name);
					}
					if ( options.value) {
						req.addGet('value', options.value);
					}
					(options.gets||[]).each(function(){
						req.addGet( this[0], this[1]);
					});
					req.start();
				}
				return false;
			}
			this.appendChild(crr);
		});
		
		if ( jCube(':header.admin nav.toolbar span.group-button a.delete-address')) {
			jCube(':header.admin nav.toolbar span.group-button a.delete-address').getParent().onGroupButtonToggle	= function( crrA) {
				if ( crrA.className.contains('selected')) {
					jCube('::.right-pane a.card .delete-button').removeClass('hidden');
				} else {
					jCube('::.right-pane a.card .delete-button').addClass('hidden');
				}
			}
		}
	}
}
jCube(function() {
	//listener (Cards)
	jCube('::.cards a.card, .cards .card a, .SplitPane-horizontal .left-pane section a, header nav.directory a.button, a.pager-click').each(function() {
		this.onclick	= this.onclick || Pager.click;
	});
	//listener (Grid)
	jCube('::.Grid').addEvent('onGridCellClick', function(e, cell){
		
	});
});


