<?php
	
	$sql	= array(
		"select"	=> "
				p.id_analytics, p.id, SUBSTRING(p.page, 2) AS page, p.scroll, p.delay AS duration, p.creation
		",
		"from"	=> "
				gt8_analytics a
				JOIN gt8_analytics_page p ON a.id = p.id_analytics
		",
		'order'	=> 'p.creation DESC'
	);
	
?>