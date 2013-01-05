<?php
	
	$sql	= array(
		'select'	=> '
				r.id,
				r.id_users,
				r.token,
				r.creation,
				u.pass,
				u.login
			',
		'from'	=> '
			gt8_users u
			INNER JOIN gt8_users_pass_reset r	ON u.id = r.id_users
		',
		'where'	=> '
			AND UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(r.creation) < 1800
		'
	);
?>