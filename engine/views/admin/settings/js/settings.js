jCube(function(){//MODAL
	var eModal		= jCube(':#eModalEditor');
	var eName		= jCube(':#eModalEditor input[name=name]');
	var eValue		= jCube(':#eModalEditor input[name=value]');
	var eRead		= jCube(':#eModalEditor select[name=read]');
	var eWrite		= jCube(':#eModalEditor select[name=write]');
	var eFeedback	= jCube(':#eModalEditor .feedback');
	var eBtConfirm	= jCube(':#eModalEditor a.href-button-ok');
	var eBtCancel	= jCube(':#eModalEditor a.href-button-cancel');
	
	var id	= 0;
	var crrDiv	= null;
	
	var Update	= function() {
		crrDiv	= this;
		
		id	= this.id.substring(6);
		eModal.appendTo( document.body);
		
		var name	= '';
		var value	= '';
		var read	= 0;
		var write	= 0;
		
		if ( this.query('::div')[0]) {
			name	= this.query('::div')[0].innerHTML;
			value	= this.query('::div')[1].innerHTML;
			read	= this.query('::div')[2].title;
			write	= this.query('::div')[3].title;
		}
		
		eName.setValue(name).trigger('keyup');
		eValue.setValue(value).trigger('keyup');
		eRead. setProperty('value', read).trigger('onchange');
		eWrite.setProperty('value', write).trigger('onchange');
		eFeedback.setHTML('&nbsp;').removeClass('error');
		
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
				eName.setFocus();
			}
		});
		
	}
	jCube('::#eResults .body > div').addEvent('onclick', function(E) {
		eName.readOnly	= true;
		var eFirst	= this.getFirstChild();
		var eLast	= this.getLastChild();
		
		if ( this.className.contains('delete')) {
			if ( E.target === eFirst) {
				return;
			} else if ( E.target === eLast) {
				return;
			}
		}
		Update.call( this);
		E.stop();
	});
	eName.addEvent('onkeydown', function(E) {
		if ( E.key == 13 ) {
			//close mado
		}
	});
	eValue.addEvent('onkeydown', function(E){
		if ( E.key == 13) {
			window.setTimeout(function(){
				eBtConfirm.trigger('onclick', E);
			}, 50);
		}
	});
	eBtCancel.addEvent('onclick', function(E) {
		E.stop();
		
		if ( crrDiv.id === 'param-0' ) {
			crrDiv.remove();
		}
		
		eModal.showOverlay();
	});
	eBtConfirm.addEvent('onclick', function(E){
		E.stop();
		
		//request
		var req	= new jCube.Server.HttpRequest({
			url: '?action=update-param&format=JSON&id='+ id,
			noCache: true,
			method: jCube.Server.HttpRequest.HTTP_POST,
			onLoad: function() {
				var ret	= GT8.onGeneralRequestLoad.call( this, {}, true);
				eFeedback.addClass('spinner-small-hidden').setHTML('&nbsp;');
				
				if ( ret.error) {
					eFeedback.addClass('error').addClass('spinner-small-hidden').setHTML(ret.error);
				} else {
					crrDiv.query('::div')[0].innerHTML	= eName.value;
					crrDiv.query('::div')[1].innerHTML	= eValue.value;
					crrDiv.query('::div')[2].innerHTML	= eRead.getOption().innerHTML;
					crrDiv.query('::div')[3].innerHTML	= eWrite.getOption().innerHTML;
					eModal.showOverlay();
					eFeedback.addClass('spinner-small-hidden').setHTML(ret.message +'&nbsp;');
					
					xx=ret
					console.log(ret)
					if ( ret.insertId) {
						crrDiv.id	= 'param-'+ ret.insertId;
					}
				}
			},
			onError: function() {
				eFeedback.addClass('error').addClass('spinner-small-hidden').setHTML('Não foi possível alterar o valor agora!');
			}
		});
		req.addGet('name', eName.value);
		req.addGet('value', eValue.value);
		req.addGet('read', eRead.getOptionValue());
		req.addGet('write', eWrite.getOptionValue());
		req.start();
		eFeedback.removeClass('spinner-small-hidden').removeClass('error').setHTML('<img src="'+ ASP.CROOT +'imgs/gt8/spinner.gif" alt="" class="float-left" />Alterando valor...');
	});
	jCube(':#eBtAdd').addEvent('onclick', function(E){
		E.stop();
		
		eName.readOnly	= false;
		
		var div	= jCube(document.createElement('DIV')).addClass('grid-12 marginless').setProperty('id', 'param-0');
		div.innerHTML	= '<div class="grid-3" ></div><div class="grid-3" ></div><div class="grid-3" title="0" ></div><div class="grid-3" title="0" ></div>';
		div.appendTo( jCube(':#eResults .body'));
		Update.call( div);
		
		div.addEvent('onclick', function(E){
			eName.readOnly	= true;
			Update.call( this);
			E.stop();
		});
		
	});
	jCube(':#eBtDel').addEvent('onclick', function(E){
		E.stop();
		
		var divs	= jCube('::#eResults .body > div');
		if ( divs[0] && divs[0].className.contains('delete')) {
			divs.removeClass('delete').removeClass('choose');
		} else {
			divs.addClass('delete');
		}
	});
	var Event_RowsFirstCell	= function(E) {
		E.stop();
		
		if ( this.getParent().className.contains('delete') ) {
			if ( this.getParent().className.contains('choose')) {
				this.getParent().removeClass('choose');
			} else {
				this.getParent().addClass('choose');
			}
		}
	}
	var Event_RowsLastCell	= function(E) {
		E.stop();
		
		if ( this.getParent().className.contains('delete') && this.getParent().className.contains('choose') ) {
			var eRow	= this.getParent();
			var eCell	= this;
			var req	= new jCube.Server.HttpRequest({
				url: '?action=delete-param&id='+ this.getParent().id.substring(6) +"&format=JSON",
				onComplete: function(){
					if ( this.ret.affected) {
						var bounds	= eCell.getOffset(document.body);
						GT8.poof( bounds.left, bounds.top, function(){
							eRow.remove();
						});
					} else {
						
					}
				}
			});
			GT8.Spinner.request( req);
		}
	}
	jCube('::#eResults .body > div > div:first-child').addEvent('onclick', Event_RowsFirstCell, true);
	jCube('::#eResults .body > div > div:last-child').addEvent('onclick', Event_RowsLastCell, true);
});