<?php
	$__userLevel	= (isset($_SESSION['login']['level'])? $_SESSION['login']['level']: 1);
	$userId			= isset($_SESSION['login']['id'])? $_SESSION['login']['id']: 0;
	global $GT8;
	$sql	= array(
		"select"	=> "
				e.type,
				e.id, e.id_dir, e.title, e.locked, e.special, e.mime, 
				
				DATE_FORMAT(e.publish_up, '%Y/%m/%d %H:%i:%s') AS publish_up,
				DATE_FORMAT(e.publish_down, '%Y/%m/%d %H:%i:%s') AS publish_down,
				
				e.dirpath,  e.path, e.filename, e.code,
				IF(e.type='file', CONCAT('{$GT8['explorer']['root']}', e.path, e.filename), CONCAT('{$GT8['explorer']['root']}', e.path, e.filename, '/')) AS fullpath,
				e.size,
				e.folders, e.files,
				
				e.stock, e.price_suggested, e.price_cost, e.price_selling, e.price_parts, e.price_selling / e.price_parts AS price_finantial,
				
				e.width, e.height, 
				e.write_privilege, e.read_privilege, DATE_FORMAT(e.creation, '%Y/%m/%d %H:%i:%s') AS creation, DATE_FORMAT(e.modification, '%Y/%m/%d %H:%i:%s') AS modification,
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
				u.id = ". $userId ." || e.read_privilege<=$__userLevel
			)
		"
	);
	
?>
