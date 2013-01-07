<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Personalização das ofertas na home | GT8</title>
		
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
				"h1" 		=> 'Vazio',
				'directory'	=> $this->getDirLocation(),
				'toolbar'	=> $this->toolbarItems
			)); ?>
			<footer class="footer-clear-height" >&nbsp;</footer>
			<?php $this->printFooter(); ?>
		</div>
	</body>
</html>