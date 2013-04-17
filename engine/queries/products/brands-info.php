<?php
	$brandId	= mysql_fetch_array(mysql_query("SELECT id FROM gt8_explorer e WHERE id_dir =0 AND e.filename = 'brands'"));
	$brandId	= $brandId[0];
	$sql	= array(
		'select'	=> '
				e.id,
				e.title,
				e.filename,
				e.size,
				d.description
		',
		'from'	=> '
			gt8_explorer e
			LEFT JOIN gt8_explorer_data d	ON e.id = d.id
		',
		'where'	=> '
			AND e.dirpath = "'.$brandId.'/"
			AND e.approved = 1
		',
		'order'	=> '
			e.filename
		'
	);
?>