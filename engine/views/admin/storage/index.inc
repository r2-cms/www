<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Armazenagem de produtos | GT8</title>
		<link rel="stylesheet" type="text/css" href="{{AROOT}}storage/css/storage.css" />
		<link rel="stylesheet" type="text/css" href="{{CROOT}}css/gt8/gt8.css" />
		<link rel="stylesheet" type="text/css" href="{{AROOT}}css/admin.css" />
		<script type="text/javascript" src="{{CROOT}}jCube/jCube.single.js" ></script>
		<script type="text/javascript" src="{{CROOT}}js/gt8/gt8.js" ></script>
		<script type="text/javascript" src="{{CROOT}}js/gt8/Pager.js" ></script>
		<script type="text/javascript" src="{{AROOT}}js/main.js" ></script>
		<script type="text/javascript" src="{{AROOT}}js/Modal.js" ></script>
		<script type="text/javascript" src="{{AROOT}}storage/js/storage.js" ></script>
		<script type="text/javascript" >
			{{JS_SERVER_VARS}}
		</script>
		<script type="text/javascript" >
			//{{$this->setCards()}}
			var jStorage	= {
				paths: [
				{{FOREACH:storage-rows}}['{{path}}{{filename}}', '##title##', '{{fullpath}}', '{{filename}}', '{{id}}'],
				{{/FOREACH}}
				['the end']
				]
			};
		</script>
		<meta name="viewport" content="width=device-width,initial-scale=1" />
	</head>
	<body class="{{modal-class}}" >
		<div id="eMain" >
			<header class="admin" >
				<div class="holder" >
					<nav class="main mobile-hidden" >
						<a class="button home" href="{{AROOT}}" title="Ir para a página administrativa inicial" ><span><img src="{{CROOT}}imgs/gt8/toolbar/home-regular.png" alt="[bt home]" /></span></a>
						<a class="button user" href="{{AROOT}}users/Roger/" ><span><img src="{{CROOT}}{{GT8:explorer.root}}users/Roger/?small" alt="Carregando..." /></span></a>
						<h1>Armazenagem de produtos</h1>
						<a href="#" onclick="jsAdmin.logout(event)" class="button logout float-right" title="Finalizar esta sessão" ><span><img src="{{CROOT}}imgs/gt8/toolbar/logout-regular.png" alt="[bt logout]" /></span></a>
					</nav>
					<nav class="directory" >{{$this->getDirLocation()}}</nav>
					<script type="text/javascript" src="{{AROOT}}chat/json.js" ></script>
				</div>
				<div class="space" >
					<div class="main mobile-hidden" ></div>
					<div class="directory"></div>
				</div>
			</header>
			<div id="eResults" class="row-12 fluid-12 margin-top-small clearfix-child" >
				<div id="eBackwardC" >
					<a id="eBackward" href="../" class="grid-12 line-height-large text-align-center margin-top-small cursor-pointer disabled" >
						<strong id="eBackwardLabel" >Voltar</strong>
					</a>
				</div>
				<div id="eC" class="line-height-large clearfix-child text-align-center" >
					
				</div>
			</div>
			<footer class="footer-clear-height" >&nbsp;</footer>
			{{$this->getFooter()}}
		</div>
	</body>
</html>