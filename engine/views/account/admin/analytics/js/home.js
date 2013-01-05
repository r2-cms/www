jCube.Include('Array.contains');
jCube.Include('Array.del');
jCube.Include('Document.getHttpVariables');
jCube.Include('Element.appendTo');
jCube.Include('Element.addClass');
jCube.Include('Element.getOffset');
jCube.Include('Element.getParent');
jCube.Include('Element.getNodeIndex');
jCube.Include('Element.remove');
jCube.Include('Element.setProperty');
jCube.Include('Element.toggleClass');
jCube.Include('Number.toTime');
jCube.Include('Transition.fadeIn');
jCube.Include('Transition.fadeOut');

jCube(function() {
	jCube('::.Grid a').setProperty('onclick', Pager.click);
	
	Pager.click._groups	= function( eA, crr, goTo) {
		if ( crr) {
			var groups	= jCube.Document.getHttpVariables().get('groups');
			if ( groups) {
				groups	= groups.split(',');
				if ( groups.contains(crr)) {
					groups.del(crr);
				} else {
					groups.push(crr);
				}
				goTo	= Pager.parse( 'groups', groups.join(','), true);
			} else {
				goTo	= Pager.parse( 'groups', crr, true);
			}
		}
		return goTo;
	}
	Pager.click._whr	= function( eA, crr, goTo, qs, E) {
		//E.stop();
		if ( crr) {
			var get	= jCube.Document.getHttpVariables().get('groups');
			if ( get) {
				get	= get.split(',');
				
				var whr	= [];
				var main	= eA.getParent().parent;
				main.eHead.query('::.g-col').each(function(){
					
					//console.log(this.title.substringIndex('|')+" : "+ get.join(','));
					switch ( this.title.substringIndex('|').toLowerCase()) {
						case 'login': {
							if ( get.contains('user')) {
								whr.push('whr[]=user|gt8|'+ main.eBody.query('::.g-col')[this.getParent().getNodeIndex()].query('::div>*')[eA.getNodeIndex()].textContent);
							}
							break;
						}
						case 'creation': {
							if ( get.contains('month')) {
								whr.push('whr[]=month|gt8|'+ main.eBody.query('::.g-col')[this.getParent().getNodeIndex()].query('::div>*')[eA.getNodeIndex()].textContent);
							}
							if ( get.contains('day')) {
								whr.push('whr[]=day|gt8|'+ main.eBody.query('::.g-col')[this.getParent().getNodeIndex()].query('::div>*')[eA.getNodeIndex()].textContent);
							}
							break;
						}
					}
					
				});
				
				goTo	= Pager.parse( 'whr[]', 'poli&'+whr.join('&').replace('/','-'), true);
				goTo	= Pager.parse( 'groups', '', true, goTo);
			} else {
				goTo	= Pager.parse( 'whr', crr, true);
			}
		} else {
			//goTo	= Pager.parse( 'whr', null, true);
			alert(666);
		}
		alert(eA +"\n"+ crr +"\n"+ goTo);
		return goTo;
	}
	
	//CELL FORMATTING
	jCube('::.Grid .head .g-col').each(function(){
		if ( this.className.contains('timestamp')) {
			this.parent.eBody.query('::.g-col')[ this.getParent().getNodeIndex()].query('::div > small').each(function(){
				
				if ( this.innerHTML) {//avoid emp
					this.innerHTML	= (this.innerHTML.toInteger()*1000).toTime((this.innerHTML.toInteger()>86400?'%ed ': '')+'%t');
				}
			});
		}
	});
	
	//WHERE FILTERS
	(function(jCube){
		var combos	= {};
		var glassPane	= jCube(document.createElement('DIV')).addClass('glass-pane').addEvent('onclick', function(e){
			this.remove();
			jCube(document.body).query('::.combo-filter-where').fadeOut({
				duration: 450,
				onComplete: function(){
					this.remove();
				}
			});
		});
		
		function ShowFilters(e) {
			e.stop();
			var name	= this.getParent().title.substringIndex('|');
			
			if ( combos[name]) {
				combos[name].appendTo( document.body);
			} else {
				/***************************************************************
				 *                       COMBO                                 *
				 **************************************************************/
				combos[name]	= jCube(document.createElement('DIV')).addClass('combo-filter-where').setStyle('opacity', 0);
				var html	= '<ul class="checkbox folder" >';
				var qs		= (jCube.Document.getHttpVariables().get(name)+'').split(',');
				for ( var i=0, crr; i<Filters[name+'s'].length; i++) {
					crr	= Filters[name+'s'][i];
					
					html	+= '<li><a href="?'+ name +'='+ escape(crr[0]) +'" class="'+ (qs.contains(crr[0])?'checked':'') +'" ><span>'+ crr[0] +'</span><small>('+ crr[1] +')</small></a></li>';
				}
				html	+=  '</ul>';
				/***************************************************************
				 *                       GROUPS                                *
				 **************************************************************/
				html	+= '<hr />';
				html	+= '<ul class="checkbox folder" ><li><a href="?group='+ name +'" class="" >Agrupar</a></li></ul>';
				
				combos[name].innerHTML	= html + '<a class="submit href-button href-button-ok" href="?filters" ><span>Filtrar</span></a>';
				jCube(combos[name]).appendTo(document.body).query('::li a').addEvent('onclick', function(e){
					e.stop();
					this.toggleClass('checked');
				});
				/***************************************************************
				 *                       SUBMIT                                *
				 **************************************************************/
				jCube(combos[name]).query('::a.submit').addEvent('onclick', function(e){
					var qs	= [];
					this.getParent().query('::li a.checked span').each(function(){
						qs.push(this.innerHTML);
					});
					
					var goTo	= goTo	= Pager.parse( name, qs.join(','), true);
					
					this.href	= goTo;
				});
			}
			
			glassPane.appendTo(document.body);
			
			combos[name].setStyle({
				left: this.getParent().getOffset(document.body).left,
				top: this.getParent().getOffset(document.body).bottom + 5
			}).fadeIn( 450);
		}
		for ( var i=0, list=['ip', 'login', 'browser', 'os', 'page', 'referrer'], hasFilter=null; i<list.length; i++) {
			hasFilter	= false;
			if ( jCube(':.Grid .head .g-col[title^="'+ list[i] +'|"]')) {
				
				hasFilter	= jCube.Document.getHttpVariables().get(list[i]);
				jCube(document.createElement('A')).
					addClass('whr-filter href-button bg-linear-light '+ (hasFilter?'href-button-ok':'href-button-light ') +'').
					setHTML('<span><img src="'+ ASP.CROOT +'imgs/gt8/filter-small.png" alt="" /></span>').
					addEvent('onclick', ShowFilters).
					appendTo(jCube(':.Grid .head .g-col[title^="'+ list[i] +'|"]')).
					href	= '#'
				;
			}
		}
	})(jCube);
});
