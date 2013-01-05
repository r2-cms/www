<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Gerenciamento de privilégios - {{login}} | GT8</title>
		{{VIEW:admin.header-default}}
		{{tag:script|CROOT:js/gt8/Editor.js}}
		{{tag:script|AROOT:js/Modal.js}}
		{{tag:script:{{CROOT}}js/gt8/Pager.js}}
		{{tag:script:{{AROOT}}privileges/js/editor.js}}
		{{tag:script|inline:{{JS_SERVER_VARS}}}}
	</head>
	<body class="{{modal-class}}" >
		<div id="eMain" >
			<?php $Editor->printHeader(array(
				'title'		=> 'Concessão de acesso | '. utf8_encode(htmlentities($Editor->data['login'])),
				'directory'	=> $Editor->getDirLocation(),
				'toolbar'	=> $Editor->toolbarItems
			)); ?>
			<div class="Editor-TabbedPaneC body padding-h padding-top" >
				<div class="TabbedPane" >
					<div class="header" >
						<div class="tab" ><div>Geral</div></div>
						<div class="tab" ><div>log</div></div>
						<?php $Editor->printManagerTab(); ?>
						<div class="clear"></div>
					</div>
					<div class="body" >
						<div class="card input-validation" id="card-general" >
							<table class="list-filter no-cellspacing alternated-background rounded-rows" >
								<tr>
									<th><span>Categoria</span></th>
									<th><span>Página</span></th>
									<th><span>Campo</span></th>
									<th><span>Privilégio</span></th>
								</tr>
							<?php $Editor->printPrivilegeFields('
								<tr id="page-@id@" >
									<td><span>@category@</span></td>
									<td><span><a href="@url@" >@page@</a></span></td>
									<td><span>@field@</span></td>
									<td>
										<span class="e-select marginless paddingless col-6" >
											<select name="privilege" class="gt8-update" >@privilege-combo-options@</select>
											<span class="out-hidden" >
												<strong>@privilege-label@</strong>
												<img src="{{CROOT}}imgs/gt8/arrow-down-mini.png" alt="" />
											</span>
										</span>
									</td>
								</tr>
							'); ?>
							</table>
						</div>
						<div class="card input-validation" id="card-history" >
							<?php $Editor->printLog('users/privileges/'); ?>
						</div>
						<?php $Editor->printManagerCard(); ?>
					</div>
				</div>
				<?php $Editor->printManagerModal(); ?>
			</div>
			<?php $Editor->printModalButtons(); ?>
			<footer class="footer-clear-height" >&nbsp;</footer>
			<?php $Editor->printFooter(); ?>
		</div>
	</body>
</html>