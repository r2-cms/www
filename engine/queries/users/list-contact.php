<?php
	
	$sql	= array(
		'select'	=> '
				uc.id, uc.id_users, 
				uc.channel, uc.type, uc.value,
				uc.creation, uc.modification
			',
		'from'	=> '
				gt8_users_contact uc
		'
	);
?>