			<header class="admin" >
				<div class="holder" >
					<nav class="main mobile-hidden" >
						<a class="button home" href="{{AROOT}}" title="Ir para a página administrativa inicial" ><span><img src="{{CROOT}}imgs/gt8/toolbar/home-regular.png" alt="[bt home]" /></span></a>
						<a class="button user" href="{{AROOT}}users/{{SESSION:login.login}}/" ><span><img src="{{CROOT}}{{GT8:explorer.root}}users/{{SESSION:login.login}}/?small" alt="Carregando..." /></span></a>
						<h1>{{title}}</h1>
						<div id="eNotificationsC" class="float-right clearfix-child" >
							<a href="#" onclick="jsAdmin.logout(event)" class="button logout " title="Finalizar esta sessão" ><span><img src="{{CROOT}}imgs/gt8/toolbar/logout-regular.png" alt="[bt logout]" /></span></a>
						</div>
					</nav>
					<nav class="toolbar mobile-hidden" >{{toolbar}}</nav>
					<nav class="directory" >{{$this->getDirLocation()}}</nav>
					<script type="text/javascript" src="{{AROOT}}chat/json.js" ></script>
				</div>
				<div class="space" >
					<div class="main mobile-hidden" ></div>
					<div class="toolbar mobile-hidden" ></div>
					<div class="directory" ></div>
				</div>
			</header>