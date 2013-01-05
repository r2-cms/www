<?php
	
	$sql	= array(
		'select'	=> "
				c.id, 
				c.id_analytics, 
				c.id_users,
				c.id_replay,
				c.nm, 
				c.ml,
				c.st,
				c.cmmnt,
				DATE_FORMAT(c.creation, '%Y%/%m/%d') AS creation
		",
		'from'	=> '
				gt8_co33e210 c
		',
		'order'	=> '
			c.id DESC
		',
		'where'	=> '
			AND c.enabled = 1
		'
	);
	
?>