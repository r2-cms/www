					<h1>Dúvidas Frequentes</h1>
					<div class="Accordion" >
						{{METHOD:getFAQ}}
						<div class="item" >
							<h3 class="header" >{{title}}</h3>
							<div class="body" >
								##article##
							</div>
						</div>
						{{/METHOD}}
					</div>
					<script type="text/javascript" >
						jCube.Include.compiled	= false;
						jCube.Include({
							url: 'Pluggins.Accordion',
							async: true,
							onLoad: function(){
								new jCube.Pluggins.Accordion({
									container: ':.Accordion',
									fireMethod: 'onclick'
								});
								jCube(':footer.main').removeClass('fixed');
							}
						});
					</script>
					<script type="text/javascript" >
						(function(){
							jCube('::.Accordion a').addClass('link');
						})();
					</script>