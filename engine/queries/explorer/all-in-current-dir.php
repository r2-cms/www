<?php
	$__userLevel	= (isset($_SESSION['login']['level'])? $_SESSION['login']['level']: 0);
	$sql	= array(
		"select"	=> "
				'file' AS type,
				e.id, e.id_dir, e.title, e.locked, e.mime,
				
				e.path,
				e.filename, e.size,
				
				e.width, e.height, 
				e.allow, e.privilege, DATE_FORMAT(e.creation, '%Y/%m/%d %H:%i:%s') AS creation, DATE_FORMAT(e.modification, '%Y/%m/%d %H:%i:%s') AS modification,
				u.id AS id_user, u.name AS user,
				
				COALESCE(ev.vtotal, 0) AS vtotal, COALESCE(ev.vmonth, 0) AS vmonth, COALESCE(ev.vweek, 0) AS vweek, COALESCE(ev.vtoday, 0) AS vtoday
		",
		"from"	=> "
				gt8_explorer e
				JOIN gt8_users u					ON u.id = e.id_users
				LEFT JOIN gt8_explorer_view ev		ON e.id = ev.id
				
				INNER JOIN gt8_explorer_dir d		ON d.id = e.id_dir
				LEFT JOIN gt8_explorer_dir d2		ON d2.id = d.id_parent
				LEFT JOIN gt8_explorer_dir d3		ON d3.id = d2.id_parent
				LEFT JOIN gt8_explorer_dir d4		ON d4.id = d3.id_parent
				LEFT JOIN gt8_explorer_dir d5		ON d5.id = d4.id_parent
				LEFT JOIN gt8_explorer_dir d6		ON d6.id = d5.id_parent
				LEFT JOIN gt8_explorer_dir d7		ON d7.id = d6.id_parent
				LEFT JOIN gt8_explorer_dir d8		ON d8.id = d7.id_parent
				LEFT JOIN gt8_explorer_dir d9		ON d9.id = d8.id_parent
		",
		'where'	=> "
			AND (
				u.id = ". $_SESSION['login']['id'] ." || ((e.privilege+0) >= 2 && (e.allow+0) <= $__userLevel) || (e.allow+0)<=$__userLevel
			)
		",
		'union'	=> "
			UNION SELECT
				'folder' AS type,
				d.id, d.id, d.title, 0, 'directory',
				
				CONCAT( IF(d9.dir IS NOT NULL, CONCAT(d9.dir, '/'), ''), IF(d8.dir IS NOT NULL, CONCAT(d8.dir, '/'), ''), IF(d7.dir IS NOT NULL, CONCAT(d7.dir, '/'), ''), IF(d6.dir IS NOT NULL, CONCAT(d6.dir, '/'), ''), IF(d5.dir IS NOT NULL, CONCAT(d5.dir, '/'), ''), IF(d4.dir IS NOT NULL, CONCAT(d4.dir, '/'), ''), IF(d3.dir IS NOT NULL, CONCAT(d3.dir, '/'), ''), IF(d2.dir IS NOT NULL, CONCAT(d2.dir, '/'), ''), d.dir, '/' ) AS path,
				d.dir AS filename, 0,
				
				0, 0, 
				1, 4, DATE_FORMAT(d.creation, '%Y/%m/%d %H:%i:%s') AS creation, NOW() AS modification,
				1 AS id_user, '' AS user,
				
				0 AS vtotal, 0 AS vmonth, 0 AS vweek, 0 AS vtoday
			FROM
				gt8_explorer_dir d
				LEFT JOIN gt8_explorer_dir d2	ON d2.id = d.id_parent
				LEFT JOIN gt8_explorer_dir d3	ON d3.id = d2.id_parent
				LEFT JOIN gt8_explorer_dir d4	ON d4.id = d3.id_parent
				LEFT JOIN gt8_explorer_dir d5	ON d5.id = d4.id_parent
				LEFT JOIN gt8_explorer_dir d6	ON d6.id = d5.id_parent
				LEFT JOIN gt8_explorer_dir d7	ON d7.id = d6.id_parent
				LEFT JOIN gt8_explorer_dir d8	ON d8.id = d7.id_parent
				LEFT JOIN gt8_explorer_dir d9	ON d9.id = d8.id_parent
		",
		'order'	=> 'path, filename',
		'foundRows'	=> "
			SELECT
				COUNT(*) AS total
			FROM
				gt8_explorer e
				JOIN gt8_users u ON u.id = e.id_users
			UNION SELECT
				COUNT(*)
			FROM
				gt8_explorer_dir d
			"
	);
	
?>
