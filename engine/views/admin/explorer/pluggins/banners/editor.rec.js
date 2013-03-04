		
		<script type="text/javascript" >
		//<![CDATA[
			
			jCube(function(){
				jCube(':h1').setHTML('Background');
				jCube('::nav.toolbar a.attachs').addClass('hidden');
				
				//hidding some unnecessary fields
				jCube('::[name=description]').each(function(){
					this.getParent('label').addClass('hidden');
				});
				
				jCube(':#addNewPicture').addEvent('onclick', function(E){//ADD NEW BACKGROUND
					E.stop();
					var SLASH	= '/';
					if ( jCube(':.TabbedPane > .body > .background > .cards > .card.hidden')) {
						jCube(':.TabbedPane > .body > .background > .cards > .card.hidden').removeClass('hidden');
					} else {
						GT8.Spinner.request(new jCube.Server.HttpRequest({
							url: './?action=new-file&useIndexFilename=1&idDir={{$this->id}}&name=filename&approved=1',
							onComplete: function(){
								this.ret;
								if ( this.ret.insertId) {
									//crie o iframe
									var div	= document.createElement('DIV');
									div.innerHTML	= '<iframe class="col-6 height-160 marginless" frameborder="0" src="'+ ASP.padmin +'explorer/upload/?id='+ this.ret.insertId +'&W=140&H=130&size=small" ><'+SLASH+'iframe><div>0 B / 0 </div>';
									jCube(div).setClass('card col-6').injectBefore(jCube(':#addNewPicture'));
									jCube(':#addNewPicture').addClass('hidden');
								}
							}
						}));
					}
				});
				//eliminando abas desnecessárias
				//jCube(':.TabbedPane .header .tab.dados').remove();
				//jCube(':.Editor-TabbedPaneC .body .card.dados').remove();
			});
			
		//]]>
		</script>