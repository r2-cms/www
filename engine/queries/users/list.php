<?php
	
	$sql	= array(
		'select'	=> '
				u.id, u.name, u.login,
				u.level, u.enabled, u.approval_level_required,
				u.genre,
				u.natureza,
				u.cpfcnpj,
				u.document,
				u.genre,
				DATE_FORMAT(u.birth, "%d%/%m/%Y") AS birth,
				DATE_FORMAT(u.creation, "%d%/%m/%Y") AS creation,
				DATE_FORMAT(u.modification, "%d%/%m/%Y") AS modification,
				DATE_FORMAT(u.birth, "%d%/%m/%Y") AS birth
			',
		'from'	=> '
			gt8_users u
		'
	);
?>