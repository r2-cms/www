<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Redirecionamentos | GT8</title>
		{{tag:css|ACROOT:css/SplitPaneListing.css}}
		{{VIEW:admin.header-default}}
		{{tag:script|CROOT:js/gt8/Pager.js}}
		{{tag:script:js/home.js}}
		{{tag:script|inline:{{JS_SERVER_VARS}}}}
	</head>
	<body class="{{modal-class}}" >
		<div id="eMain" >
			<?php
				$this->printHeader(array(
					'title'		=> 'Redirecionamentos',
					'directory'	=> $this->getDirLocation(),
					'toolbar'	=> $this->toolbarItems
				));
			?>
			<div class="body SplitPane-horizontal" >
				<?php $this->printSidebar(); ?>
				<div class="knob-pane" >&nbsp;</div>
				<div class="content cards row right-pane" >
					<?php $this->printCards('
						<a title="address-{{id}}" href="{{zip}}/" class="address card card-border col-7" id="address-{{id}}" >
							<em class="zip">{{zip}}</em>
							<span class="imgC"><img alt="[imagem]" src="{{CROOT}}imgs/address/Delivery.png" /></span>
							<strong class="estado">{{stt}} - ##city##</strong>
							<span class="title">##street##, {{number}}. ##district##</span>
							<span class="stt hidden">{{stt}}</span>
							<span class="city hidden">##city##</span>
							<span class="street hidden">{{street}}</span>
							<span class="number hidden">{{number}}</span>
							<span class="district hidden">{{district}}</span>
						</a>
					'); ?>
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