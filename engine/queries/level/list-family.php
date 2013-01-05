<?php
	
	$sql	= array(
		'select'	=> '
				idLine, line, lcanonical,
				idFamily, family, fcanonical,
				url,
				COUNT(products) AS products
			',
		'from'	=> '(
				SELECT
					
					l.id AS idLine, l.title AS line, l.canonical AS lcanonical,
					f.id AS idFamily, f.title AS family, f.canonical AS fcanonical,
					CONCAT( "/", l.canonical, "/", f.canonical, "/") AS url,
					1 AS products
			
				FROM
					gt8_level l
					JOIN gt8_level_family f		ON l.id = f.id_level
					JOIN gt8_level_group g		ON f.id = g.id_family
					JOIN gt8_level_subgroup sg	ON g.id = sg.id_group
					LEFT JOIN gt8_level_tie t	ON sg.id = t.id_subgroup
					LEFT JOIN gt8_products  p 	ON p.id = t.id_products
					LEFT JOIN gt8_brands b		ON b.id = p.id_brand
				WHERE
					l.id = l.id
				GROUP BY
					p.id
			) z
		',
		'group'	=> 'idFamily'
	);
?>