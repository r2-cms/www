<?php
	
	$sql	= array(
		"select"	=> "
				m.id,
				m.module,
				m.card_index,
				m.page_index,
				m.views 
		",
		"from"	=> "
				gt8_modules m
		",
		'where'	=> '
			AND m.id_users = '. $_SESSION['login']['id'] .'
		'
	);
	
?>