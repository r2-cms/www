<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>File Manager | GT8</title>
		{{VIEW:admin.header-default}}
		{{tag:script|inline:{{JS_SERVER_VARS}}}}
		<script type="text/javascript" src="{{CROOT}}js/gt8/Pager.js" ></script>
	</head>
	<body class="{{modal-class}}" >
		<div id="eMain" >
			<?php
				GT8::printHeader(array(
					"title" 		=> 'Explorer',
					'directory'	=> GT8::getDirLocation()
				));
			?>
			<div class="wrapper" >
				<div class="row padding-top" >
					<h2 class="block" >Desculpe-nos o transtorno, mas não encontramos a seguinte página que você tentou acessar:</h2>
					<div class="block" >
						<small class="text-error" >"{{path}}"</small> 
					</div>
					<p class="block" >
						Se você digitou o endereço, verifique novamente a ortografia ou contate o administrador deste sistema.
					</p>
					<div class="clear" ></div>
				</div>
			</div>
			<footer class="footer-clear-height" >&nbsp;</footer>
			<?php $this->printFooter(); ?>
		</div>
	</body>
</html>