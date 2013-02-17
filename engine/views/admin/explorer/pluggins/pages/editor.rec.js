		
		<script type="text/javascript" >
		//<![CDATA[
			
			jCube(function(){
				jCube(':h1').setHTML('Page ##title##');
				jCube(':textarea[name=description]').getParent('label').addClass('hidden');
				
				//Editor das páginas
				window.addEvent('onresize', function(){
					var eEditor		= jCube(':#eArticleEditor');
					var eEditorC	= eEditor.getParent();
					var eCardBody	= jCube(':.Editor-TabbedPaneC .body');
					var isHidden	= eEditor.className.contains('hidden');
					eEditor.removeClass('hidden');
					eEditor.setStyle('height',
						eCardBody.offsetHeight -
						eCardBody.getComputedStyle('padding-top').toInteger() -
						eCardBody.getComputedStyle('padding-bottom').toInteger() -
						eEditorC.getComputedStyle('padding-top').toInteger() -
						eEditorC.getComputedStyle('padding-bottom').toInteger()
					);
					jCube('::iframe.DivEditor').setStyle({
						width: eEditor.offsetWidth,
						height: eEditor.offsetHeight
					});
					if ( isHidden) {
						eEditor.addClass('hidden');
					}
				});
				jCube(':#eArticleEditor').trigger.call( window, 'onresize');
				
				//Ações do editor
				jCube(':#ePublishAttach').appendTo('ePublishC').removeClass('hidden');
				
				var ArticleEditor	= new	DivEditor({
					eEditor: jCube(':#eArticleEditor'),
					eBtsC: jCube(':#ePublishAttach'),
					eBts: jCube('::#ePublishAttach button'),
					eSelects: jCube('::#ePublishAttach select.editor-fontSize,#ePublishAttach select.editor-formatBlock'),
					ePreventLosingFocus: jCube('::#ePublishAttach *,#ePublishAttach'),
					ePreventIgnore: jCube('::#ePublishAttach .editor-link *,#ePublishAttach .editor-image *'),
					eLinkEditor: jCube(':#ePublishAttach .editor-link'),
					eImgEditor: jCube(':#ePublishAttach .editor-image'),
					
					//listeners
					onChange: function() {
						jCube(':#eSaveArticleButton').fadeIn();
						window.onbeforeunload	= function() { return 'Tem certeza que deseja sair sem salvar as alteraçõs no artigo?';}
					}
				});
				jCube( ":.TabbedPane").addEvent('onTabbedPaneOpen', function( E, tab, card){
					if ( card.className.contains('article')) {
						this.trigger.call( window, 'onresize');
						jCube(':#ePublishAttach').removeClass('hidden');
						jCube(':#ePublishC .img-preview').addClass('hidden');
					} else {
						jCube(':#ePublishAttach').addClass('hidden');
						jCube(':#ePublishC .img-preview').removeClass('hidden');
					}
				}).trigger('onTabbedPaneOpen', jCube( ":.TabbedPane .header > .tab.over"), jCube( ":.TabbedPane .body > .card.over"));
				jCube(':#eSaveArticleButton').appendTo(jCube(':header.admin nav.toolbar')).removeClass('hidden').setStyle('opacity', 0.1).addEvent('onclick', function(E){
					E.stop();
					var req	= new jCube.Server.HttpRequest({
						url: '?action=update&id='+ ASP.id,
						method: jCube.Server.HttpRequest.HTTP_POST,
						noCache: true,
						onComplete: function(){
							jCube(':#eSaveArticleButton').fadeOut({
								opacity: 0.1
							});
							window.onbeforeunload	= null;
						}
					});
					req.addGet('field', 'description');
					req.addPost('value', ArticleEditor.getContents());
					GT8.Spinner.request(req);
				});
			});
			
			var DivEditor	= function( options){
				var
					eEditor				= options.eEditor,
					eBtsC				= options.eBtsC,
					eBts				= options.eBts,
					eSelects			= options.eSelects,
					ePreventLosingFocus	= options.ePreventLosingFocus,
					ePreventIgnore		= options.ePreventIgnore,
					eLinkEditor			= options.eLinkEditor
					eImgEditor			= options.eImgEditor
				;
				var iFrame	= null;
				var focused	= false;
				var storage	= {
					sel: null,
					range: null,
					content: eEditor.innerHTML
				};
				var Instance	= this;
				
				this.getContents	= function(){
					return iFrame.contentDocument.body.innerHTML;
				}
				this.hasChanges	= function(){
					return storage.content != iFrame.contentDocument.body.innerHTML;
				}
				this.getSelectedNodesByRange	= (function() {//range methods by some one named Tim: http://stackoverflow.com/questions/7781963/js-get-array-of-all-selected-nodes-in-contenteditable-div
					var GetNextNode	= function(node) {
						if (node.hasChildNodes()) {
							return node.firstChild;
						} else {
							while (node && !node.nextSibling) {
								node = node.parentNode;
							}
							if (!node) {
								return null;
							}
							return node.nextSibling;
						}
					}
					var GetRangeSelectedNodes	= function(range) {
						var node = range.startContainer;
						var endNode = range.endContainer;
					
						// Special case for a range that is contained within a single node
						if (node == endNode) {
							return [node];
						}
						// Iterate nodes until we hit the end container
						var rangeNodes = [];
						while (node && node != endNode) {
							rangeNodes.push( node = GetNextNode(node) );
						}
						// Add partially selected nodes at the start of the range
						node = range.startContainer;
						while (node && node != range.commonAncestorContainer) {
							rangeNodes.unshift(node);
							node = node.parentNode;
						}
					
						return rangeNodes;
					}
					return (function(){
						if ( window.getSelection) {
							var sel = iFrame.contentWindow.getSelection();
							
							if ( sel.isCollapsed) {
								if ( sel.rangeCount) {
									var range	= sel.getRangeAt(0);
									return [range.commonAncestorContainer];
								}
							} else {
								return GetRangeSelectedNodes(sel.getRangeAt(0));
							}
						}
						return [];
					});
				})();
				this.getFirstFoundNode	= function() {
					var sel	= iFrame.contentWindow.getSelection? iFrame.contentWindow.getSelection(): iFrame.contentDocument.selection;
					var eFocus	= null;
					if ( sel.isCollapsed && sel.rangeCount) {
						eFocus	= sel.getRangeAt(0).commonAncestorContainer;
					} else if ( sel.rangeCount){
						eFocus	= sel.getRangeAt(0).startContainer;
					}
					if ( eFocus.nodeType == 3) {
						eFocus	= eFocus.parentNode;
					}
					if ( eFocus) {
						return eFocus;
					}
					return iFrame;
				}
				this.getFocusNode	= function() {
					var eNodes	= this.getSelectedNodesByRange();
					var eFocus	= null;
					for ( var i=0; i<eNodes.length; i++) {
						if ( eNodes[i].nodeType != 3 ) {
							eFocus	= eNodes[i];
							break;
						}
					}
					if ( !eFocus) {
						eFocus	= eEditor;
					}
					if ( eFocus.nodeType == 3) {
						eFocus	= eFocus.parentNode;
					}
					return eFocus;
				}
				this.getSelectedText	= function(){
					return window.getSelection? window.getSelection().toString(): window.selection +'';
				}
				this.execCommand	= function( command){
					var eFocus	= this.getFocusNode();
					if ( eFocus) {
						var eBlock	= eFocus;
						while ( eBlock && !['DIV','P', 'PRE', 'BODY','H1','H2','H3','H4','H5','H6'].contains(eBlock.nodeName) ) {
							eBlock	= eBlock.parentNode;
						}
						
						iFrame.contentWindow.focus();
						
						switch( command) {
							case 'createLink': {
								iFrame.contentDocument.execCommand( command, null, '#');
								EditLink();
								break;
							}
							case 'openImageEditor': {
								OpenImageEditor(EditImage());
								break;
							}
							default: {
								iFrame.contentDocument.execCommand( 'styleWithCSS', null, true);
								if ( command.startsWith('fontSize-')) {
									if ( command == 'fontSize-none') {
										if ( eFocus) {
											eFocus.style.fontSize	= null;
										}
									} else {
										iFrame.contentDocument.execCommand( command.split('-')[0], null, command.split('-')[1]);
									}
								} else if ( command.startsWith('formatBlock-')) {
									var tag	= command.split('-')[1];
									iFrame.contentDocument.execCommand( command.split('-')[0], null, '<'+ tag +'>');
								} else {
									iFrame.contentDocument.execCommand( command);
								}
								break;
							}
						}
					}
				};
				//private functions
				function CheckBtByCommand( command) {
					if ( iFrame.contentDocument.queryCommandState(command)) {
						var obj	= eBtsC.query(':.editor-'+ command);
						if ( obj) {
							obj.addClass('selected');
						}
					}
				}
				function checkCommandStatus(E) {
					eBts.removeClass('selected');
					
					CheckBtByCommand('italic');
					CheckBtByCommand('bold');
					CheckBtByCommand('underline');
					CheckBtByCommand('strikeThrough');
					CheckBtByCommand('justifyLeft');
					CheckBtByCommand('justifyCenter');
					CheckBtByCommand('justifyRight');
					CheckBtByCommand('justifyFull');
					CheckBtByCommand('insertOrderedList');
					CheckBtByCommand('insertUnorderedList');
					
					var selection	= Instance.getSelectedText();
					var eNodes		= Instance.getSelectedNodesByRange();
					var eNode		= Instance.getFocusNode();
					var eFocus		= Instance.getFirstFoundNode();
					var eBlock		= eFocus;
					while ( eBlock && !['DIV','P', 'BODY','H1','H2','H3','H4','H5','H6'].contains(eBlock.nodeName) ) {
						eBlock	= eBlock.parentNode;
					}
					
					//has Hyperlink?
					var eHyper	= null;
					for ( var i=0; i<eNodes.length; i++) {
						if ( eNodes[i]) {
							if ( eNodes[i].nodeName == 'A' ) {
								eHyper	= eNodes[i];
								break;
							} else if ( eNodes[i].parentNode.nodeName=='A') {
								eHyper	= eNodes[i].parentNode;
								break;
							}
						}
					}
					if ( !eHyper) {
						for ( var i=0; i<eNodes.length; i++) {
							if ( jCube(eNodes[i]).getParent('a')) {
								eHyper	= jCube(eNodes[i]).getParent('a');
								break;
							}
						}
					}
					if ( eHyper) {
						EditLink(eHyper);
					} else {
						eLinkEditor.addClass('hidden');
					}
					//has Image?
					var eImg	= null;
					for ( var i=0; i<eNodes.length; i++) {
						if ( eNodes[i]) {
							if ( eNodes[i].nodeName == 'IMG' ) {
								eImg	= eNodes[i];
								break;
							} else if ( eNodes[i].parentNode.nodeName=='IMG') {
								eImg	= eNodes[i].parentNode;
								break;
							}
						}
					}
					if ( eImg) {
						EditImage(eImg);
					} else {
						eImgEditor.addClass('hidden');
					}
					if ( eFocus ) {//fontSize
						var size	= eFocus.style.fontSize;
						size	= size=='xx-small'? 1: (size=='x-small'?2:(size=='small'?3:(size=='medium'?4:(size=='large'?5:(size=='x-large'?6:(size=='xx-large'?7:('none')))))));
						eBtsC.query(':select.editor-fontSize').setValue('editor-fontSize-'+ size).trigger('updateValue');
					}
					if ( eBlock ) {//formatBlock
						var nodeName	= eBlock.nodeName.toLowerCase();
						if ( !eBtsC.query(':select.editor-formatBlock option[value=editor-formatBlock-'+ nodeName +']')) {
							nodeName	= 'div';
						}
						eBtsC.query(':select.editor-formatBlock').setValue('editor-formatBlock-'+ nodeName).trigger('updateValue');
					}
				}
				var timeoutRangeSaved	= new Date().getTime();
				function saveSelection() {
					//evitemos duplos cliques para não perder a seleção correta
					if ( new Date().getTime()-timeoutRangeSaved	< 250) {
						return;
					}
					timeoutRangeSaved	= new Date().getTime();
					
					//a seleção de texto deve ser dentro do editor
					var eNode	= Instance.getSelectedNodesByRange()[0];
					//com iframes, isso parece não ser mais necessário
					//if ( eNode != eEditor && !jCube(eNode).getParent('.DivEditor')) { return;}
					if ( eNode ) {
						if ( iFrame.contentWindow.getSelection) {
							storage.sel		= iFrame.contentWindow.getSelection();
							storage.range	= storage.sel.getRangeAt(0).toString()? storage.sel.getRangeAt(0): storage.range;
						} else if (iFrame.contentDocument.selection) {
							storage.sel		= iFrame.contentWindow.selection;
							storage.range	= storage.sel.createRange();
						}
					}
				}
				function restoreSelection() {
					if ( storage.range != null) {
						if (window.getSelection) {
							storage.sel.removeAllRanges();
							storage.sel.addRange(storage.range);
						} else if ( document.createRange) {
							window.getSelection().addRange(storage.range);
						} else if (document.selection) {
							storage.range.select();
						}
					}
					window.setTimeout(function(){
						if ( storage.range != null) {
							if (window.getSelection) {
								storage.sel.removeAllRanges();
								storage.sel.addRange(storage.range);
							} else if ( document.createRange) {
								window.getSelection().addRange(storage.range);
							} else if (document.selection) {
								storage.range.select();
							}
							storage.range	= null;
						}
						iFrame.contentWindow.focus();
					}, 80);
				}
				function EditLink( eLink) {
					if ( !eLink ) {
						eLink	= Instance.getSelectedNodesByRange();
						for ( var i=0; i<eLink.length; i++) {
							if ( eLink[i].nodeName == 'A') {
								eLink	= eLink[i];
								break;
							}
						}
						if ( eLink) {
							jCube(eLink).addEvent('onclick', function(E){
								EditLink(this);
							});
						}
					}
					if ( eLink && eLink.href) {
						for ( var i=0; i<eBts.length; i++) {//select bt hyperlink
							if ( eBts[i].className.contains('editor-createLink')) {
								eBts[i].addClass('selected');
								break;
							}
						}
						eLinkEditor.removeClass('hidden');
						var eInput	= eLink;
						if ( eLinkEditor.nodeName != 'INPUT') {
							eInput	= jCube(eLinkEditor).query(':input');
						}
						eInput.value	= eLink.getAttribute('href');
						eInput.crrLink	= eLink;
					}
				}
				function EditImage( eImg) {
					if ( !eImg ) {
						var src	= '{{CROOT}}imgs/gt8/newfile-regular.png';
						var range	= iFrame.contentWindow.getSelection().getRangeAt(0);
						range.collapse(false);
						var eImg	= document.createElement('IMG');
						eImg.src	= src;
						eImg.alt	= '[nova imagem]';
						range.insertNode(eImg);
						
						jCube(eImg).addEvent('onclick', function(E){
							EditImage(this);
						}).addEvent('ondblclick', function(E){
							OpenImageEditor(this);
						});
					}
					if ( eImg && eImg.src) {
						for ( var i=0; i<eBts.length; i++) {//select bt hyperlink
							if ( eBts[i].className.contains('openImageEditor')) {
								eBts[i].addClass('selected');
								break;
							}
						}
						eImgEditor.removeClass('hidden');
						eImgEditor.query(':input[name=editor-img-src]').value	= eImg.getAttribute('src');
						eImgEditor.query(':select[name=editor-img-align]').setProperty('value', eImg.style.cssFloat || 'none').trigger('updateValue');
						var size		= eImg.src.contains('?')? eImg.src.substringIndex('?',-1).substringIndex('&'): 'default';
						if ( ['default', 'small', 'regular', 'preview'].contains(size)) {
							eImgEditor.query(':select[name=editor-img-size]').getParent('label').removeClass('hidden');
							eImgEditor.query(':select[name=editor-img-size]').setProperty('value', size).trigger('updateValue');
						} else {
							eImgEditor.query(':select[name=editor-img-size]').getParent('label').addClass('hidden');
						}
						eImgEditor.query(':input[name=editor-img-margin]').setProperty('value', (eImg.style.marginTop||0) +" "+ (eImg.style.marginRight||0) +" "+ (eImg.style.marginBottom||0) +" "+ (eImg.style.marginLeft||0)).trigger('updateValue');
						eImgEditor.crrImage	= eImg;
					}
					return eImg;
				}
				function OpenImageEditor( eImg, path) {
					Modal.show({
						objRef: null,
						onChoose: function( eA, ASP, mado){
							if ( eA && eA.query) {
								if ( eA.query(':.hidden .type').innerHTML == 'directory') {
									return;
								}
								if ( eImg) {
									eImg.src	= '{{CROOT}}downloads/'+ eA.query(':.hidden .path').innerHTML + eA.query(':.hidden .filename').innerHTML +'/?regular';
								}
								Modal.hide();
							}
						},
						url: path?path: ASP.padmin +'explorer/images/?headbar=0&locationbar=1&toolbar=0'
					});
				}
				{//initialization
					(function(){//IFRAME
						iFrame	= jCube(document.createElement('IFRAME')).setStyle({
							width: eEditor.offsetWidth,
							height: eEditor.offsetHeight
						}).injectAfter(eEditor).addClass('DivEditor');
						eEditor.addClass('hidden');
						iFrame.contentWindow.document.open();
						iFrame.contentWindow.document.write(storage.content);
						iFrame.contentWindow.document.close();
						iFrame.contentWindow.document.body.contentEditable	= true;
						iFrame.contentWindow.document.designMode	= 'On';
					})();
					(function(){//LISTENERS
						jCube(iFrame.contentDocument).addEvent('onkeyup', function(E){
							if ( this.body.innerHTML != storage.content) {
								if ( options.onChange) {
									options.onChange.call( Instance);
								}
							}
						}).addEvent('onmouseup', function(E){
							if ( this.body.innerHTML != storage.content) {
								if ( options.onChange) {
									options.onChange.call( Instance);
								}
							}
						});
					})();
					//EVENTS
					var saved	= false;
					ePreventLosingFocus.addEvent('onmousedown', function(E){
						if ( ePreventIgnore.contains(this) || ePreventIgnore.contains(E.target) ) {
							return;
						}
						if ( E.target.className.contains('editor-') || jCube(E.target).getParent('A') && E.target.getParent('A').className.contains('editor-')) {
							//saved	= true;
						} else {
							saveSelection();
							window.setTimeout(function(){
								restoreSelection();
								checkCommandStatus();
							}, 80);
						}
					}).each(function(){
						this.unselectable	= 'on';
						this.unSelectable	= 'on';
					});
					var OnClick	= function(E){
						if ( this.className && this.className.contains('editor-')) {
							var command	= this.className.match(/editor\-([a-zA-Z0-9\_\-]+)/);
							if ( !command || !command[1]) {
								return null;
							}
							command	= command[1];
							if ( this.nodeName == 'SELECT') {
								command	= this.getOptionValue();
								command	= command.substring(command.indexOf('-')+1);
							}
							Instance.execCommand(command);
						}
						if ( !saved) {
							saveSelection();
							restoreSelection();
						}
						saved	= false;
					}
					eBts.addEvent('onclick', OnClick);
					eSelects.addEvent('onchange', OnClick);
					jCube(eEditor).addClass('DivEditor');
					jCube(iFrame.contentDocument).addEvent('onkeyup', checkCommandStatus).addEvent('onmouseup', checkCommandStatus);
					var chronBlur	= new jCube.Time.Chronometer({
						onComplete: function(){
							if ( focused==false) {
								iFrame.removeClass('editor-focused');
								eBts.removeClass('selected');
							}
						}
					});
					jCube(iFrame.contentWindow).addEvent('onblur', function(){
						focused	= false;
						chronBlur.start(300);
					}).addEvent('onfocus', function(){
						iFrame.addClass('editor-focused');
						focused	= true;
					});
					(function(){//LINK
						var eInput	= eLinkEditor;
						if ( eLinkEditor.nodeName != 'INPUT') {
							eInput	= jCube(eLinkEditor).query(':input');
						}
						eInput.addEvent('onblur', function(){
							eLinkEditor.addClass('hidden');
						}).addEvent('onchange', function(E){
							
							if ( this.crrLink ) {
								this.crrLink.href	= this.value;
								this.crrLink	= null;
							}
							
						});
					})();
					(function(){//IMAGES
						eImgEditor.query('::input[name=editor-img-src]').addEvent('onchange', function(E){
							if ( eImgEditor.crrImage ) {
								eImgEditor.crrImage.src	= this.value;
							}
						});
						eImgEditor.query('::select[name=editor-img-align]').addEvent('onchange', function(E){
							if ( eImgEditor.crrImage ) {
								eImgEditor.crrImage.setStyle('float', this.getOptionValue());
							}
						});
						eImgEditor.query('::select[name=editor-img-size]').addEvent('onchange', function(E){
							if ( eImgEditor.crrImage ) {
								eImgEditor.crrImage.src	= eImgEditor.crrImage.getAttribute('src').replace(/\?default|\?preview|\?regular|\?small/, '?'+ this.getOptionValue());
							}
						});
						eImgEditor.query('::input[name=editor-img-margin]').addEvent('onchange', function(E){
							if ( eImgEditor.crrImage ) {
								var margin	= this.value.match(/(\S+)[px\.\,\ ]?(\S+)[px\.\,\ ]?(\S+)[px\.\,\ ]?(\S+)[px\.\,\ ]?/);
								margin[1]	= (margin[1] || '0').toInteger() +'px';
								margin[2]	= (margin[2] || '0').toInteger() +'px';
								margin[3]	= (margin[3] || '0').toInteger() +'px';
								margin[4]	= (margin[4] || '0').toInteger() +'px';
								eImgEditor.crrImage.style.margin	= margin[1] +' '+ margin[2] +' '+ margin[3] +' '+ margin[4];
							}
						});
						jCube(iFrame.contentDocument).query('::img').addEvent('onclick', function(E){
							EditImage(this);
						}).addEvent('ondblclick', function(E){
							OpenImageEditor(this);
						});
					})();
				};
				return this;
			}
		//]]>
		</script>
		<script type="text/javascript" src="{{AROOT}}js/Modal.js" ></script>