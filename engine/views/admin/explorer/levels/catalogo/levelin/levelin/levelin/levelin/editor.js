		
		<script type="text/javascript" >
		//<![CDATA[
			
			jCube(function(){
				var SLASH	= '/';
				jCube(':h1').setHTML('Variação: <small>'+ jCube(':input[name=title]').value +'<'+SLASH+'small>');
				jCube(':nav.toolbar a.attachs').addClass('hidden');
				
				//hidding some unnecessary fields
				jCube('::[name=sumary], [name=description]').each(function(){
					this.getParent('label').addClass('hidden');
				});
				//changing filename to código
				jCube(':input[name=filename]').getParent('label').query(':strong').innerHTML	= 'Código da variação';
				
				jCube('::[name=price_selling], [name=price_suggested], [name=price_cost], [name=stock]').each(function(){
					this.getParent('label').removeClass('hidden');
				});
				
				jCube('::.TabbedPane > .body > .attributes > .card > label strong.att').each(function(){
					if ( ['material da sola', 'material interno', 'material externo', 'tema', 'tipo de salto', 'gênero', 'desconto', 'cor','altura da plataforma', 'altura do cano', 'altura do salto'].contains(this.innerHTML.toLowerCase())) {
						this.getParent('label').addClass('hidden');
					}
				});
				
				jCube(':#addNewPicture').addEvent('onclick', function(E){//ADD NEW PHOTO
					E.stop();
					
					if ( jCube(':.TabbedPane > .body > .photos > .cards > .card.hidden')) {
						jCube(':.TabbedPane > .body > .photos > .cards > .card.hidden').removeClass('hidden');
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
								}
							}
						}));
					}
				});
				//eliminando abas desnecessárias
				jCube(':.TabbedPane .header .tab.imagem').remove();
				jCube(':.Editor-TabbedPaneC .body .card.imagem').remove();
				
				//Criando link para a imagem do painel
				var eA	= jCube(document.createElement('A'));
				eA.href	= ASP.CROOT + ASP.path.replace('catalogo/', '');
				eA.appendChild(jCube(':#ePublishC .img-preview span'));
				jCube(':#ePublishC .img-preview').appendChild( eA);
				jCube(':#ePublishC .img-preview img').src	= jCube(':#ePublishC .img-preview img').src.replace(jCube(':input[name=filename]').value, '');
			});
			
		//]]>
		</script>