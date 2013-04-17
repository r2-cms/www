<?php
	$this->setData('title', 'Armazenamento de produtos');
	$this->setCards();
?>
<!DOCTYPE html>
<html lang="en" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Armazenagem de produtos | GT8</title>
		<link rel="stylesheet" type="text/css" href="{{AROOT}}storage/css/storage.css" />
		{{VIEW:admin.head-default}}
		<script type="text/javascript" src="{{CROOT}}js/gt8/Pager.js" ></script>
		<script type="text/javascript" src="{{AROOT}}js/Modal.js" ></script>
		<script type="text/javascript" src="{{AROOT}}storage/js/storage.js" ></script>
		<script type="text/javascript" >
			{{JS_SERVER_VARS}}
		</script>
		<script type="text/javascript" >
			var jStorage	= {
				paths: [
				{{FOREACH:storage-rows}}['{{path}}{{filename}}', '##title##', '{{fullpath}}', '{{filename}}', {{id}}],
				{{/FOREACH}}
				['the end']
				]
			};
		</script>
		<meta name="viewport" content="width=device-width,initial-scale=1" />
	</head>
	<body class="{{modal-class}} overflow-auto" >
		<div id="eMain" >
			{{VIEW:admin.header-new}}
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