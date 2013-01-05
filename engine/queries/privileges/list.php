<?php
	
	$sql	= array(
		"select"	=> "
				p.id,
				p.id AS id_page,
				p.category,
				p.url,
				p.field,
				p.title,
				p.description,
				
				f.id AS idPrivilege,
				f.id_users, f.login,
				f.modification,
				IF ( f.privilege IS NOT NULL, f.privilege, '-') AS privilege,
				IF ( f.privilege IS NOT NULL, f.privilege-1, 0) AS iprivilege
		",
		"from"	=> "
				gt8_privileges p
				LEFT JOIN (
					SELECT
						f2.id,
						f2.id_privileges, 
						f2.id AS idPrivilegeField, f2.privilege, f2.modification,
						f2.id_users, u.login, u.name
					FROM
						gt8_privileges_fields f2
						JOIN gt8_users u ON u.id = f2.id_users
					WHERE
						u.id = u.id
				) f ON p.id = f.id_privileges
		"
	);
?>