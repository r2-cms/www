<?php
	$sql	= array(
		"select"	=> "
				d.id, d.id_parent, d.title, d.dir,
				IF(d2.dir IS NOT NULL, d2.dir, '') AS parent,
				DATE_FORMAT(d.creation, '%Y/%m/%d %H:%i:%s') AS creation,
				CONCAT( IF(d9.dir IS NOT NULL, CONCAT(d9.dir, '/'), ''), IF(d8.dir IS NOT NULL, CONCAT(d8.dir, '/'), ''), IF(d7.dir IS NOT NULL, CONCAT(d7.dir, '/'), ''), IF(d6.dir IS NOT NULL, CONCAT(d6.dir, '/'), ''), IF(d5.dir IS NOT NULL, CONCAT(d5.dir, '/'), ''), IF(d4.dir IS NOT NULL, CONCAT(d4.dir, '/'), ''), IF(d3.dir IS NOT NULL, CONCAT(d3.dir, '/'), ''), IF(d2.dir IS NOT NULL, CONCAT(d2.dir, '/'), ''), d.dir, '/' ) AS path,
				IF ( z1.files>0, z1.files, 0) AS files,
				IF ( z2.folders>0, z2.folders, 0) AS folders
		",
		"from"	=> "
				gt8_explorer_dir d
				LEFT JOIN gt8_explorer_dir d2	ON d2.id = d.id_parent
				LEFT JOIN gt8_explorer_dir d3	ON d3.id = d2.id_parent
				LEFT JOIN gt8_explorer_dir d4	ON d4.id = d3.id_parent
				LEFT JOIN gt8_explorer_dir d5	ON d5.id = d4.id_parent
				LEFT JOIN gt8_explorer_dir d6	ON d6.id = d5.id_parent
				LEFT JOIN gt8_explorer_dir d7	ON d7.id = d6.id_parent
				LEFT JOIN gt8_explorer_dir d8	ON d8.id = d7.id_parent
				LEFT JOIN gt8_explorer_dir d9	ON d9.id = d8.id_parent
				LEFT JOIN ( SELECT id_dir, COUNT(*) AS files FROM gt8_explorer i WHERE 1=1 GROUP BY i.id_dir) z1 ON d.id = z1.id_dir
				LEFT JOIN ( SELECT d.id_parent, COUNT(*) AS folders FROM gt8_explorer_dir d WHERE 1 = 1 GROUP BY d.id_parent) z2 ON d.id = z2.id_parent
		"
		//na cláusula ids, use: array(array('d.id', '5')), por exemplo.
	);
	
?>
