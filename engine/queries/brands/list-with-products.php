<?php
	
	$sql	= array(
		"select"	=> "
				id, brand, canonical, enabled, COUNT(total) AS products
		",
		"from"	=> "
			(
				SELECT
					b.id, b.title AS brand, b.canonical, b.enabled, COUNT(b.id) AS total
				FROM
					gt8_products  p
					JOIN gt8_level_tie t			ON p.id = t.id_products
					JOIN gt8_level_subgroup sg	ON sg.id = t.id_subgroup
					JOIN gt8_level_group g		ON g.id = sg.id_group
					JOIN gt8_level_family f		ON f.id = g.id_family
					JOIN gt8_level l				ON l.id = f.id_level
					JOIN gt8_brands b			ON b.id = p.id_brand
				WHERE
					l.id = l.id
				GROUP BY
					p.id
			) AS z
		",
		'group'	=> 'id'
	);
	
?>