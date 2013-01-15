					<h1>Dúvidas Frequentes</h1>
					{{FOREACH:articles-faq}}
					<h3>{{title}}</h3>
					<div class="content-articles" >
						##article##
					</div>
					{{/FOREACH}}
					<script type="text/javascript" >
						(function(){
							jCube('::.content-articles a').addClass('link');
						})();
					</script>