			jCube(function(){
				jCube(':h1').setHTML('Page ##title##');
				
				//Editor das páginas
				window.addEvent('onresize', function(){
					jCube(':#eArticleEditor').setStyle('height', 0);
					jCube(':#eArticleEditor').setStyle('height',
						jCube(':.Editor-TabbedPaneC .body').offsetHeight -
						jCube(':.Editor-TabbedPaneC .body').getComputedStyle('padding-top').toInteger() -
						jCube(':.Editor-TabbedPaneC .body').getComputedStyle('padding-bottom').toInteger() -
						jCube(':#eArticleEditor').getParent().offsetHeight
					);
				});
				jCube(':#eArticleEditor').trigger.call( window, 'onresize');
				
				//Ações do editor
				jCube(':#ePublishAttach').appendTo('ePublishC').removeClass('hidden');
				
				new	DivEditor({
					eEditor: jCube(':#eArticleEditor'),
					eBts: jCube('::#ePublishAttach button'),
					ePreventLosingFocus: jCube('::#ePublishAttach *,#ePublishAttach'),
					ePreventIgnore: jCube('::#ePublishAttach .editor-link *'),
					eLinkEditor: jCube(':#ePublishAttach .editor-link')
				});
			});
			
			var DivEditor	= function( options){
				var eLinkEditor			= options.eLinkEditor,
					eEditor				= options.eEditor,
					eBts				= options.eBts,
					ePreventLosingFocus	= options.ePreventLosingFocus,
					ePreventIgnore		= options.ePreventIgnore
				;
				var focused	= false;
				var storage	= {
					sel: null,
					range: null
				};
				var Instance	= this;
				
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
							var sel = window.getSelection();
							
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
				this.getFocusNode	= function() {
					//busque o primeiro display:block (eg: div,p) e coloque text-align:left
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
					if ( eFocus != eEditor && !jCube(eFocus).getParent('.DivEditor')) {
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
						while ( eBlock && !['DIV','P', 'BODY'].contains(eBlock.nodeName) ) {
							eBlock	= eBlock.parentNode;
						}
						switch( command) {
							case 'createLink': {
								
								document.execCommand( command, null, '#');
								EditLink( jCube(this.getFocusNode()));
								break;
							}
							default: {
								document.execCommand( 'styleWithCSS', null, true);
								document.execCommand( command);
								break;
							}
						}
					}
				};
				{//initialization
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
					eBts.addEvent('onclick', function(E){
						this.__hasRangeEvent	= true;
						if ( this.className && this.className.contains('editor-')) {
							var command	= this.className.match(/editor\-([a-zA-Z0-9\_\-]+)/);
							if ( !command || !command[1]) {
								return null;
							}
							command	= command[1];
							Instance.execCommand(command);
						}
						if ( !saved) {
							saveSelection();
							restoreSelection();
						}
						saved	= false;
					});
					jCube(eEditor).addClass('DivEditor').addEvent('onkeyup', checkCommandStatus).addEvent('onmouseup', checkCommandStatus);
					var chronBlur	= new jCube.Time.Chronometer({
						onComplete: function(){
							if ( focused==false) {
								eEditor.removeClass('editor-focused');
								eBts.removeClass('selected');
							}
						}
					});
					jCube(eEditor).addEvent('onblur', function(){
						focused	= false;
						chronBlur.start(300);
					}).addEvent('onfocus', function(){
						this.addClass('editor-focused');
						focused	= true;
					});
					//LINK
					jCube(eLinkEditor).addEvent('onblur', function(){
						this.addClass('hidden');
					});
					jCube(eEditor).query('::a').addEvent('onclick', function(E){
						EditLink(this);
					});
				};
				//private functions
				function CheckBtByCommand( command) {
					if ( document.queryCommandState(command)) {
						for ( var i=0, len=eBts.length; i<len; i++) {
							if ( eBts[i].className.contains('editor-'+ command)) {
								eBts[i].addClass('selected');
								break;
							}
						}
					}
				}
				function checkCommandStatus(E) {
					eBts.removeClass('selected');
					//console.log([eNode, style.fontStyle]);
					CheckBtByCommand('italic');
					CheckBtByCommand('bold');
					CheckBtByCommand('underline');
					CheckBtByCommand('strikeThrough');
					CheckBtByCommand('justifyLeft');
					CheckBtByCommand('justifyCenter');
					CheckBtByCommand('justifyRight');
					CheckBtByCommand('justifyFull');
					
					var selection	= Instance.getSelectedText();
					var eFocus		= Instance.getSelectedNodesByRange();
					
					if ( eFocus) {
						
					}
					xx=eFocus;
					console.log(eFocus)
				}
				function EditLink( eLink) {
					if ( eLink.href) {
						eLinkEditor.removeClass('hidden');
						var eInput	= eLink;
						if ( eLinkEditor.nodeName != 'INPUT') {
							eInput	= jCube(eLinkEditor).query(':input');
						}
						eInput.value	= eLink.href;
						eInput.crrLink	= eLink;
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
					if ( eNode != eEditor && !jCube(eNode).getParent('.DivEditor')) {
						return;
					}
					if ( eNode ) {
						if ( window.getSelection) {
							storage.sel		= window.getSelection();
							storage.range	= storage.sel.getRangeAt(0).toString()?storage.sel.getRangeAt(0):storage.range;
						} else if (document.selection) {
							storage.sel		= window.selection;
							storage.range	= storage.sel.createRange();
						}
					}
					//console.log([eNode,storage.range])
					//xx=storage.range;
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
						eEditor.focus();
					}, 80);
				}
				return this;
			}
