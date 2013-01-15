/*!
 * jCube 2002 - 2012
 * jCube.org
 * License:		GPL-LICENSE
 * To compile, after a fully navigation in your project, type in the console:
	window.location = 'http://10.0.1.95/gt8/www/tools/jCube-compile/?filename=jCube.min&files='+ window.localStorage['jCube.Include'].split(',').join(',').substring(1);
	//i`m working on this :D
*/
var jCube	= (function( $, $$) {
	var jCube	= function( obj, selector) {
		if ( typeof obj == 'string' ) {
			if ( obj.substring(0,1)==":" ) {
				selector	=  obj;
				obj		= document.body;
			} else {
				obj		= document.getElementById(obj);
			}
		//shortcut to jCube.Window.onDOMReady for jQuery compatibility
		} else if ( typeof obj == 'function') {
			jCube.Window.DOMEvents.push(obj);
		}
		if (!obj) {
			return null;
		}
		if ( obj == window ) {
			if	( jCube.Element.methods.addEvent) {
				window.addEvent	= jCube.Element.methods.addEvent;
			}
			if ( jCube.Element.methods.removeEvent) {
				window.removeEvent	= jCube.Element.methods.removeEvent;
			}
			return obj;
		}
		if ( !obj.jCubeReady && typeof obj == 'object' ) {
			jCube.Object.merge( obj, jCube.Element.methods);
			obj.jCubeReady	= true;
		}
		if ( selector ) {
			if ( !jCube.Element.getElementsBySelector && jCube.Debug.showWarnings) {
				//alert( '"jCube.Element.getElementsBySelector" is missing! Please, load it using jCube.load("Element.getElementsBySelector") or use jCube.min.js');
			} else {
				return jCube.Element.getElementsBySelector( obj, selector);
			}
		}
		return obj;
	}
	//Object.merge is now native prototype
	jCube.Object	= {
		merge:	function ( obj, OBJ) {
			if ( !OBJ) {
				OBJ	= obj;
				obj	= this;
			}
			for ( var i in OBJ) {
				if ( !Object.prototype[i] ) {
					obj[i] = OBJ[i];
				}
			}
			return obj;
		}
	}
	jCube.Object.merge.call(jCube, {
		config: {
			alterNativePrototype: true,
			selectorAlwaysReturnArray: false,
			loadAsync: true,
			noCache: null,						/** @property {Integer} config.noCache A timestamp to be appended in the url of included files. This may be useful on browsers that refuses to update its cache and the libraries changes often */
			theme: 'default',
			trackIncludedFiles: true			/** @property {Boolean} config.trackIncludedFiles Using window.localStorage, if avaiable, all files included is tracked. This may be useful for compile all files into one.
												 * To get the files, just type in the console:
													window.location = 'http://10.0.1.95/gt8/www/tools/jCube-compile/?filename=jCube.min&files='+ window.localStorage['jCube.Include'].split(',').join(',').substring(1)
												 * @default true
												**/
		},
		Date:			{
			en:	{
				months:		["January",	"February",	"March",	"April",	"May",		"June",		"July",		"August",	"September",	"October",	"November",	"December"],
				weekdays:	["Sunday",	"Monday",	"Tuesday",	"Wednesday",	"Thursday",	"Friday",	"Saturday"],
				names:		["Day", "Days",	"Week", "Weeks", "Month", "Months", "Year", "Years"]
			},
			pt:	{
				months:		["Janeiro",	"Fevereiro",	"Mar&ccedil;o",	"Abril",	"Maio",		"junho",	"Julho",	"Agosto",	"Setembro",	"Outubro",	"Novembro",	"Dezembro"],
				weekdays:	["Domingo",	"Segunda-feira","Ter&ccedil;a-feira",	"Quarta-feira",	"Quinta-feira",	"Sexta-feira",	"S&aacute;bado"],
				names:		["Dia", "Dias",	"Semana", "Semanas", "M&ecirc;s", "Meses", "Ano", "Anos"]
			}
		},
		International:	{
			language:		'pt',
			dateLongFormat:		'%Y, %M %e - %W %T',
			dateMediumFormat:	'%Y, %b %e - %T',
			dateShortFormat:	'%Y/%b/%e - %T',
			thousandSeparator:	'.',
			decimalSeparator:	',',
			decimalWidth:		2,
			currencySimbol:		'R$',
			charset:		"UTF-8"
		},
		Debug:			{
			displayErrors:		true,
			showWarnings:		true,
			alertOnDuplicates:	false
		},
		Array:			{
			remove:	Array.prototype.remove
		},
		Color:			{
			
		},
		Document:		{
			
		},
		Event:			{
			
		},
		Element:		{
			SMP:			{},
			methods:		{},
			pMethods:		{},
			Implements:	function( props) {
				var fxName	= props.name,
					allowTags	= props.allow,
					disallowTags	= props.disallow,
					method	= props.method
				;
				if ( method) {
					jCube.Element.methods[fxName]	= method;
				}
				jCube.Element.pMethods[fxName]	= function() {
					allowTags = allowTags || '*';
					disallowTags = disallowTags || ['window'];
					for ( var i=0, crr, allow, j=0, len=this.length, len2=disallowTags.length, len3=allowTags.length; i<len; i++ ) {
						
						if ( allowTags != '*' && typeof allowTags != 'object') {
							throw new Error('"allowTags" must be an Array');
						}
						
						allow	= allowTags=='*'? true: false;
						for ( j=0; j<len2; j++) {
							
							if ( this[i].nodeName.toUpperCase() == disallowTags[j].toUpperCase()) {
								allow	= false;
								break;
							}
						}
						if ( !allow ) {
							for ( j=0; j<len3; j++) {
								if ( this[i].nodeName.toUpperCase() == allowTags[j].toUpperCase()) {
									allow	= true;
									break;
								}
							}
						}
						if ( allow && (crr = this[i][fxName].apply( this[i], arguments))) {
							
						}
					}
					return this;
				}
				return method;
			},
			//SetMethods is intented for applying DOM methods to a collection of objs html
			SetMethods:	function( fxName) {
				jCube.Element.pMethods[fxName]	= function() {
					var objs	= [];
					for ( var i=0, crr, len=this.length; i<len; i++ ) {
						if ( (crr = this[i][fxName].apply( this[i], arguments))) {
							objs[objs.length]	= crr;
						}
					}
					return this;
				};
			}
		},
		Function:		{
			
		},
		UI:				{
			
		},
		Math:			{
			
		},
		Number:			{
			
		},
		Pluggins:		{
			
		},
		Server:			{
			
		},
		String:			{
			
		},
		Time:			{
			
		},
		Transition:		{
			
		},
		Util:			{
			
		},
		Window:			{
			DOMEvents:	[]
		},
		Import:			function( url, charset) {
			charset	= charset || jCube.International.charset;
			
			if ( url.substring( url.length-4,url.length).toLowerCase() == ".css" ) {
				document.write('<link rel="stylesheet" charset="'+ charset +'" type="text/css" href="'+ url +'" />');
			} else {
				document.write('<scr' + 'ipt type="text/Javascript" src="'+ url +'" charset="'+ charset +'" ></scr' + 'ipt>');
			}
		},
		root:			(function() {
			for ( var i=0, crr, oScripts=document.getElementsByTagName("SCRIPT"); i<oScripts.length; i++) {
				crr	= oScripts[i].src.toLowerCase();
				
				if ( crr.substring(crr.length-8)=="jcube.js" || crr.substring(crr.length-12)=="jcube.min.js" || crr.substring(crr.length-15)=="jcube.single.js" ){
					var filePos	= 0;
					if ( crr.substring(crr.length-8)=="jcube.js") {
						filePos	= 8;
					} else if ( crr.substring(crr.length-12)=="jcube.min.js") {
						filePos	= 12;
					} else if ( crr.substring(crr.length-15)=="jcube.single.js") {
						filePos	= 15;
					}
						
					crr	= oScripts[i].src.substring(0,crr.length-filePos);
					if(document.location.protocol=="https:"){
						crr=crr.replace(/^http:/,"https:")
					}
					return crr;
				}
			}
			return "";
		})(),
		/**
		 * @method Include( url, charset, async, onLoad)
		 * @description Includes javascript libraries and css files on the fly in the current page. To load a jCube item library use "Class.library" format. E.g.: String.toInteger
		 * @param {String} url The javascript or css file source
		 * @param {String} charset The character set used in the source @default jCube.International.charset
		 * @param {Boolean} async Indicates if javascript files must be loaded asynchronous or synchronous @default jCube.config.loadAsync
		 * @param {Listener} onLoad Fires when the script is loaded asynchronously
		 * @method Include( url, options)
		 * @description Alias to jCube.Include( url, charset, async, onLoad)
		 * @method Include( options) Alias to jCube.Include( url, charset, async, onLoad)
		 * @description Alias to jCube.Include( url, charset, async, onLoad)
		 **/
		Include:		(function(jCube) {
			return jCube.Object.merge.call(
				(function(  url, charset, async, onLoad){
					var options	= {};
					if ( typeof url == 'object') {
						url	= option.url;
					} else if ( typeof charset == 'object') {
						options	= charset;
						options.url	= url;
					}
					charset	= options.charset;
					async	= options.async;
					onLoad	= options.onLoad;
					charset	= charset || jCube.International.charset;
					
					if ( jCube.Include.compiled && url.substring( url.length-4).toLowerCase() != ".css") {
						jCube.Include.files[url]	= {type: "js"}
						return;
					}
					
					var parsedURL	= jCube.Include.ParseUrl( url),
						sURL	= parsedURL[0],
						jsURL	= parsedURL[1],
						cssURL	= parsedURL[2],
						nocache	= (jsURL && jCube.config.noCache? (jsURL.indexOf("?")>-1?"&":"?") + jCube.config.noCache: "")
					;
					
					if ( !jCube.Include.files[url] ) {
						var includeThis	= url!="Util.CreateSingleFile";
						var OBJ	= {};
						
						if ( jsURL ) {
							//if allowed in config, the js files included using jCube.Include will be tracked and stored in window.localStorage['jCube.Include']
							if ( jCube.config.trackIncludedFiles && window.localStorage) {
								var storage	= (window.localStorage['jCube.Include'] || '').split(',');
								var found	= -1;
								for ( var i=0; i<storage.length; i++) {
									if ( storage[i] == url) {
										found	= i;
										break;
									}
								}
								if ( found == -1) {
									storage.push( url);
									window.localStorage['jCube.Include']	= storage.sort(function(a, b){
										if (a < b) {
											return -1;
										} else if ( b < a) {
											return 1;
										}
										return 0;
									}).join(',');
									storage	=
									i	=
									found	= null;
								}
							}
							/**
							 * @callback {String} jCube.Include.callback(url) A callback to modify an url provided by the jCube.Include function
							*/
							if ( jCube.Include.callback) {
								jsURL	= jCube.Include.callback( jsURL);
							}
							if ( async===false || (async==null && (async==false||this.config.loadAsync==false)) ) {
								document.write("<scr" + "ipt charset=\""+ charset +"\" type=\"text/javascript\" id=\""+ url +"\" src=\""+ jsURL + (nocache) +"\" ></scr" + "ipt>");
							} else {
								
								//load async... pending
								var eScript	= document.createElement('SCRIPT');
								eScript.setAttribute('type', 'text/javascript');
								eScript.type	= 'text/javascript';
								eScript.src	= jsURL + nocache;
								eScript.charset	= charset;
								eScript.id		= url;
								eScript.duration	= new Date().getTime();
								eScript.onreadystatechange	=
								eScript.onload	=
								function() {
									
									var loaded	= false;
									for ( var i=0; i<jCube.Include.asyncFiles.length; i++) {
										if ( jCube.Include.asyncFiles[i] == this && (!this.readyState || this.readyState=='complete' || this.readyState=='loaded')) {
											jCube.Include.asyncFiles.splice( i, 1);
											loaded	= true;
											break;
										}
									}
									if ( loaded) {
										/**
										 * @listener jCube.Include.onLoad( duration) An event listener called when the script is loaded
										 * @remark This listener is called only when jCube.config.loadAsync is set to true. The ScriptElement is referenced by 'this' operator
										 **/
										if ( onLoad) {
											onLoad.call( this, this);
										}
										if ( jCube.Include.onLoad) {
											jCube.Include.onLoad.call( this, new Date().getTime()-this.duration);
										}
										//all scripts has been loaded successfully
										if ( jCube.Include.asyncFiles.length == 0) {
											if ( jCube.Window.DOMEvents.awaitingFiles && jCube.Window.DOMReady && jCube.Window.DOMReady.onDOMReady) {
												jCube.Window.DOMReady.onDOMReady();
											}
										}
									}
								}
								eScript.async	= true;
								var eRootScript	= document.getElementsByTagName('SCRIPT')[0];
								eRootScript.parentNode.insertBefore( eScript, eRootScript);
								OBJ.type	= "js";
								OBJ.url	= jsURL;
								jCube.Include.asyncFiles.push(eScript);
							}
						} else if ( cssURL) {
							if ( async===false || (async==null && (async==false||this.config.loadAsync==false)) ) {
								document.write('<link rel="stylesheet" charset="'+ charset +'" type="text/css" href="'+ cssURL +'" />');
							} else {
								var eLink	= document.createElement('LINK');
								eLink.setAttribute('type', 'text/css');
								eLink.setAttribute('href', cssURL);
								eLink.setAttribute('rel', 'stylesheet');
								eLink.type	= 'text/css';
								eLink.href	= cssURL;
								eLink.rel	= 'stylesheet';
								eLink.id		= url;
								eLink.charset	= charset;
								
								eLink.async	= true;
								document.getElementsByTagName('HEAD')[0].appendChild( eLink);
								
								window.eLink	= eLink
							}
							OBJ.type	= "css";
							OBJ.url	= cssURL;
							includeThis	= false;
						}
						
						if ( includeThis ) {
							jCube.Include.files[url]	= OBJ;
							jCube.Include.count++;
						}
					} else if ( jCube.Debug.alertOnDuplicates ) {
						alert( "The following package \""+ url +"\" has already been loaded!" );
					}
				}), {
				ParseUrl:	function ( url) {
					var	sURL	= null,
						cssURL	= null,
						jsURL	= null
					;
					
					if ( url.substring( url.length-4,url.length).toLowerCase() == ".css" ) {
						cssURL	= jCube.root + url;
						sURL	= cssURL.toLowerCase();
					} else {
						if ( url.substring( url.length-3,url.length).toLowerCase() == ".js" ) {
							jsURL	= jCube.root + url;
							sURL	= jsURL.toLowerCase();
						} else {
							if ( url.indexOf(".*") > -1 ) {
								var nome	= url.substring(0, url.indexOf("."));
								var files	= jCube.Include[nome];
								for	( var f=1; f<files.length; f++) {
									urls.push( nome +"."+ files[f]);
								}
								url	= nome +"."+ files[0];
							}
							if ( url.indexOf(".") == -1 ) {
								url		= url +"."+ url;
							} else {
								url		= url + url.substring( url.lastIndexOf("."));
							}
							jsURL	= jCube.root + url.replace(/\./g, "/") +".js";
							sURL	= jsURL.toLowerCase();
						}
					}
					sURL	= sURL.replace( /\//g, "_");
					sURL	= sURL.replace( /\./g, "D");
					
					return [ sURL, jsURL, cssURL, url];
				},
				files:	{},
				count:	1,	//include jCube.js
				asyncFiles:	[]
			});
		})(jCube)
	});
	//for compatibility :P
	jCube.load	= jCube.Include;
	/***************************************************************************
	*                                                                          *
	*                       UTILITIES                                          *
	*                                                                          *
	***************************************************************************/
	(function(){//support for DOMReady events. Still needs to load jCube.Window.DOMReady!
		// A fallback to window.onload, that will always work
		if ( window.addEventListener ) {
			window.addEventListener( "load", function(){
				jCube.Window.DOMEvents.awaitingFiles	= true;
				if ( jCube.Window.DOMReady && jCube.Window.DOMReady.onDOMReady) {
					jCube.Window.DOMReady.onDOMReady();
				}
			}, true);
		} else {
			window.attachEvent( "onload", function(){
				jCube.Window.DOMEvents.awaitingFiles	= true;
				if ( jCube.Window.DOMReady && jCube.Window.DOMReady.onDOMReady) {
					jCube.Window.DOMReady.onDOMReady();
				}
			});
		}
		//The world would be better if only...
		if ( document.addEventListener ) {
			document.addEventListener( "DOMContentLoaded", function() {
				document.removeEventListener( "DOMContentLoaded", arguments.callee, false);
				jCube.Window.DOMEvents.awaitingFiles	= true;
				if ( jCube.Window.DOMReady && jCube.Window.DOMReady.onDOMReady) {
					jCube.Window.DOMReady.onDOMReady();
				}
			}, false );
		} else if ( document.attachEvent ) {
			// ensure firing before onload, maybe late but safe also for iframes
			document.attachEvent( "onreadystatechange", function() {
				if ( document.readyState === "complete" ) {
					document.detachEvent( "onreadystatechange", arguments.callee);
					jCube.Window.DOMEvents.awaitingFiles	= true;
					if ( jCube.Window.DOMReady && jCube.Window.DOMReady.onDOMReady) {
						jCube.Window.DOMReady.onDOMReady();
					}
				}
			});
			
			// If IE and not an iframe continually check to see if the document is ready
			if ( document.documentElement.doScroll && window == window.top ) (function(){
				if ( jCube.Window.DOMReady && jCube.Window.DOMReady.isDOMReady ) {
					return;
				}
				
				try {
					// If IE is used, use the trick by Diego Perini
					//http://javascript.nwbox.com/IEContentLoaded/
					document.documentElement.doScroll("left");
				} catch( error ) {
					setTimeout( arguments.callee, 0 );
					return;
				}
				// and execute any waiting functions
				jCube.Window.DOMEvents.awaitingFiles	= true;
				if ( jCube.Window.DOMReady && jCube.Window.DOMReady.onDOMReady) {
					jCube.Window.DOMReady.onDOMReady();
				}
			})();
		}
	})();
	if ( jCube.config.alterNativePrototype) {
		Object.prototype.merge	= jCube.Object.merge;
		Array.prototype.isArray		= true;
		String.prototype.isString	= true;
		Number.prototype.isNumber	= true;
		Function.prototype.isFunction	= true;
	}
	window.$	= $ || function ( obj) {
		return jCube(":"+ obj);
	};
	window.$$	= $$ || function ( obj) {
		return jCube("::"+ obj);
	};
	return jCube;
})( window.$, window.$$);
