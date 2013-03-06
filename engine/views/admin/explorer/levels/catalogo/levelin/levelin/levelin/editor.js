		
		<script type="text/javascript" >
		//<![CDATA[
			
			jCube(function(){
				jCube(':h1').setHTML('Produto: '+ jCube(':input[name=title]').value);
				jCube('::.TabbedPane > .body > .attributes > .card > label strong.att').each(function(){
					if ( ['tamanho','preço adicional'].contains(this.innerHTML.toLowerCase()) ) {
						this.getParent('label').addClass('hidden');
					}
				});
			});
			
		//]]>
		</script>