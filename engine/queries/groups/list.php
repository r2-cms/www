<?php
	
	$sql	= array(
		'select'	=> '
				l.id AS idLine, f.id AS idFamily, g.id AS idGroup, sg.id AS idSubgroup,
				l.title AS line, f.title AS family, g.title AS `group`, sg.title AS subgroup,
				COUNT(sg.title) AS products
		',
		'from'	=> '
				gt8_products p
				LEFT JOIN gt8_level_tie t		ON p.id = t.id_products
				LEFT JOIN gt8_level_subgroup sg	ON sg.id = t.id_subgroup
				LEFT JOIN gt8_level_group g		ON g.id	= sg.id_group
				LEFT JOIN gt8_level_family f		ON f.id = g.id_family
				LEFT JOIN gt8_level l			ON l.id = f.id_level
				LEFT JOIN gt8_brands b			ON b.id = p.id_brand
		',
		'group'	=> 'g.id, sg.title',
		'order'	=> 'g.title, sg.title'
	);
?>