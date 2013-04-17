		
		<script type="text/javascript" >
		//<![CDATA[
			
			jCube(function(){
				jCube(':h1').setHTML('Produto: '+ jCube(':input[name=title]').value);
				jCube('::.TabbedPane > .body > .attributes > .card > label strong.att').each(function(){
					if ( ['tamanho','armazenamento','mostruário'].contains(this.innerHTML.toLowerCase()) ) {
						this.getParent('label').addClass('hidden');
					}
				});
				
				var eA	= jCube(document.createElement('A'));
				eA.href	= ASP.CROOT + ASP.path.replace('catalogo/', '') + jCube(':input[name=filename]').value +'/';
				eA.appendChild(jCube(':#ePublishC .img-preview span'));
				jCube(':#ePublishC .img-preview').appendChild( eA);
			});
			
		//]]>
		</script>