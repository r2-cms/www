<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Usuários | GT8</title>
		{{tag:css|ACROOT:css/SplitPaneListing.css}}
		{{tag:css|ACROOT:css/home.css}}
		{{VIEW:admin.header-default}}
		{{tag:script|CROOT:js/gt8/Pager.js}}
		{{tag:script:js/home.js}}
		{{tag:script|inline:{{JS_SERVER_VARS}}}}
		<style type="text/css" >
			.SplitPane-horizontal {
				__height: {{COOKIE:users-sp-height|1440px}};
			}
		</style>
	</head>
	<body class="{{modal-class}}" >
		<div id="eMain" >
			<?php
				$this->printHeader(array(
					'title'		=> 'Usuários',
					'directory'	=> $this->getDirLocation(),
					'toolbar'	=> $this->toolbarItems
				));
			?>
			<div class="body SplitPane-horizontal" >
				<?php $this->printSidebar(); ?>
				<div class="knob-pane" >&nbsp;</div>
				<div class="content cards row right-pane" >
					<div class="clear invisible height-15" >&nbsp;</div>
					{{$this->getCards()}}
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