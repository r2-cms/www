<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>File Manager | GT8</title>
		<link rel="stylesheet" type="text/css" href="{{CROOT}}{{GT8:admin.root}}css/admin.css" />
		{{VIEW:admin.header-default}}
		{{tag:css|AROOT:css/SplitPaneListing.css}}
		{{tag:script|inline:{{JS_SERVER_VARS}}}}
		<script type="text/javascript" src="{{CROOT}}js/gt8/Pager.js" ></script>
		<script type="text/javascript" src="{{AROOT}}explorer/js/explorer.js" ></script>
	</head>
	<body class="{{modal-class}}" >
		<div id="eMain" >
			<?php
				$Index->printHeader(array(
					'title'		=> 'Explorer',
					'directory'	=> $Index->getDirLocation(),
					'toolbar'	=> $Index->toolbarItems
				));
			?>
			<div class="body SplitPane-horizontal" >
				<?php $Index->printSidebar(); ?>
				<div class="knob-pane" >&nbsp;</div>
				<div class="content cards row right-pane" >
					<div class="clear invisible height-15" >&nbsp;</div>
					<?php
					$modalClass	= $Index->isModal? ' hidden': '';
					$Index->printCards('<a class="card col-'. ($Index->isModal?4:6) .' locked-{{locked}} allow- privilege-" href="{{filename}}/type={{type}}" title="##title##" id="img-{{id}}" >
						<span class="hidden" >{{all-fields}}</span>
						<em class="user'. $modalClass .'" title="{{id_user}}" >##user##</em>
						<strong class="title" >##title##</strong>
						<span class="imgC" ><img src="{{CROOT}}{{fullpath}}?regular" alt="{{width}}x{{height}}"  /></span>
						<span class="filename'. $modalClass .'" >{{filename}}</span>
						<small class="modification'. $modalClass .'" >{{modification}}</small>
						<small class="small-icn locked-{{locked}}" >&nbsp;</small>
					</a>'); ?>
					<div class="clear invisible height-15" >&nbsp;</div>
				</div>
				<div class="clear" >&nbsp;</div>
			</div>
			<footer class="footer-clear-height" >&nbsp;</footer>
			<?php $Index->printFooter(); ?>
		</div>
		<!-- CUSTOMS -->
		<div id="eFolderEdit" class="teste modal input-validation" >
			<h3 class="col-10 text-shadow" >
					Novo arquivo
			</h3>
			<label title="minlength:1" class="line required col-10 title-holder" >
				<span><input id="eNewFolderTitle" type="text" value="" name="title" class="input-rounded-shadowed" onkeyup="Explorer.createNew( false, event); if ( event.keyCode==13) { jCube(':#eNewFolderName').setFocus();}" /><small>Título</small></span>
			</label>
			<label title="minlength:1" class="line padding-v required col-10 filename-holder" >
				<span><input id="eNewFolderName" type="text" value="" name="dirname" class="input-rounded-shadowed" onkeyup="if(event.keyCode==13){Explorer.createNew( true, event)}" /><small>Nome do arquivo</small></span>
			</label>
			<label class="line" >
				<em class="col feedback spinner-small-hidden spinner-small" id="eNewFolderFeedback"  >&nbsp;</em>
			</label>
			<label class="line buttons" >
				<a class="col-3 href-button href-button-cancel" href="?cancel" onclick="jCube(':#eFolderEdit').showOverlay(); return false" ><span>Cancelar</span></a>
				<a class="col-7 href-button href-button-ok href-button-disabled" href="?createNew" onclick="return Explorer.createNew(true, event)" ><span>OK</span></a>
			</label>
		</div>
		{{PLUGGIN:index.js}}
	</body>
</html>