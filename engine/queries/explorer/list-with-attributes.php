<?php
	$__userLevel	= (isset($_SESSION['login']['level'])? $_SESSION['login']['level']: 0);
	$sql	= array(
		"select"	=> "
				e.type,
				e.id, e.id_dir, e.title, e.locked, e.mime, 
				
				DATE_FORMAT(e.publish_up, '%Y/%m/%d %H:%i:%s') AS publish_up,
				DATE_FORMAT(e.publish_down, '%Y/%m/%d %H:%i:%s') AS publish_down,
				
				e.dirpath,  e.path, e.filename,
				IF(e.type='file', CONCAT('explorer/', e.path, e.filename), CONCAT('explorer/', e.path, e.filename, '/')) AS fullpath,
				e.size,
				e.folders, e.files,
				
				e.width, e.height, 
				e.allow, e.privilege, DATE_FORMAT(e.creation, '%Y/%m/%d %H:%i:%s') AS creation, DATE_FORMAT(e.modification, '%Y/%m/%d %H:%i:%s') AS modification,
				e.approved,
				u.id AS id_user, u.name AS user,
				COALESCE(ev.vtotal, 0) AS vtotal, COALESCE(ev.vmonth, 0) AS vmonth, COALESCE(ev.vweek, 0) AS vweek, COALESCE(ev.vtoday, 0) AS vtoday
		",
		"from"	=> "
				gt8_explorer e
				LEFT JOIN gt8_users u			ON u.id = e.id_users
				LEFT JOIN gt8_explorer_view ev	ON e.id = ev.id
		",
		'where'	=> "
			AND (
				u.id = ". $_SESSION['login']['id'] ." || ((e.privilege+0) >= 2 && (e.allow+0) <= $__userLevel) || (e.allow+0)<=$__userLevel
			)
		"
	);
	
?>
