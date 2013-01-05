jCube.Include('Array.contains');
jCube.Include('Array.remove');
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
	
	Pager.click._user	= function( eA, crr, goTo) {
		if ( crr) {
			var groups	= jCube.Document.getHttpVariables().get('user');
			if ( groups) {
				groups	= groups.split(',');
				if ( groups.contains(crr)) {
					groups.remove(crr);
				} else {
					groups.push(crr);
				}
				goTo	= Pager.parse( 'user', groups.join(','), true);
			} else {
				goTo	= Pager.parse( 'user', crr, true);
			}
		}
		return goTo;
	}
	Pager.click._level	= function( eA, crr, goTo) {
		if ( crr) {
			var groups	= jCube.Document.getHttpVariables().get('level');
			if ( groups) {
				groups	= groups.split(',');
				if ( groups.contains(crr)) {
					groups	= groups.remove(crr);
				} else {
					groups.push(crr);
				}
				goTo	= Pager.parse( 'level', groups.join(','), true);
			} else {
				goTo	= Pager.parse( 'level', crr, true);
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
	Pager.click._date	= function( eA, crr, goTo) {
		if ( crr) {
			var groups	= jCube.Document.getHttpVariables().get('date');
			if ( groups == crr) {
				crr	= '';
			}
			goTo	= Pager.parse( 'date', crr, true);
			
			goTo	= Pager.parse( 'date-from', '', false, goTo);
			goTo	= Pager.parse( 'date-to', '', false, goTo);
			
		}
		return goTo;
	}
	Pager.click._status	= function( eA, crr, goTo) {
		if ( crr) {
			var groups	= jCube.Document.getHttpVariables().get('status');
			if ( groups) {
				groups	= groups.split(',');
				if ( groups.contains(crr)) {
					groups.remove(crr);
				} else {
					groups.push(crr);
				}
				goTo	= Pager.parse( 'status', groups.join(','), true);
			} else {
				goTo	= Pager.parse( 'status', crr, true);
			}
		}
		return goTo;
	}
	
	jCube(':#eFilter-cpf').addEvent('onkeyup', function(E){
		if ( E.key == 13) {
			var val		= this.value.match(/[0-9\.\-]+/);
			val			= val && val[0]? val[0]: '';
			val			= val != '.'? val: '';
			var goTo	= Pager.parse( 'cpf', val);
			
			window.location	= goTo;
		}
	}).setFixedMask('###.###.###-##');
	jCube('::#eFilter-order, #eFilter-name, #eFilter-login, #eFilter-stt, #eFilter-city').addEvent('onkeyup', function(E){
		if ( E.key == 13) {
			window.location	= Pager.parse( this.name, this.value);
		}
	});
	jCube(':#eFilter-cnpj').addEvent('onkeyup', function(E){
		if ( E.key == 13) {
			var val		= this.value.match(/[0-9\.\/\-]+/);
			val			= val && val[0]? val[0]: '';
			val			= val != '.'? val: '';
			var goTo	= Pager.parse( 'cnpj', val);
			
			window.location	= goTo;
		}
	}).setFixedMask('##.###.###/####-##');
	jCube('::#eFilter-date-from, #eFilter-date-to').addEvent('onkeyup', function(E){
		if ( E.key == 13) {
			var val		= this.value.match(/[0-9\/]+/);
			val			= val && val[0]? val[0]: '';
			val			= val != '/'? val: '';
			var goTo	= Pager.parse( this.name, val);
			//elimine as datas, se existir
			goTo	= Pager.parse( 'date', '', true, goTo);
			
			window.location	= goTo;
		}
	}).setFixedMask('##/##/####');
	
});
