<?php
	global $GT8;
	$_h_isModal		= isset($_GET['modal']) && ($_GET['modal']==1 || $_GET['modal']=='true');
	$_h_showMain	= !$_h_isModal;
	$_h_showTool	= isset($data['toolbar']) && $data['toolbar'];
	$_h_showDir		= isset($data['directory']) && $data['directory'];
	
	if ( $_h_isModal) {
		if ( isset($_GET['headbar']) && ($_GET['headbar']==0 || $_GET['headbar']=='false')) {
			$_h_showMain	= false;
		}
		if ( isset($_GET['toolbar']) && ($_GET['toolbar']==0 || $_GET['toolbar']=='false')) {
			$_h_showTool	= false;
		}
		if ( isset($_GET['locationbar']) && ($_GET['locationbar']==0 || $_GET['locationbar']=='false')) {
			$_h_showDir	= false;
		}
	}
?><header class="admin" >
				<div class="holder" >
					<nav class="main<?php if ( !$_h_showMain ) { print(' hidden'); } ?>" >
						<a class="button home<?php print(strpos('#'.strtolower($_SERVER["PHP_SELF"]), "/". strtolower($GT8['admin']['root']) ."index.php")>0? ' disabled': ''); ?>" href="{{AROOT}}" title="Ir para a página administrativa inicial" ><span><img src="{{CROOT}}imgs/gt8/toolbar/home-regular.png" alt="[bt home]" /></span></a>
						<a class="button user" href="{{AROOT}}users/{{SESSION:login.login}}/" ><span><img src="{{CROOT}}{{GT8:explorer.root}}users/{{SESSION:login.login}}/?small" alt="Carregando..." /></span></a>
						<h1>{{h1}}</h1>
						<div id="eNotificationsC" class="float-right clearfix-child" >
							<a href="#" onclick="jsAdmin.logout(event)" class="button logout " title="Finalizar esta sessão" ><span><img src="{{CROOT}}imgs/gt8/toolbar/logout-regular.png" alt="[bt logout]" /></span></a>
						</div>
					</nav>
					<?php
						if ( $_h_showTool ) {
							print('<nav class="toolbar" >'. $data['toolbar'] .'</nav>');
						}
						if ( $_h_showDir ) {
							print('<nav class="directory" >'. $data['directory'] .'</nav>');
						}
					?>
					
					<script type="text/javascript" src="{{AROOT}}chat/json.js" ></script>
				</div>
				<div class="space" ><?php
					if ( $_h_showMain) {
						print('<div class="main"></div>');
					}
					if ( $_h_showTool) {
						print('<div class="toolbar"></div>');
					}
					if ( $_h_showDir) {
						print('<div class="directory"></div>');
					}
				?></div>
			</header>