
jCube(function() {//navigation
	
	var eC	= jCube(':#eC');
	var template	= '<a id="" href="#" class="grid grid-fixed" ><strong>title</strong><span class="imgC" ><img src="imgPath?regular" alt="" /></span><span class="hidden tamanho" ></span><span class="hidden path" >%path%</span><span class="overlay" ></span></a>';
	var startLevel	= -1;
	var dirName		= null;
	
	jCube(':#eBackward').addEvent('onclick', function(E){//bt backwards
		E.stop();
		
		this.trigger('checkBackwardState');
		
		if ( this.__storageInitilized) {
			var loc	= (window.location +'').split('/');
			if ( loc[loc.length-2].startsWith('storage') && loc[loc.length-3]+'/' == '{{GT8:admin.root}}' ) {
				return;
			}
			
			var Qsa		= jCube.Document.getHttpVariables();
			var qsa		= '?';
			Qsa.each(function(){
				qsa	+= '&'+ this[0] +'='+ this[1];
			});
			qsa	= qsa==='?' || qsa==='?&'? '': qsa;
			
			var path	= '../'+ qsa;
			history.pushState({id:path}, 'Storage', path);
			window.onpopstate();
		} else {
			this.__storageInitilized	= true;
		}
	}).addEvent('checkBackwardState', function(){
		var loc	= (window.location +'').split('/');
		if ( loc[loc.length-2].startsWith('storage') && loc[loc.length-3]+'/' == '{{GT8:admin.root}}' ) {
			jCube(':#eBackward').addClass('disabled');
		} else {
			jCube(':#eBackward').removeClass('disabled');
		}
		
		//location state
		//find start
		var eAs	= jCube('::nav.directory a');
		var pathIncr	= '';
		for( var i=0, crr, found=false; i<eAs.length; i++) {
			crr	= eAs[i];
			if ( found) {
				crr.remove();
			} else if ( crr.innerHTML.toLowerCase() === 'storage') {
				found	= i;
				pathIncr	= crr.getAttribute('href');
			}
		}
		for( var i=0, crr, locs=loc, zIndex=locs.length, found=false; i<locs.length-1; i++) {
			crr	= locs[i];
			if ( found) {
				pathIncr	+= crr +'/';
				jCube(document.createElement('A')).
					appendTo( jCube(':nav.directory')).
					addClass('button').
					setStyle('z-index', zIndex--).
					setHTML(crr).setProperty('pushStatePath', pathIncr).
					addEvent('onclick', function(E){
						//E.stop();
						//jStorage.getContents();
					}).
					href	= pathIncr
				;
			} else if ( crr.toLowerCase() === 'storage') {
				found	= true;
			}
		}
		loc.pop();
		
	}).trigger('onclick');
	
	jStorage.paths.pop();
	window.onpopstate	= function() {
		jCube(':#eC').fadeOut({
			duration: 250,
			onComplete: function(){
				jStorage.getContents();
			}
		});
		jCube(':#eBackwardC').fadeOut({
			duration: 250
		});
	}
	jStorage.getContents	= function( eCrrDir) {
		var level;
		var crootM	= '';
		var path = window.location +'';
		var qsa	= path.contains('?')? path.substring(path.indexOf('?')): '';
		
		if ( eCrrDir) {
			path	= eCrrDir.query(':span.path').innerHTML;
			
			level	= path.split('/').length;
			for ( var i=0; i<level-1; i++) {
				crootM	+= '../';
			}
			startLevel	= startLevel!==-1? startLevel: level;
			history.pushState({filename:path}, 'Storage', crootM + path +'/'+ qsa);
		} else {
			path	= path.contains('?')? path.substring(0, path.indexOf('?')): path;
			path	= 'storage'+ path.substringIndex('gt8-admin/storage', -1);
			
			//"storage/matriz"
			path	= path === 'storage/'? 'storage': path.substring(0, path.length-1);
			
			crootM	= '../';
			level	= path.split('/').length;
			startLevel	= startLevel!==-1? startLevel: level;
		}
		for ( var i=0; i<level; i++) {
			crootM	+= '../';
		}
		startLevel	= startLevel? startLevel: level;
		
		//return;
		var html	= [];
		for ( var i=0, crr; i<jStorage.paths.length; i++) {
			crr	= jStorage.paths[i];
			
			//console.log([ (path +'/'+ crr[3]).toLowerCase(), crr[0].toLowerCase(), (crr[0].split('/').length === level + 1)]);
			if ( (path +'/'+ crr[3]).toLowerCase() === crr[0].toLowerCase() && crr[0].split('/').length === level + 1 ) {
				html.push(
					template.
					replace('id=""', 'id="'+ crr[4] +'"').
					replace('%path%', crr[0]).
					replace('title', crr[1]).
					replace('imgPath', (level<4? crootM + crr[2]: crootM +'imgs/gt8/blank.gif'))
				);
			}
		}
		//console.log([html.length]);
		var is3x	= false;
		if ( html.length == 1) {
			html	= html.join('').replace(/grid-fixed/g, 'grid-12');
		} else if ( html.length == 2) {
			html	= html.join('').replace(/grid-fixed/g, 'grid-6');
		} else if ( html.length == 3) {
			html	= html.join('').replace(/grid-fixed/g, 'grid-3');
		} else if ( html.length > 4) {
			html	= html.join('').replace(/grid-fixed/g, 'grid-fixed');
			is3x	= true;
		}
		eC.innerHTML	= html;
		
		jCube('::#eC span.imgC img').each(function(){
			GT8.adjustImgSize( this);
			this.getParent('a').addEvent('onclick', OnCardClick)
		});
		
		if ( is3x) {
			var crrL	= null;
			var lastL	= null;
			jCube(':#eResults').setStyle('width', 10000).setStyle('max-width', '10000px');
			jCube(':#eC').setStyle('width').setStyle('float', 'left');
			jCube('::#eC a strong').each(function(){
				crrL	= this.innerHTML.charAt(0);
				lastL	= lastL===null? crrL: lastL;
				if ( lastL !== crrL ) {
					jCube(document.createElement('DIV')).addClass('clearfix').injectAfter( this.getParent('a'))
					lastL	= this.innerHTML.charAt(0);
				}
			});
			window.setTimeout(function(){
				jCube(':#eC').setStyle('width', jCube(':#eC').offsetWidth+20);
				jCube(':#eBackwardC').setStyle('width', jCube(':#eC').offsetWidth);
			}, 100);
			window.setTimeout(function(){
				jCube(':#eC').setStyle('float', 'none');
			}, 250);
			window.setTimeout(function(){
				jCube(':#eResults').setStyle('width', '').setStyle('max-width', '');
			}, 500);
		} else {
			jCube(':#eC').setStyle('width', '');
			jCube(':#eBackwardC').setStyle('width', '');
		}
		jCube(':#eC').fadeIn({
			duration: 450,
			delay: is3x? 500: 0
		});
		jCube(':#eBackwardC').fadeIn({
			duration: 450,
			delay: is3x? 500: 0
		});
		
		//LABEL BACKWARDS
		jCube(':#eBackwardLabel').setHTML( path.split('/')[path.split('/').length-1] +' <small>(voltar)</small>');
		if ( level===5) {
			jCube(':#eBackwardLabel').addClass("text-uppercase");
		} else {
			jCube(':#eBackwardLabel').removeClass("text-uppercase");
		}
		
		//CARREGAR aJax
		if ( is3x) {
			jStorage.load( eCrrDir);
		} else if ( level === 5) {
			//exibir os produtos no último level
			if ( !dirName ) {
				dirName	= (window.location+'').split('/');
				dirName	= dirName[dirName.length-2];
			}
			var processImgs	= function() {
				html	= [];
				for ( var i=0, crr, rows=jStorage.load.cache; i<rows.length; i++) {
					crr	= rows[i];
					if ( crr.filename.toLowerCase() === dirName.toLowerCase()) {
						html.push(
							template.
							replace('href="#"', 'href="../'+ crootM +'{{GT8:admin.root}}explorer/'+ (crr.fullpath.replace('{{GT8:explorer.root}}', '')) +'?edit&tabIndex=5"').
							replace('%path%', crr.fullpath).
							replace('<span class="imgC" >', '<span class="imgC grid-6" >').
							replace('<span class="hidden tamanho" ></span>', '<span class="grid-6 tamanho" >'+ crr.tamanho +'</span>').
							replace('title', crr.fullpath.substringIndex('/', -5).substringIndex('/', 4)).
							replace('imgPath', '../'+ crootM + crr.fullpath.substringIndex('/', 6).replace('?regular', '?small'))
						);
					}
				}
				eC.innerHTML	= html.join('').replace(/grid-fixed/g, 'grid-6');
			}
			if ( jStorage.load.cache) {
				processImgs();
			} else {
				jStorage.load( null, processImgs);
			}
		}
		jCube(':#eBackward').trigger('checkBackwardState');
	}
	function OnCardClick(E) {
		E.stop();
		
		if ( parent && parent != window && parent.Modal && parent.Modal.onStorageChoose) {
			if ( this.query(':.hidden.path').innerHTML.split('/').length === 5) {
				parent.Modal.onStorageChoose( this.id, this);
				return;
			}
		}
		
		var crr	= this;
		dirName	= this.query(':strong').innerHTML;
		jCube(':#eC').fadeOut({
			duration: 250,
			onComplete: function(){
				jStorage.getContents(crr);
			}
		});
		jCube(':#eBackwardC').fadeOut({
			duration: 250
		});
	}
	jStorage.load	= function( eCrrDir, onComplete) {
		
		var req	= new jCube.Server.HttpRequest({
			url: '?action=get-products-in&format=JSON',
			onComplete: function(){
				var results;
				if ( this.responseText.contains('//#error')) {
					
				} else {
					eval(this.responseText);
				}
				
				if ( results && results.length) {
					jStorage.load.cache	= results;
					for ( var i=0, j, crr, eL, found, cards=jCube('::#eC a strong'); i<cards.length; i++) {
						crr	= cards[i].innerHTML.toLowerCase();
						found	= false;
						
						for ( j=0; j<results.length; j++) {
							if ( crr === results[j].filename.toLowerCase()) {
								eL	= cards[i].getParent().getLastChild();
								eL.setStyle('height', (cards[i].getParent().offsetHeight/14) + eL.offsetHeight);
								found	= true;
							}
						}
					}
					if ( onComplete) {
						onComplete();
					}
				}
			}
		});
		
		req.start();
		
	}
	
	jStorage.getContents(null);
	
});
