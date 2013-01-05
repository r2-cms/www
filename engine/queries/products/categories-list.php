<?php
	$sql	= array(
		"select"	=> "
				l.id AS idLine, l.title AS line, l.canonical AS lcanonical,
				f.id AS idFamily, f.title AS family, f.canonical AS fcanonical,
				CONCAT( '/', l.canonical, '/', f.canonical, '/') AS url,
				1
		",
		"from"	=> "
				gt8_level l, gt8_level_family f
		",
		'where'	=> 'AND l.id = COALESCE(f.id_level, 0)'
	);
	
?>