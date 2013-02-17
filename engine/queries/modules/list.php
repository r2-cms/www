<?php
	
	$sql	= array(
		"select"	=> "
				m.id,
				m.module,
				m.card_index,
				m.page_index,
				m.views,
				m.sumary,
				m.shortcut,
				IF ( LENGTH(m.img)>0, m.img, CONCAT(m.module, '/imgs/large.png')) AS img
		",
		"from"	=> "
				gt8_modules m
		",
		'where'	=> '
			AND m.id_users = '. $_SESSION['login']['id'] .'
		'
	);
	
?>