<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Domínios cadastrados | GT8</title>
		
		<link rel="stylesheet" type="text/css" href="{{CROOT}}css/gt8/gt8.css" />
		<link rel="stylesheet" type="text/css" href="{{CROOT}}{{GT8:admin.root}}css/main.css" />
		<script type="text/javascript" src="{{CROOT}}jCube/jCube.single.js" ></script>
		<script type="text/javascript" src="{{CROOT}}js/gt8/gt8.js" ></script>
		<script type="text/javascript" src="{{CROOT}}{{GT8:admin.root}}js/main.js" ></script>
		<script type="text/javascript" src="{{CROOT}}js/gt8/Editor.js" ></script>
		<script type="text/javascript" src="{{CROOT}}js/gt8/Pager.js" ></script>
		{{tag:script|inline:{{JS_SERVER_VARS}}}}
	</head>
	<body class="{{modal-class}}" >
		<div id="eMain" >
			<?php $this->printHeader(array(
				"h1" 		=> 'Domínios disponíveis',
				'directory'	=> $this->getDirLocation(),
				'toolbar'	=> $this->toolbarItems
			)); ?>
			<div class="cards-container clearfix-child row-12 fluid-12" >
				{{METHOD:getScanDomains}}
				<a href="{{domain}}/" class="card grid-3 box-shadow border-radius" >
					<span class="block" >{{id}}</span>
					<span class="block" >{{id_user}}</span>
					<span class="block" >{{ftp}}</span>
					<span class="block" >{{domain}}</span>
					<span class="block" >{{login}}</span>
					<span class="block" >{{pass}}</span>
					<span class="block" >{{port}}</span>
					<span class="block" >{{scan_frequency}}</span>
					<span class="block" >//creation\\</span>
					<span class="block" >//modification\\</span>
				</a>
				{{/METHOD}}
			</div>
			<footer class="footer-clear-height" >&nbsp;</footer>
			{{$this->getFooter()}}
		</div>
	</body>
</html>