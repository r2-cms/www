<?php
	print("<h1>DEPRECATED!</h1>".PHP_EOL);
	print("<h1>queries.current-dir</h1>".PHP_EOL);
	die('');
	$sql	= array(
		"select"	=> "
				CONCAT( IF(d9.dir IS NOT NULL, CONCAT(d9.dir, '/'), ''), IF(d8.dir IS NOT NULL, CONCAT(d8.dir, '/'), ''), IF(d7.dir IS NOT NULL, CONCAT(d7.dir, '/'), ''), IF(d6.dir IS NOT NULL, CONCAT(d6.dir, '/'), ''), IF(d5.dir IS NOT NULL, CONCAT(d5.dir, '/'), ''), IF(d4.dir IS NOT NULL, CONCAT(d4.dir, '/'), ''), IF(d3.dir IS NOT NULL, CONCAT(d3.dir, '/'), ''), IF(d2.dir IS NOT NULL, CONCAT(d2.dir, '/'), ''), d.dir, '/' ) AS dirName,
				CONCAT( IF(d9.id  IS NOT NULL, CONCAT(d9.id,  '/'), ''), IF(d8.id  IS NOT NULL, CONCAT(d8.id,  '/'), ''), IF(d7.id IS NOT NULL, CONCAT(d7.id,  '/'), ''), IF(d6.id  IS NOT NULL, CONCAT(d6.id,  '/'), ''), IF(d5.id IS NOT NULL, CONCAT(d5.id,  '/'), ''), IF(d4.id  IS NOT NULL, CONCAT(d4.id,  '/'), ''), IF(d3.id  IS NOT NULL, CONCAT(d3.id,  '/'), ''), IF(d2.id  IS NOT NULL, CONCAT(d2.id,  '/'), ''), d.id, '/' ) AS dirCode
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
		",
		'foundRows'	=> 10000
		//na cláusula ids, use: array(array('d.id', '5')), por exemplo.
	);
	
?>
