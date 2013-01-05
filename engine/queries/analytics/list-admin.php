<?php
	
	$sql	= array(
		"select"	=> "
				a.id, a.id_users, a.page, a.action, a.name, a.value, DATE_FORMAT(a.creation, '%Y/%m/%d %H:%i:%s') AS creation, a.remarks,
				p.page,
				u.name AS user, u.login
		",
		"from"	=> "
				gt8_analytics_adm a
				LEFT JOIN gt8_analytics_page p ON p.id = a.id_analytics_page
				LEFT JOIN gt8_users u ON u.id = a.id_users
		",
		'order'	=> 'a.id DESC'
	);
	
?>