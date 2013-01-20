<?php
	$sql	= array(
		"select"	=> "
			sd.id, sd.id_user, sd.ftp, sd.domain, sd.login, sd.pass, sd.port, sd.scan_frequency, sd.creation, u.name
		",
		"from"	=> "
			gt8_scan_domains sd
				INNER JOIN
					gt8_users u
						ON
							u.id =  sd.id_user
		",
		'group'	=> 'sd.id'
	);
?>