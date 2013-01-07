<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Privilégios | GT8</title>
		{{tag:css|ACROOT:css/SplitPaneListing.css}}
		{{tag:css|ACROOT:css/home.css}}
		{{VIEW:admin.header-default}}
		{{tag:script|CROOT:js/gt8/Pager.js}}
		{{tag:script:js/home.js}}
		{{tag:script|inline:{{JS_SERVER_VARS}}}}
	</head>
	<body class="{{modal-class}}" >
		<div id="eMain" >
			<?php
				$this->printHeader(array(
					'title'		=> 'Privilégios',
					'directory'	=> $this->getDirLocation(),
					'toolbar'	=> $this->toolbarItems
				));
			?>
			<div class="body SplitPane-horizontal" >
				<?php $this->printSidebar(); ?>
				<div class="knob-pane" >&nbsp;</div>
				<div class="content cards row right-pane" >
					<div class="clear invisible height-15" >&nbsp;</div>
					<?php $this->printCards(
						'<a class="card col-6" title="{{name}}" href="{{AROOT}}privileges/{{login}}/" >
							<small>&nbsp;</small>
							<span class="imgC2" ><span><span><img src="{{CROOT}}{{GT8:explorer.root}}users/{{login}}/?regular" alt="[imagem]" /></span></span></span>
							<strong id="user-{{id}}" class="title" >##name##</strong>
							<span class="level" >{{level}}</span>
						</a>'
					); ?>
					<div class="clear invisible height-15" >&nbsp;</div>
				</div>
				<div class="clear" >&nbsp;</div>
			</div>
			<footer class="footer-clear-height" >&nbsp;</footer>
			<footer class="main" >
				{{VIEW:admin.paging}}
			</footer>
		</div>
	</body>
</html>