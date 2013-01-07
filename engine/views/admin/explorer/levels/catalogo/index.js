		<script type="text/javascript" >
		//<![CDATA[
			
			jCube(function(){
				jCube(':h1').setHTML('Linhas');
				if ( jCube('::nav.toolbar a.new-file')) {
					jCube('::nav.toolbar a.new-file').addClass('hidden');
					jCube('::nav.toolbar a.new-folder img').setProperty('title', 'Criar nova linha de produtos');
				}
				Explorer.strings.label	= 'Nova linha';
			});
			
		//]]>
		</script>