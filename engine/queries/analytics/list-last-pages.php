<?php
	
	$sql	= array(
		"select"	=> "
				p.id_analytics, p.id, SUBSTRING(p.page, 2) AS page, p.scroll, SUM(p.delay) AS duration, DATE_FORMAT(MAX(p.creation), '%Y/%m/%d %H:%i:%s') AS creation, COUNT(*) AS total
		",
		"from"	=> "
				gt8_analytics a
				JOIN gt8_analytics_page p ON a.id = p.id_analytics
		",
		'group'	=> 'p.page',
		'order'	=> 'MAX(p.creation) DESC'
	);
	
?>