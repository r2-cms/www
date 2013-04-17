		<?php
			$this->addAttributeInSelect('tamanho');
		?>
		<style type="text/css" >
			.cards .card {
				overflow: hidden;
			}
			.cards .card .imgC {
				font-size: 2em;
				line-height: 135%;
				font-weight: bold;
				text-indent: 5px;
				color: #000;
			}
			.cards .card .user, .cards .card .title, .cards .card .filename, .cards .card .modification {
				margin: 0 10px 0 60px !important;
			}
			.color-amarelo		{ background-color: rgb(255, 252, 0);}
			.color-azul			{ background-color: rgb( 60,180,255);}
			.color-azul-marinho	{ background-color: rgb( 0,0,100);}
			.color-bege			{ background-color: rgb(237, 224, 190);}
			.color-branco		{ background-color: rgb(255, 255, 255);}
			.color-bronze		{ background-color: rgb(228, 198, 172);}
			.color-caramelo		{ background-color: rgb(179, 102, 60);}
			.color-cinza		{ background-color: rgb(176, 176, 176);}
			.color-coral		{ background-color: rgb(255, 157, 161);}
			.color-grafite		{ background-color: rgb(115, 115, 115);}
			.color-laranja		{ background-color: rgb(253, 158, 0);}
			.color-marrom		{ background-color: rgb(112, 51, 33);}
			.color-marfim		{ background-color: rgb(245, 245, 240);}
			.color-preto		{ background-color: rgb(0, 0, 0);}
			.color-rosa			{ background-color: rgb(241, 188, 214);}
			.color-roxo			{ background-color: rgb(184, 23, 249);}
			.color-roxo			{ background-color: rgb( 86,54,87);}
			.color-verde		{ background-color: rgb(121, 202, 1);}
			.color-oliva		{ background-color: rgb(105, 137, 74); }
			.color-vermelho		{ background-color: rgb(214, 5, 1);}
			.color-vinho		{ background-color: rgb(113, 16, 25);}
			.color-cobra		{ background-image: url({{CROOT}}imgs/catalog/colors/cobra.jpg); }
			.color-dourado		{ background-image: url({{CROOT}}imgs/catalog/colors/dourado.jpg); }
			.color-floral		{ background-image: url({{CROOT}}imgs/catalog/colors/floral.jpg); }
			.color-listra		{ background-image: url({{CROOT}}imgs/catalog/colors/listrado.jpg); }
			.color-multicolorido{ background-image: url({{CROOT}}imgs/catalog/colors/multicolorido.jpg); }
			.color-onca			{ background-image: url({{CROOT}}imgs/catalog/colors/onca.jpg); }
			.color-onca-escuro	{ background-image: url({{CROOT}}imgs/catalog/colors/onca-escuro.jpg); }
			.color-ouro			{ background-image: url({{CROOT}}imgs/catalog/colors/ouro.jpg); }
			.color-poas			{ background-image: url({{CROOT}}imgs/catalog/colors/poas.jpg); }
			.color-prata		{ background-image: url({{CROOT}}imgs/catalog/colors/prata.jpg); }
			.color-xadrez		{ background-image: url({{CROOT}}imgs/catalog/colors/xadrez.jpg); }
			.color-zebra		{ background-image: url({{CROOT}}imgs/catalog/colors/zebra.jpg); }
		</style>
		<script type="text/javascript" >
		//<![CDATA[
			jCube.Include('String.toASCII');
			jCube(function(){
				jCube(':h1').setHTML('Variações');
				Explorer.strings.label	= 'Digite o código produto (fornecedor)';
				Explorer.strings.filename	= 'Código do produto';
				Explorer.strings.title	= 'Código numérico';
				jCube('::nav.toolbar a.new-folder').addClass('hidden');
				jCube('::nav.toolbar a.new-file img').setProperty('title', 'Criar nova variação de produto');
				
				jCube('::.right-pane.cards .card .filename').addClass('hidden');
				jCube('::.right-pane.cards .card .title').addClass('height-20');
				jCube('::.right-pane.cards .card .modification').addClass('hidden');
				
				//jCube('::.right-pane.cards').addClass('fluid');
				//jCube('::.right-pane.cards .card .hidden .cor').each(function(){
				//	this.getParent('a').addClass('color-'+ this.innerHTML +' card-linear col-8').removeClass('col-6');
				//	jCube(document.createElement('SPAN')).setHTML( '&nbsp;').addClass('icon color color-'+ this.innerHTML.toASCII().toLowerCase().replace(/ /g, '-')).appendTo( this.getParent('a'));
				//});
				jCube('::.right-pane.cards .card .hidden .tamanho').each(function(){
					this.getParent('a').addClass(' card-linear col-7').removeClass('col-6');
					//jCube(document.createElement('SPAN')).setHTML( '&nbsp;').addClass('large').injectBefore( this.getParent('a').query(':a > .modification'));
					this.getParent('a').query(':.imgC').setHTML( this.innerHTML)
					//jCube(document.createElement('SPAN')).setHTML( this.innerHTML).addClass('icon tamanho').appendTo( this.getParent('a'));
				});
				
				//modal
				jCube(':#eNewFolderName').getParent('label').addClass('hidden');
				
			});
			
		//]]>
		</script>