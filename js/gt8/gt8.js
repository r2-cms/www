var GT8	= {
	init: function() {
		//caching for GT8.poof
		var imgSrc	= new Image();
		imgSrc.src	= jCube.root +'../imgs/gt8/poof-regular.png';
		
		jCube(this.analytics.onLoad);
		jCube(this.onLoad);
		
		
	},
	onLoad: function() {
		/***********************************************************************
		 * General utilities                                                   *
		***********************************************************************/
		//INPUTS
		jCube("::.input-validation label").each(function(){
			var
				main	= this,
				input	= this.query('input')[0]||this.query('textarea')[0],
				pseudo = this.query('span small')
			;
			if ( input) {
				input.addEvent('focus', function() {
					this.setSelection();
				});
				function CheckInput(){
					if ( this.value.length) {
						pseudo.addClass('hidden');
					} else {
						pseudo.removeClass('hidden');
					}
				}
				input.addEvent('keydown', CheckInput);
				input.addEvent('keyup', CheckInput);
				
				input.trigger('keyup');
			}
		});
		//PSEUDO
		jCube('::input.pseudo-text').each(function(e) {
			this.setPseudoText(this.title, {color:'#CCC'});
			this.title	= '';
		});
		//BUTTONS: [title|size|img]
		jCube("::.js-button").each(function(){
			var props	= this.title.split("|");
			new jCube.GUI.Button( Number(props[1].split('x')[1]), this, props[2], 800);
			this.title	= props[0];
			this.setStyle("width", Number(props[1].split('x')[0]));
		});
		jCube("::span.e-select select,label.select-encapsuled select").each(function(){//SELECT (SPAN.e-select)
			this.addEvent('updateValue', function() {
				this.getNextSibling().getFirstChild().innerHTML	= this.getOption().innerHTML;
			});
			this.addEvent('onchange', function(e) {
				this.trigger('updateValue');
			});
		}).trigger('updateValue');
		//GROUP-BUTTONs listener: onGroupButtonActive( A), onGroupButtonToggle( A)
		jCube('::span.group-button a, span.group-button button').each(function(){
			if ( this.getPreviousSibling()) {
				jCube(document.createElement('SMALL')).setHTML('<span>&nbsp;</span>').injectBefore( this);
			}
			this.onclick	= function() {
				var cls	= this.getParent().className;
				var isSelected	= this.className.split(/\s/).contains('selected');
				if ( this.className.contains('disabled')) {
					return false;
				}
				if ( cls.contains('group-unique') ) {
					this.getParent().query('::a,button').removeClass('selected');
					
					if ( cls.contains('group-toggle')) {
						if ( isSelected) {
							this.removeClass('selected');
						} else {
							this.addClass('selected');
						}
					} else {
						this.addClass('selected');
					}
					if ( !isSelected && this.getParent().onGroupButtonActive && this.className.split(/\s/).contains('selected')) {
						this.getParent().onGroupButtonActive( this);
					}
				} else {
					if ( cls.contains('group-toggle')) {
						this.toggleClass('selected');
						if ( this.getParent().onGroupButtonToggle ) {
							this.getParent().onGroupButtonToggle( this);
						}
						if ( isSelected && this.getParent().onGroupButtonActive ) {
							this.getParent().onGroupButtonActive( this);
						}
					} else if ( cls.contains('group-trigger-only') ) {
						this.addClass('selected');
						var This	= this;
						window.setTimeout(function(){ This.removeClass('selected');}, 250);
					} else {
						this.addClass('selected');
						if ( this.getParent().onGroupButtonActive ) {
							this.getParent().onGroupButtonActive( this);
						}
					}
				}
				return false;
			}
		});
		jCube("::.TabbedPane").each(function(){//CARDTAB
			new jCube.Pluggins.TabbedPane({
				container: this
			})
		});
		jCube("::.Switch").each(function(){
			new jCube.Pluggins.Switch({
				container: this
			})
		});
		jCube("::.target-blank").each(function(){//blank targets on <A/>
			this.target	= "_blank";
		});
		jCube("::table.no-cellspacing").each(function(){//TABLE.cellspacing and cellpadding
			this.cellSpacing	= 0;
			this.cellPadding	= 0;
		});
		//table.list-filter
		var lastFocused	= null;
		jCube("::table.list-filter tr").addEvent('onclick', function(e){
			if ( this.query(':input') && lastFocused!=this) {
				this.query(':input').setFocus().setSelection();
				lastFocused	= this;
			}
		});
		jCube("::.allow-focus input").each(function(){//FOCUS on INPUTS
			this.addEvent("focus", function() { this.parentNode.parentNode.addClass("focused"); });
			this.addEvent("blur", function() { this.parentNode.parentNode.removeClass('focused'); });
		});
		jCube("::input.allow-focus").each(function(){
			var crr	= this;
			this.addEvent("focus", function() { window.setTimeout(function(){ crr.select(); }, 150) });
		});
		jCube('::.imgC img').each(function(){//imgC img: centralize images
			GT8.adjustImgSize(this);
		});
		jCube('::.auto-sprite').each(function(){//img.auto-sprite
			var sett	= this.title.split('|');
			this.setStyle({
				backgroundImage: 'url('+ sett[1] +')',
				backgroundPosition: (-sett[2]*sett[3].split(',')[0]) +"px "+ (-sett[2]*sett[3].split(',')[1]) +"px"
			});
			this.title	= sett[0] || (this.alt+'').substring(1, (this.alt+'').length-1);
			sett	= null;
		});
		//size adjustments
		var size	= null;
		jCube('::.cards a.card .imgC img').each(function() {
			size	= this.alt.split('x');
			if ( Number(size[0]) > Number(size[1])) {
				this.className	= 'larger-than-taller';
			}
		});
		jCube('::.Grid').each(function(){//GRID
			new jCube.Pluggins.Grid({grid:this});
		});
		(function(){//INPUT INTEGER ONLY
			jCube('::.gt8-input-allow-integer').addEvent('onkeydown', function(e){
				if ( e.key.isBetween( 48, 57) || e.key.isBetween( 96, 105) || e.key==109 || e.key==107) {//is number or -+
					//ok
				} else if ( e.key.isBetween( 8, 20) ) {//special chars
					//ok
				} else if ( e.meta || e.ctrl || e.alt) {
					//ok
				} else if ( e.key.isBetween(37, 40)) {//arrows
					//ok
				} else {
					e.stop();
				}
			});
		})();
		(function(){//INPUT FLOAT ONLY
			jCube('::.gt8-input-allow-float').addEvent('onkeydown', function(e){
				if ( e.key.isBetween( 48, 57) || e.key.isBetween( 96, 105) || e.key==109 || e.key==107 || e.key==190 || e.key==188) {//is number or -+,
					//ok
				} else if ( e.key.isBetween( 8, 20) ) {//special chars
					//ok
				} else if ( e.key.isBetween(37, 40)) {//arrows
					//ok
				} else {
					e.stop();
				}
			});
		})();
		jCube('::input.fixed-mask').each(function(){//masks
			this.setFixedMask(this.title).title	= '';
		});
		(function(){//INCREASER side-by-size
			var __mouseDowned	= false;
			var __keyPressed	= false;
			var __crrInput		= null;
			var __isPressed		= false;
			var __mouseOvered	= false;
			var __keyPressed	= false;
			var Check	= function( Inc) {
				var value	= Inc.eInput.value;
				
				value	= Number( value) || parseInt(value) || 0;
				
				if ( value > Inc.max) {
					value	= Inc.max;
				} else if ( value <Inc.min ) {
					value	= Inc.min;
				}
				
				Inc.eInput.value	= value;
				return this;
			}
			jCube('::label.increaser').each(function(){
				var eInput	= this.query(':input');
				var args	= eInput.title.split('|');
				var Inc	= {
					min:  args[0]==""? 0: Number(args[0]),
					max:  args[1]==""? Number.MAX_VALUE: Number(args[1]),
					step: !args[2]? 1: Number(args[2]),
					timeout: 480,
					eInput: eInput
				}
				var __dir;
				
				var __chron	= new jCube.Time.Chronometer();
				__chron.onComplete	= function () {
					if ( __keyPressed ) {
						if ( __mouseDowned && __mouseOvered || !__mouseDowned) {
							if ( __dir == 'top') {
								eInput.trigger('onkeydown', 38);
							} else {
								eInput.trigger('onkeydown', 40);
							}
						}
						__chron.start(50);
					}
				}
				eInput.addEvent('keydown', function(E, argKey){
					E.stop();
					
					var key		= E.key,
						shift	= E.shift,
						ctrl	= E.ctrl,
						meta	= E.meta,
						alt		= E.alt,
						value	= Number(this.value)
					;
					
					key	= argKey || key;
					
					//temos de certificar que se a ação vier do mouse, ele está realmente pressionado
					if ( argKey && !__mouseDowned ) {
						//do nothing
					} else if ( ctrl || alt || meta ) {
						//ignore
					} else if ( key == 38 ) {
						if ( value < Inc.max ) {
							if ( argKey==null || __mouseOvered ) {
								this.value			= Number(value) + Number(Inc.step);
								if ( __keyPressed == false) {
									__chron.start( Inc.timeout);
								}
							}
							__dir 	= 'top';
							__keyPressed	= true;
							
							this.trigger('onchange');
							
						} else {
							__keyPressed	= false;
						}
						Check( Inc);
					} else if	( key == 40 ) {
						
						if ( value > Inc.min ) {
							if ( argKey==null || __mouseOvered ) {
								this.value			= value - Inc.step;
								
								if ( !__keyPressed) {
									__chron.start( Inc.timeout);
								}
							}
							
							__dir 	= 'bottom';
							__keyPressed	= true;
							
							this.trigger('onchange');
							
						} else {
							__keyPressed	= false;
						}
						Check(Inc);
					} else if ( key == 27 ) {
						__keyPressed	= false;
					}
					
				});
				this.query('::a').addEvent('mousedown', function(E){
					__mouseDowned	= true;
					__mouseOvered	= true;
					eInput.trigger('onkeydown', this.className.contains('up')? 38: 40);
					
				}).addEvent('click', function(E){
					E.stop();
				});
				this.query('::a').addEvent('mouseup', function(E){
					E.stop();
					__keyPressed	= false;
				});
				this.query('::a').addEvent('mouseout', function(E){
					__mouseOvered	= false;
				});
				this.query('::a').addEvent('mouseover', function(E){
					__mouseOvered	= true;
				});
				this.query('::a').addEvent('mousemove', function(E){
					__mouseOvered	= true;
				});
				eInput.addEvent('keyup', function(E){
					__keyPressed	= false;
				});
			});
		})();
		(function() {//Errors
			jCube('::.gt8-debug-error > img').addEvent('onclick', function(e){
				var b	= this.getOffset(document.body);
				GT8.poof( b.left-30, b.top-20);
				this.getParent().remove();
			});
			var top	= 0;
			jCube('::.gt8-debug-error').each(function(e){
				this.appendTo(document.body);
				this.setStyle({
					top: top,
					width: window.getWidth()-40
				});
				top	+= this.offsetHeight + 5;
			});
		})();
		//MERGING FROM /js/main.js. It must be mirrored
		jCube("::label.input-button, label.input-text").each(function(){
			var
				main	= this,
				input	= this.query(':input'),
				pseudo	= this.query(':small'),
				link	= this.query('::a')
			;
			input.addEvent('focus', function() {
				this.setSelection();
			});
			function CheckInput(){
				if ( this.value.length) {
					pseudo.fadeOut(450);
					link.addClass('href-button-blue');
				} else {
					pseudo.fadeIn();
					link.removeClass('href-button-blue');
				}
			}
			input.addEvent('keydown', CheckInput);
			input.addEvent('keyup', CheckInput);
			input.addEvent('keyup', function(E){
				if ( E.key == 27) {
					input.value	= '';
					CheckInput.call( input, E);
				} else if ( E.key == 13) {
					this.trigger('click', E);
				}
			});
			input.trigger('keyup');
		});
	},
	adjustImgSize: function( eImg){
		eImg	= jCube(eImg);
		if ( !eImg.getParent || !eImg.getParent()) {
			return null;
		}
		var W	= eImg.getParent().offsetWidth;
		var H	= eImg.getParent().offsetHeight;
		var dif	= W/H;
		var rs	= eImg.getRealSize();
		rs.w	= rs[0];
		rs.h	= rs[1];
		var paddingTop	= (eImg.getParent().getComputedStyle('padding-top')+'').toInteger();
		
		var w	= rs.w;
		var h	= rs.h;
		
		if ( w > W) {
			w	= W;
			h	= rs.h / (rs.w/W);
			if ( h > H) {
				h	= H;
				w	= 'auto';
			}
		} else if ( h > H ) {
			h	= H;
			w	= 'auto';
		}
		if ( !w || !h) {
			window.setTimeout( function(){ GT8.adjustImgSize(eImg); }, 250);
			return null;
		}
		eImg.setStyle({
			width: w,
			height: h
		});
		
		eImg.addEvent('onload', function(){
			GT8.adjustImgSize(this);
		});
		
		if ( w < W) {
			//eImg.setStyle('left', (W-w)/2);
		}
		if ( h < H) {
			eImg.setStyle('top', (H-h)/2);
		}
		return eImg;
	},
	analytics: {
		gets: [],
		//listeners
		onLoad:		function() {
			window.onscroll = 
			document.documentElement.onscroll	= 
			document.body.onscroll			= function() {
				var scroll	= window.scrollTop || document.documentElement.scrollTop || document.body.scrollTop || 0;
				GT8.analytics.scroll	= Math.max( scroll, GT8.analytics.scroll);
			}
			
			var req	= GT8.analytics.send();
			jCube(window).addEvent("beforeunload", GT8.analytics.send, true);
		},
		send:		function( a, forceAsync) {
			var browserCode	= 1;
			if ( jCube.Browser.chrome ) {
				browserCode	= 4;
			} else if ( jCube.Browser.webkit ) {
				browserCode	= 5;
			} else if ( jCube.Browser.opera ) {
				browserCode	= 6;
			} else if ( jCube.Browser.msie ) {
				browserCode	= 3;
			} else if ( jCube.Browser.mozilla ) {
				browserCode	= 2;
			}
			
			var OScode	= 1;
			var ua		= navigator.appVersion.toLowerCase();
			if ( ua.indexOf("win") > -1) {
				OScode	= 2;
			} else if ( ua.indexOf("mac") > -1) {
				OScode	= 3;
			} else if ( ua.indexOf("unix") > -1 || ua.indexOf("x11") > -1) {
				OScode	= 4;
			} else if ( ua.indexOf("linux") > -1) {
				OScode	= 5;
			}
			
			var session	= jCube.Document.Cookie.get("jsanalyticssessid");
			GT8.analytics.session	= session;
			
			//scroll in percentage
			var maxScroll	= 0;
			var scroll	= 0;
			if ( document.body) {
				maxScroll	= window.getScrollHeight();
				scroll		= Math.floor( (GT8.analytics.scroll+window.getHeight())/maxScroll * 100);
			}
			var url		= (window.location+"").substring((window.location+"").indexOf("/", 10));
			
			var referrer	= (document.referrer.startsWith( jCube.root.replace('jCube/', '').substring(0,30))? "": document.referrer);
			if ( referrer.indexOf("mail.live")>-1) {
				referrer	= referrer.replace( /https?\:\/\/[a-z0-9\.]+\//, "http://mail.live.com/");
			} else if ( referrer.indexOf("mail.yahoo")>-1) {
				referrer	= referrer.replace( /https?\:\/\/[a-z0-9\.]+\//, "http://mail.yahoo.com/");
			} else if ( referrer.indexOf("terra.com")>-1 && referrer.indexOf("mail")>-1) {
				referrer	= referrer.replace( /https?\:\/\/[a-z0-9\.]+\//, "http://mail.terra.com.br/");
			} else if ( referrer.indexOf("mail.uol.com")>-1 ) {
				referrer	= referrer.replace( /https?\:\/\/[a-z0-9\.]+\//, "http://mail.uol.com.br/");
			} else if ( referrer.indexOf("click21.com")>-1 && referrer.indexOf("mail")>-1) {
				referrer	= referrer.replace( /https?\:\/\/[a-z0-9\.]+\//, "http://webmail.click21.com.br/");
			} else if ( referrer.indexOf("images.google.co")>-1 ) {
				referrer	= referrer.replace( /https?\:\/\/[a-z0-9\.]+\//, "http://images.google.com/");
			} else if ( referrer.indexOf("https://shopline.itau.com.br/shopline")>-1 ) {//não é referrer!
				referrer	= "";
			}
			referrer	= referrer.replace( /www[0-9]/g, "www");
			
			//envio
			var req	= new jCube.Server.HttpRequest( {
				url: jCube.root +"../admin/analytics/?",
				method: jCube.Server.HttpRequest.HTTP_POST,
				async: (forceAsync? jCube.Server.HttpRequest.HTTP_ASYNCHRONIZED: jCube.Server.HttpRequest.HTTP_SYNCHRONIZED),
				noCache: true
			});
			req.addGet( "analytics", 'GT8');
			req.addGet( "format", 'JSON');
			req.addGet( "duration", Math.floor((new Date().getTime() - GT8.analytics.tstart)/1000));
			req.addGet( "url", GT8.analytics.url || url);
			req.addGet( "scroll", scroll);
			req.addGet( "referrer", referrer);
			req.addGet('browser', browserCode);
			req.addGet('browser_v', jCube.Browser.version);
			req.addGet( "OS", OScode);
			
			GT8.analytics.gets.each(function(){
				req.addGet(this[0], this[1]);
			});
			
			if ( GT8.analytics.unloadingEvent) {
				req.addGet("unloadingEvent", GT8.analytics.unloadingEvent);
			}
			req.onLoad	= function() {
				var session	= "";
				var id;
				
				try {
					eval( this.responseText);
				} catch(e) {
					
				}
				
				if ( id) {
					GT8.analytics.unloadingEvent	= id;
				}
				
				if ( session) {
					jCube.Document.Cookie.set("jsanalyticssessid", session, 0.021);
				}
				
				//mais de 30 minutos??
				if ( (new Date().getTime() - GT8.analytics.tstart)/60/1000 > 28) {
					window.clearInterval(GT8.analytics.interval);
				}
			}
			
			req.start();
			if ( !GT8.analytics.send.initialized) {
				GT8.analytics.send.initialized	= true;
				window.setTimeout(function(){
					if ( GT8.analytics.unloadingEvent) {
						GT8.analytics.send();
						GT8.analytics.interval	= window.setInterval(GT8.analytics.send, 60 * 1000);
					}
				}, 30*1000);
			}
			return req;
		},
		//initial properties
		tstart:		new Date().getTime(),
		scroll:		0
	},
	poof: function( x, y, onComplete) {
		var img	= GT8.poof.img;
		if ( !img) {
			img	= jCube(document.createElement('DIV')).setStyle({
				position: 'absolute',
				width: 100,
				height: 86,
				zIndex: 1000,
				background: 'transparent url('+ jCube.root +'../imgs/gt8/poof-regular.png) no-repeat center'
			});
			GT8.poof.img	= img;
		}
		
		img.appendTo(document.body).setStyle({
			left: x||0,
			top: y||0
		});
		
		for( var i=0, steps=5; i<steps; i++) {
			(function( pos, i){
				img.setBackgroundPosition( pos, 0);
				if ( i==steps-1) {
					img.remove();
					if ( onComplete) {
						onComplete();
					}
				}
			}).delay(i*90, window, -(100*i), i);
		}
		
	},
	/*
		option prefix
	*/
	onGeneralRequestLoad: function(options, valueOnly) {
		options	= options || {};
		var ret	= {
			value: 0,
			affected: 0,
			error: '',
			message: '',
			insertId: 0
		};
		
		if ( this.responseText.contains('//#affected')) {
			if ( !valueOnly) {
				GT8.Spinner.show({
					hideAfter: 3000,
					label: 'Alteração realizada com sucesso!',
					position: 'upper right',
					hideImage: true
				});
			}
			var value	= this.responseText.match(/\/\/\#affected(\ rows)?\:?\ ?([0-9\.]+)/);
			if ( value && value[2]) {
				value	= Number(value[2]);
			} else {
				value	= 0;
			}
			ret.affected	= value;
		}
		if ( this.responseText.contains('//#insert id')) {
			if ( !valueOnly) {
				GT8.Spinner.show({
					hideAfter: 3000,
					label: 'Objeto criado com sucesso!',
					position: 'upper right',
					hideImage: true
				});
			}
			var value	= this.responseText.match(/\/\/\#insert\ id\:\ ?([0-9\.]+)/);
			if ( value && value[1]) {
				value	= Number(value[1]);
			} else {
				value	= 0;
			}
			ret.insertId	= value;
		}
		if ( this.responseText.contains('//#error:')) {
			ret.error	= this.responseText.substring( this.responseText.indexOf('//#error:')+9, (this.responseText.contains('\n')? this.responseText.indexOf('\n', this.responseText.indexOf('//#error:')+9): this.responseText.length));
			ret.error	= ret.error=='//#error:'? '': ret.error;
			
			if ( !valueOnly) {
				GT8.Spinner.show({
					type: 'error',
					position: 'upper right',
					hideAfter: 10000,
					label: ret.error,
					hideImage: true
				});
			}
		}
		if ( this.responseText.contains('//#message:')) {
			ret.message	= this.responseText.substring( this.responseText.indexOf('//#message:')+11, this.responseText.indexOf('\n', this.responseText.indexOf('//#message:')+11));
			if ( !valueOnly) {
				GT8.Spinner.show({
					type: 'positive',
					position: 'upper right',
					hideAfter: 10000,
					label: ret.message,
					hideImage: true
				});
			}
		}
		if ( this.responseText.contains('//#value:')) {
			var value	= this.responseText.match(/\/\/\#value\:\ ?([0-9\.]+)/);
			if ( value && value[1]) {
				value	= Number(value[1]);
			} else {
				value	= 0;
			}
			if ( options.spinner !== false && !valueOnly) {
				GT8.Spinner.show({
					hideAfter: options.hideAfter || 3000,
					label: (options.prefix || 'Valor: ') + value.format(2),
					position: 'upper right',
					hideImage: true
				});
			}
			ret.value	= value;
		}
		return ret;
	},
	/*
		options:
			load( options)
				onAffect(affectedRows, response)
				onError(message, response)
				label: Aguarde...
				hideAfter: 5000
				valuePrefix: Valor:
				affected: Operação realizada com sucesso!
				message: ''
				error: Erro não especificado
			label
			hideAfter	milliseconds
			duration
			transition
			delay
			showGlasspane
			showGlasspaneOptions
			hideImage
			type error
			position	center|upper right
	*/
	Spinner: (function() {
		var gp, topUR	= 20, urC=null;
		return {
			request: function( req, eLabel, eMessage, eSpinner) {
				req.label		= req.label || 'Aguarde...';
				var hideAfter	= req.hideAfter || 10000;
				req.hideAfter	= 0;
				req.position	= req.position || 'upper right';
				req.duration	= req.duration || 850;
				
				if ( eLabel) {
					eLabel.addClass('spinning');
				} else if ( eMessage) {
					if ( eSpinner) {
						eSpinner.removeClass('hidden').setStyle('opacity', 0).fadeIn(1);
					}
					eMessage.setHTML( req.label);
				} else {
					var growl	= GT8.Spinner.show({
						label: req.label,
						position: req.position,
						duration: req.duration
					});
					req.growl	= growl;
				}
				var onLoad	= req.onLoad || req.onComplete;
				var onError	= req.onError;
				
				req.onLoad	= null;
				req.onComplete	= function() {
					var ret	= GT8.onGeneralRequestLoad.call( this, null, true);
					if ( growl) {
						growl.obj.eImgC.addClass('hidden');
					} else if ( eLabel) {
						eLabel.removeClass('spinning');
					} else if ( eMessage) {
						if ( eSpinner) {
							eSpinner.fadeOut({
								duration: 0.6,
								onComplete: function() {
									this.addClass('hidden');
								}
							});
						}
					}
					
					var actionPerformed	= false;
					if ( ret.affected) {
						if ( growl) {
							growl.obj.query(':span').innerHTML	= ret.message || 'Operação realizada com sucesso!';
							growl.obj.getFirstChild().className	= 'affected';
						} else if ( eLabel) {
							eLabel.addClass('positive').removeClass('waiting');
							eLabel.query(':em').setHTML('&nbsp;');
						} else if ( eMessage) {
							eMessage.addClass('positive').removeClass('waiting');
							eMessage.setHTML( ret.message || 'Operação realizada com sucesso!');
						}
						actionPerformed	= true;
					}
					if ( ret.error) {
						if ( growl) {
							growl.obj.query(':span').innerHTML	= ret.error || 'Erro não especificado';
							growl.obj.getFirstChild().className	= 'error';
						} else if ( eLabel) {
							eLabel.addClass('error').removeClass('waiting');
							eLabel.query(':em').setHTML( ret.error || 'Erro não especificado');
						} else if ( eMessage) {
							eMessage.addClass('error').removeClass('waiting');
							eMessage.setHTML( ret.error || 'Erro não especificado');
						}
						actionPerformed	= true;
					}
					if ( ret.value) {
						if ( growl) {
							growl.obj.query(':span').innerHTML	= (ret.valuePrefix || 'Valor: ') + req.value.format(2);
							growl.obj.getFirstChild().className	= 'value';
						} else if ( eLabel) {
							eLabel.addClass('positive').removeClass('waiting');
							eLabel.query(':em').setHTML('&nbsp;');
						} else if ( eMessage) {
							eMessage.addClass('positive').removeClass('waiting');
							eMessage.setHTML('&nbsp;');
						}
						actionPerformed	= true;
					}
					if ( ret.insertId) {
						if ( growl) {
							growl.obj.query(':span').innerHTML	= ret.message || 'Objeto criado com sucesso!';
							growl.obj.getFirstChild().className	= 'inserted';
						} else if ( eLabel) {
							eLabel.addClass('positive').removeClass('waiting');
							eLabel.query(':em').setHTML('&nbsp;');
						} else if ( eMessage) {
							eMessage.addClass('positive').removeClass('waiting');
							eMessage.setHTML('&nbsp;');
						}
						actionPerformed	= true;
					}
					if ( ret.message) {
						if ( growl) {
							growl.obj.query(':span').innerHTML	= ret.message;
							growl.obj.getFirstChild().className	= 'message';
						} else if ( eLabel) {
							eLabel.addClass('positive').removeClass('waiting');
							eLabel.query(':em').setHTML('&nbsp;');
						} else if ( eMessage) {
							eMessage.addClass('positive').removeClass('waiting');
							eMessage.setHTML( ret.message);
						}
						actionPerformed	= true;
					}
					
					if ( !actionPerformed) {
						if ( growl) {
							growl.obj.query(':span').innerHTML	= 'Nenhuma ação foi realizada.';
							growl.obj.getFirstChild().className	= 'undefined';
						} else if ( eLabel) {
							eLabel.addClass('error').removeClass('waiting');
							eLabel.query(':em').setHTML( 'Erro no servidor');
						} else if ( eMessage) {
							eMessage.addClass('error').removeClass('waiting');
							eMessage.setHTML( 'Erro no servidor');
						}
					}
					this.ret	= ret;
					
					if ( this.position == 'center') {//como a mensagem pode alterar o tamanho da caixa, ajustemos a posição do growl
						growl.obj.setStyle({
							left: '50%',
							top: '50%',
							marginLeft: -growl.obj.offsetWidth/2,
							marginTop: -growl.obj.offsetHeight
						});
					}
					
					window.setTimeout(function(){
						if ( growl) {
							growl.hide();
						}
					}, hideAfter + req.duration);

					if ( onLoad) {
						onLoad.apply( this, arguments);
					}
				}
				req.onError	= function() {
					if ( growl ) {
						growl.obj.eImgC.setStyle('display', 'none');
						growl.obj.getFirstChild().className	= 'error';
						growl.obj.query(':span').innerHTML	= req.error || 'Erro não especificado';
					} else {
						eLabel.removeClass('spinning').removeClass('waiting');
						eLabel.query(':em').setHTML('Erro no servidor');
					}
					
					this.ret	= {};
					
					window.setTimeout(function(){
						if ( growl) {
							growl.hide();
						}
					}, hideAfter + req.duration);
					
					if ( onError) {
						onError.apply( this, arguments);
					}
				}
				req.start();
				return growl || eLabel;
			},
			create: function() {
				var sp	= jCube(document.createElement('DIV')).setHTML('<div><div class="sp-imgC" ><img src="'+ jCube.root +'../imgs/gt8/spinner-small.gif" alt="" /></div><span>&nbsp;</span></div>').addClass('growl-spinner').setStyle({
					opacity:  0,
					zIndex: 10000
				});
				sp.eLabel	= sp.query(':span');
				sp.eImgC	= sp.query(':div.sp-imgC');
				sp.eImg		= sp.query(':div.sp-imgC img');
				
				gp	= jCube(document.createElement('DIV')).setStyle({
					position: 'fixed',
					background: 'transparent',
					zIndex: 9999
				});
				
				return sp;
			},
			show: function( options) {
				var sp	= this.create();
				if ( !urC) {
					urC	= jCube(document.createElement('DIV')).addClass('growl-spinner-upper-right').appendTo( document.body);
				}
				
				if ( options.img) {
					sp.eImg.src	= options.img;
				}
				if ( options.showGlassPane) {
					var glassPaneOptions	= ({
						position: 'fixed',
						background: 'transparent',
						bounds: [0,0, window.getWidth(), window.getHeight()],
						zIndex: 9999
					}).merge(options.glassPaneOptions || {});
					gp.appendTo( document.body).setStyle(glassPaneOptions);
				}
				
				sp.eImgC.setStyle('display', options.hideImage? 'none': '');
				sp.getFirstChild().className	= options.type? options.type: '';
				if ( options.type == 'error' && !options.img) {
					sp.eImg.src	= jCube.root + '../imgs/gt8/delete-small.png';
				} else if ( !options.img) {
					sp.eImg.src	= jCube.root + '../imgs/gt8/spinner-small.gif';
				}
				
				options	= options || {};
				options.duration	= options.duration || 850;
				sp.eLabel.innerHTML	= options.label || '&nbsp;';
				sp.appendTo(document.body).fadeIn(options);
				
				if ( options.position == 'upper right') {
					sp.prependTo( urC);
					var h	= sp.offsetHeight;
					sp.setStyle('height', 1).resizeTo({
						height: h,
						duration: 1000
					});
				} else {
					sp.setStyle({
						left: '50%',
						top: '50%',
						marginLeft: -sp.offsetWidth/2,
						marginTop: -sp.offsetHeight
					});
				}
				
				var Obj	= {
					obj: sp,
					hide: function() {
						if ( options.onComplete) {
							options.onComplete.call( sp);
						}
						options.onComplete	= function() {
							if ( options.position == 'upper right') {
								this.resizeTo({
									height: 1,
									duration: 1000,
									onComplete: function() {
										this.remove();
									}
								});
							} else {
								this.remove();
							}
						}
						sp.fadeOut(options);
						
						if ( options.showGlassPane && gp.parentNode) {
							try {
								gp.remove();
							} catch(e) {
								
							}
						}
					}
				};
				sp.onclick	= Obj.hide;
				
				if ( options.hideAfter) {
					window.setTimeout(function(){
						Obj.hide();
					}, options.hideAfter + options.duration + (options.delay||0));
				}
				
				return Obj;
			},
			hide: function( options) {
				options	= options || {};
				
				jCube('::.growl-spinner').each(function(){
					if ( options.onComplete) {
						options.onComplete.call( this);
					}
					options.onComplete	= function() {
						this.remove();
					}
					this.fadeOut(options);
					
					if ( gp.parentNode) {
						try {
							gp.remove();
						} catch(e) {
							
						}
					}
				});
			}
		}
	})()
}
GT8.init();
jCube.Include("Browser");
jCube.Include("Array.contains");
jCube.Include("Array.each");
jCube.Include("Document.Cookie");
jCube.Include("Element.addClass");
jCube.Include("Element.appendTo");
jCube.Include("Element.getElementsBySelector");
jCube.Include("Element.getFirstChild");
jCube.Include("Element.getNextSibling");
jCube.Include("Element.getOption");
jCube.Include("Element.getOptionValue");
jCube.Include("Element.getPreviousSibling");
jCube.Include("Element.getParent");
jCube.Include("Element.getRealSize");
jCube.Include("Element.injectBefore");
jCube.Include("Element.remove");
jCube.Include("Element.removeClass");
jCube.Include("Element.setBackgroundPosition");
jCube.Include("Element.setFixedMask");
jCube.Include("Element.setFocus");
jCube.Include("Element.setHTML");
jCube.Include("Element.setPseudoText");
jCube.Include("Element.setSelection");
jCube.Include("Element.setStyle");
jCube.Include("Element.setValue");
jCube.Include("Element.toggleClass");
jCube.Include("Event.add");
jCube.Include("Event.remove");
jCube.Include("Function.delay");
jCube.Include("Number.format");
jCube.Include("Pluggins.Grid");
jCube.Include("Pluggins.showOverlay");
jCube.Include("Pluggins.TabbedPane");
jCube.Include("Pluggins.Switch");
jCube.Include("Server.HttpRequest");
jCube.Include("String.contains");
jCube.Include("String.startsWith");
jCube.Include("Transition.fadeIn");
jCube.Include("Transition.fadeOut");
jCube.Include("Transition.resizeTo");
jCube.Include("Window");
jCube.Include("Window.DOMReady");