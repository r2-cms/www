<?php
	$__userLevel	= (isset($_SESSION['login']['level'])? $_SESSION['login']['level']: 1);
	$userId			= isset($_SESSION['login']['id'])? $_SESSION['login']['id']: 0;
	$sql	= array(
		"select"	=> "
				j.id,
				j.id_users,
				j.sumary,
				j.creation
		",
		"from"	=> "
				gt8_calendar_journal j
				JOIN gt8_users u		ON u.id = j.id_users
		",
		'@where'	=> "
			AND (
				u.id = ". $userId ." || e.read_privilege <= $__userLevel
			)
		"
	);
	
?>
