jCube.Include("Array.each");
jCube.Include("Element.addClass");
jCube.Include("Element.getData");
jCube.Include("Element.getOption");
jCube.Include("Element.getOptionValue");
jCube.Include("Element.getParent");
jCube.Include("Element.removeClass");
jCube.Include("Element.setData");
jCube.Include("Element.setFocus");
jCube.Include("Element.setProperty");
jCube.Include("Element.setSelection");
jCube.Include("Event");
jCube.Include("Event.add");
jCube.Include("Event.trigger");
jCube.Include("Pluggins.TabbedPane");
jCube.Include("String.contains");
jCube.Include("Window.DOMReady");

jCube(function(){
	Editor.setEvents();
	
	if ( ASP.locked) {
		Editor.setAllReadOnly( true);
	}
	
	if ( jCube( ":.TabbedPane")) {
		jCube( ":.TabbedPane").onTabbedPaneOpen	= function( e, tab, card) {
			var label	= tab.getFirstChild().innerHTML.toLowerCase();
			if ( label.contains('log')) {
				//tab.getFirstChild().query(':#tab-history div.find input').setFocus();
			} else if ( label.contains('admin')) {
				jCube('::.Switch').each(function(){
					this.getData('UI.Switch').update();
				});
			}
			
			if ( typeof ASP.tabIndex != 'number') {
				jCube.Document.Cookie.set( 'admin-'+ ASP.cardListerName +'-crrTabIndex', tab.getNodeIndex(), 0.5);
			}
			window.setTimeout(function(){
				if ( card.query(':input')) {
					card.query(':input').setFocus().setSelection();
				}
			}, 150);
		}
		jCube( ":.TabbedPane").onTabbedPaneInitialization	= function( e, tabbedPane) {
			if ( typeof ASP.tabIndex == 'number') {
				tabbedPane.openTab( ASP.tabIndex);
			} else {
				tabbedPane.openTab( Number(jCube.Document.Cookie.get('admin-'+ ASP.cardListerName +'-crrTabIndex')||0));
			}
		}
		
		//pode ser que o TabbedPane já tenha sido inicializado. Neste caso, dispare o evento de inicialização novamente
		if ( jCube(':.TabbedPane').getData('UI.TabbedPane')) {
			jCube( ":.TabbedPane").onTabbedPaneInitialization(null, jCube(':.TabbedPane').getData('UI.TabbedPane'));
		}
		
		//overflow-y
		jCube('::.Editor-TabbedPaneC .TabbedPane > .body ').setStyle('height', window.getHeight() - (
			jCube(':header.admin').offsetHeight + 
			jCube(':.Editor-TabbedPaneC .TabbedPane').offsetTop * 2 + //margin top and bottom
			jCube(':.Editor-TabbedPaneC .TabbedPane > .header').offsetHeight +
			jCube(':.Editor-TabbedPaneC .TabbedPane > .body').getComputedStyle('padding-top').toInteger(0) +
			jCube(':.Editor-TabbedPaneC .TabbedPane > .body').getComputedStyle('padding-bottom').toInteger(0)
		));
	}
});
var Editor	= {
	id: 0,
	changes:		[],
	setEvents:		function( selector) {
		jCube(selector || '::input.gt8-update, textarea.gt8-update, select.gt8-update').each(function(){
			this.onchange	= Editor.updateField;
			this.onblur		= Editor.validate;
			this.onkeyup	= Editor.validate;
			
			var value	= this.value;
			if ( this.type == 'checkbox') {
				value	= this.checked? 1: 0;
			} else if ( this.nodeName == 'SELECT') {
				value	= this.getOptionValue();
			}
			this.setData('Editor::lastSavedValue', value);
		}).addEvent('onkeyup', function(e){
			if ( e.key == 13) {
				this.trigger('onchange');
			}
		});
	},
	validate:		function() {
		if ( !this.getData('Editor::validate::firstTime')) {
			this.setData('Editor::validate::firstTime', true);
			//return null;
		}
		
		var
			n	= jCube(this).name,
			v	= this.value,
			l	= v.length,
			label	= this.getParent('label')
		;
		function __SetStatus( status, mssg) {
			var em	= label.removeClass('positive').removeClass('error').addClass(status).query(':em');
			if ( em) {
				em.setHTML(mssg||label.messageDefault||'&nbsp;');
			}
		}
		//validação
		if ( label) {
			if ( label.className.contains('required') && !v.length) {
				__SetStatus('error', 'campo obrigatório');
				return false;
			} else {
				var minLength	= 
					maxLength	= label.title
				;
				minLength	= minLength && minLength.match(/minlength\:([0-9]+)/)? Number(minLength.match(/minlength\:([0-9]+)/)[1]): false;
				maxLength	= maxLength && maxLength.match(/maxlength\:([0-9]+)/)? Number(maxLength.match(/maxlength\:([0-9]+)/)[1]): false;
				
				if ( minLength && l < minLength) {
					__SetStatus('error', 'valor muito curto');
					return false;
				} else if ( label.className.contains('required')) {
					__SetStatus('positive', label.messageOk);
				}
			}
		}
		return true;
	},
	validateAll:	function() {
		jCube('::input.gt8-update, textarea.gt8-update, select.gt8-update').trigger('onkeyup');
	},
	updateField:	function(e) {
		if ( !Editor.validate.call( this)) {
			return null;
		}
		
		var eInput	= this;
		var name	= this.name;
		var value	= this.value;
		
		//páginas .Editor-new-item não devem ter campos atualizados
		if ( jCube(':.Editor-new-item')) {
			return null;
		}
		
		if ( this.type == 'checkbox') {
			value	= this.checked? 1: 0;
		} else if ( this.nodeName == 'SELECT') {
			value	= this.getOptionValue();
		}
		
		if ( this.type != 'radio' ) {
			if ( this.getData('Editor::lastSavedValue') == value ) {
				return null;
			}
		}
		//validate datetime
		if ( this.className.contains('fixed-mask') && this.className.contains('date-time') ) {
			//tem realmente o formato correto para datas, pelo menos yyyy/mm/dd?
			if ( /[0-9]{4}\-[0-9]{2}\-[0-9]{2}/.test(value.replace(/\//g, '-'))) {
				value	= value.replace(/\//g, '-')
			} else if ( value == '') {
				value	= '0000-00-00';
			} else {
				return null;
			}
		}
		
		Editor.req	= Editor.req || new jCube.Server.HttpRequest({
			method: jCube.Server.HttpRequest.HTTP_POST,
			noCache: true,
			onError: function() {
				Editor.isRequesting	= false;
				GT8.Spinner.hide();
			},
			onLoad:	function( ) {
				Editor.isRequesting	= false;
				Editor.changes.push([this.name, this.value]);
				
				if ( this.ret.value) {
					eInput.setValue( v[1]);
				}
				
				if ( Editor.onRequestComplete) {
					Editor.onRequestComplete( this.responseText);
				}
			}
		});
		Editor.req.name		= name;
		Editor.req.value	= value;
		
		
		
		var req	= Editor.req;
		req.url	= "?action=update&opt=update&format=JSON";
		
		if ( Editor.onBeforeUpdate) {
			var obj	= Editor.onBeforeUpdate( name, value, this, req);
			if ( !obj) {
				return this;
			}
			name	= obj[0];
			value	= obj[1];
		} else if ( window.ASP ) {
			req.addGet( 'sql', ASP.cardListerName + '.update');
		}
		var id	= ASP.id;
		
		//auto grab custom id
		if ( eInput.getParent('.gt8-update-id')) {
			var id	= (eInput.getParent('.gt8-update-id').id +'');
			id	= id? id.match(/[0-9]+$/): [0];
			if ( id && id[0]) {
				id	= id[0];
			} else {
				id	= 0;
			}
		}
		if ( !id && window.ASP) {
			id	= ASP.id;
		}
		if ( id) {
			req.addGet('id', id);
		}
		
		req.addGet( 'field', name);
		req.addGet( 'value', value);
		GT8.Spinner.request( req);
		Editor.isRequesting	= true;
		
		this.setData('Editor::lastSavedValue', value);
		
		return null;
	},
	setAllReadOnly:	function( b) {
		if ( b==null && jCube(':input[name=locked]')) {
			b	= jCube(':input[name=locked]').checked;
		}
		
		if ( b ) {
			jCube('::input.gt8-update, select.gt8-update, textarea.gt8-update').addClass('readonly').setProperty('readOnly', true).setProperty('disabled', true);
			jCube('::select.gt8-update').each(function(){
				if ( this.getParent().className.contains('e-select')) {
					this.getParent().query(':span.group-button').addClass('readonly');
				}
			});
			jCube('::.imgC').addClass('locked-1');
			jCube('::.imgC .glass-lock').setStyle({
				'opacity': 0.3,
				display: 'block'
			});
		} else {
			jCube('::input.gt8-update, select.gt8-update, textarea.gt8-update').removeClass('readonly').setProperty('readOnly', false).setProperty('disabled', false);
			jCube('::select.gt8-update').each(function(){
				if ( this.getParent().className.contains('e-select')) {
					this.getParent().query(':span.group-button').removeClass('readonly');
				}
			});
			jCube('::.imgC').removeClass('locked-1');
			jCube('::.imgC .glass-lock').setStyle('display', 'none');
		}
		try {
			if ( jCube(':.imgC iframe') && jCube(':.imgC iframe').contentWindow && jCube(':.imgC iframe').contentWindow.document.getElementById('img-delete-bt')) {
				jCube(':.imgC iframe').contentWindow.document.getElementById('img-delete-bt').style.visibility	= b? 'hidden': '';
			}
		} catch (E) {
			
		}
	},
	createNew:		function() {
		
		Editor.validateAll();
		
		if ( Editor.createNew.__tinserting && new Date().getTime()-Editor.createNew.__tinserting < 5000) {
			return;
		}
		Editor.createNew.__tinserting	= new Date().getTime();
		var eRequireds	= jCube('::.input-validation label.required');
		for ( var i=0, len=eRequireds.length; i<len; i++) {
			if ( !eRequireds[i].className.contains('positive')) {
				GT8.Spinner.show({
					label: 'Por favor,<br /> preencha todos os campos necessários',
					type: 'error',
					hideImage: true,
					hideAfter: 3000
				});
				eRequireds[i].query('::input,select,textarea')[0].trigger('onkeyup').trigger('updateValue').setFocus().setSelection();
				return;
				break;
			}
		}
		
		var req	= new jCube.Server.HttpRequest({
			method: jCube.Server.HttpRequest.HTTP_POST,
			noCache: true,
			url: "?opt=new&format=JSON&sql=new&action=new",
			onLoad:	function() {
				
				var ret	= GT8.onGeneralRequestLoad.call( this, null, true);
				Editor.createNew.ret	= ret;
				
				if ( Editor.createNew.onLoad) {
					Editor.createNew.onLoad(this.responseText);
				}
			}
		});
		
		jCube('::input.gt8-update, textarea.gt8-update, select.gt8-update').each(function(){
			if ( this.nodeName == 'SELECT') {
				req.addGet(this.name, this.getOptionValue());
			} else {
				req.addGet(this.name, this.value);
			}
		});
		req.position	= 'center';
		GT8.Spinner.request( req);
	},
	Log: {
		search: function( eSearch) {
			var trs	= this.trs || (this.trs=jCube('::#card-history table tr'));
			var q	= eSearch.value.toLowerCase();
			
			for ( var i=1, tr, v0, v1, v3, v4, len=trs.length; i<len; i++) {
				tr	= trs[i];
				
				v0	= tr.v0 || (tr.v0 = tr.cells[0].innerHTML.toLowerCase());
				v1	= tr.v1 || (tr.v1 = tr.cells[1].innerHTML.toLowerCase());
				v3	= tr.v3 || (tr.v3 = tr.cells[3].innerHTML.toLowerCase());
				v4	= tr.v4 || (tr.v4 = tr.cells[4].innerHTML.toLowerCase());
				
				if ( q=='' || v0.contains(q) || v1.contains(q) || v3.contains(q) || v4.contains(q)) {
					tr.style.display	= '';
				} else {
					tr.style.display	= 'none';
				}
			}
		}
	},
	temp: null
}
