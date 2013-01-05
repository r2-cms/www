<?php
	$__ids	= ' 1=1';
	if ( isset($_SESSION['login']['id']) && $_SESSION['login']['id'] ) {
		$__ids	= 'u.id='. $_SESSION['login']['id'];
	}
	if ( isset($_SESSION['analytics']['id']) && $_SESSION['analytics']['id']) {
		$__ids	.= ' OR a.id = '. $_SESSION['analytics']['id'];
	} else if ( isset($_COOKIE['session']) && (integer)$_COOKIE['session']) {
		$__ids	.= ' OR a.id = '. $_SESSION['analytics']['id'];
	}
	$sql	= array(
		"select"	=> "
				ap.id_analytics,
				ap.id AS idPage,
				ap.page,
				pr.id_product,
				ap.scroll,
				SUM(ap.delay) AS duration,
				ap.creation
		",
		"from"	=> "
				gt8_analytics a
				JOIN gt8_analytics_page ap ON a.id  = ap.id_analytics
				JOIN gt8_analytics_products pr ON a.id  = pr.id_analytics
				JOIN gt8_products p ON a.id  = pr.id_analytics
				LEFT JOIN gt8_users u ON u.id  = a.id_users
		",
		"where"	=> "
				AND ($__ids)
				AND ap.page LIKE '%.htm'
				AND ap.page LIKE '/catalogo/%'
		",
		'group'	=> '
				pr.id_product
		',
		'order'	=> '
				RAND()
		'
	);
	unset($__ids);
?>