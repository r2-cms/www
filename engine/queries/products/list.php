<?php
	$sql	= array(
		"select"	=> "
				p.id, p.id_brand, p.code, p.title AS product, p.price_cost, p.price_suggested, p.price_selling, p.financing_parts, p.width, p.height, p.length, p.pack_width, p.pack_height, p.pack_length, p.ipi, p.cofins, p.pis, p.iva, p.icms, p.crossdocking, p.canonical AS pcanonical, p.creation, p.modification, p.enabled,
				CONCAT(l.canonical, '/', f.canonical, '/', b.canonical, '/', p.canonical, '.htm') AS url,
				
				l.id AS idLine, l.title AS line,
				
				f.id AS idFamily, f.title AS family,
				
				b.id AS idBrand, b.title AS brand,
				b.canonical AS bcanonical, b.image AS bimage,
				
				CONCAT('". CROOT . $GT8['explorer']['root'] . $GT8['catalog']['root'] ."', l.canonical, '/', f.canonical, '/', b.canonical, '/', p.canonical, '/') AS thumbsrc
		",
		"from"	=> "
				gt8_products p
				LEFT JOIN gt8_level_tie t		ON p.id = t.id_products
				LEFT JOIN gt8_level_subgroup sg	ON sg.id = t.id_subgroup
				LEFT JOIN gt8_level_group g		ON g.id = sg.id_group
				LEFT JOIN gt8_level_family f		ON f.id = g.id_family
				LEFT JOIN gt8_level l			ON l.id = f.id_level
				INNER JOIN gt8_brands b			ON b.id = p.id_brand
		",
		'group'	=> 'p.id'
	);
	
?>