<?php
	$idProduct	= $props['idProduct'];
	$idFamily	= $props['idFamily'];
	
	if ( !$idProduct && ($_GET['print']||isset($_GET['print']))) {
		$idProduct	= (integer)$_GET['idProduct'];
	}
	if ( !$idProduct) {
		die('idProduct missing!');
	}
	
	//requer idFamily
	if ( !$idFamily) {
		$idFamily	= mysql_query("
			SELECT
				p.id, f.id AS idFamily
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
				a.id AS idAttribute, a.attribute,
				f.id AS idFeature, IF(f.id_product=$idProduct, f.feature, '') AS feature,
				af.id_family
		",
		'from'	=> "
			gt8_products_attributes_family af
			INNER JOIN gt8_products_attributes a	ON a.id	= af.id_attribute
			LEFT  JOIN (
				SELECT 
					id, feature, id_attribute, id_product 
				FROM 
					gt8_products_features
				WHERE 
					id_product = $idProduct
			) f	ON f.id_attribute	= a.id
		",
		'where'	=> "
			AND af.id_family = $idFamily
			AND COALESCE( f.id_product, 0) IN ( $idProduct, 0)
		",
		'order'	=> 'a.attribute'
	);
?>