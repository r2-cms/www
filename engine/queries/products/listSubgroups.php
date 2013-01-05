<?php
	$idProduct	= $props['idProduct'];
	$idFamily	= $props['idFamily'];
	
	if ( !$idProduct && ($_GET['print']||isset($_GET['print']))) {
		$idProduct	= (integer)$_GET['idProduct'];
	}
	if ( !$idFamily && ($_GET['print']||isset($_GET['print']))) {
		$idFamily	= (integer)$_GET['idFamily'];
	}
	//id do produto é obrigatório
	if ( !$idProduct) {
		die('idProduct missing!');
	}
	//requer idFamily. Se já passado como argumento, economiza uma consulta
	if ( !$idFamily) {
		$idFamily	= mysql_query("
			SELECT
				p.id, f.id AS idFamily, f.title AS family
			FROM
				gt8_products p
				LEFT JOIN gt8_level_tie t		ON p.id = t.id_products
				LEFT JOIN gt8_level_subgroup sg	ON sg.id = t.id_subgroup
				LEFT JOIN gt8_level_group g		ON g.id = sg.id_group
				LEFT JOIN gt8_level_family f		ON f.id = g.id_family
			WHERE
				p.id = $idProduct
			LIMIT 1
		");
		$idFamily = @mysql_fetch_assoc($idFamily);
		$idFamily = $idFamily? $idFamily['idFamily']: 0;
	}
	$sql	= array(
		'select'	=> "
				$idFamily AS idFamily,
				$idProduct AS idProduct,
				ag.idGroup, ag.`group`,
				IF(gp.idSubgroup, 1, 0) AS isDefined, ag.idSubgroup, ag.subgroup
		",
		'from'	=> "(
				SELECT
					t.id_products AS idProduct,
					g.id AS idGroup, g.title AS `group`,
					sg.id AS idSubgroup, sg.title AS subgroup
				FROM
					gt8_level_group g
					JOIN gt8_level_family f			ON f.id	= g.id_family
					JOIN gt8_level_subgroup sg		ON g.id	= sg.id_group
					JOIN gt8_level_tie t				ON sg.id	= t.id_subgroup
				WHERE
					id_products	= $idProduct
				) gp
				
				RIGHT JOIN (
					SELECT
						g.id AS idGroup, g.title AS `group`,
						sg.id AS idSubgroup, sg.title AS subgroup
					FROM
						gt8_level_family f
						JOIN gt8_level_group g		ON f.id	= g.id_family
						JOIN gt8_level_subgroup sg	ON g.id	= sg.id_group
					WHERE
						f.id = $idFamily AND g.title != '". utf8_decode('Família') ."'
				) ag
				
				ON ag.idSubgroup = gp.idSubgroup
		",
		'order'	=> "
				ag.`group` ASC
		",
		"foundRows"	=> 1000
	);
	
?>