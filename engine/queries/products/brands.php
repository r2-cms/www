<?php
	$sql	= array(
		'select'	=> '
			ez.id,
			ez.title,
			ez.filename,
			e.size,
			ez.files
		',
		'from'	=> '
			gt8_explorer e
			INNER JOIN gt8_explorer ez		ON ez.id = e.id_dir
			LEFT JOIN gt8_explorer_view ev	ON e.id = ev.id
		',
		'where'	=> "
			AND ez.dirpath REGEXP '^474/[0-9]+/[0-9]+/$'
		",
		'group'	=> '
			ez.filename
		',
		'order'	=> '
			ev.vtotal DESC
		'
	);
?>