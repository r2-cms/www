<?php
	$sql	= array(
		'select'	=> '
			e.id,
			e.title,
			e.filename,
			e.files
		',
		'from'	=> '
			gt8_explorer e
		',
		'where'	=> "
			AND e.dirpath REGEXP '^474/[0-9]+/$'
			AND e.approved = 1
		",
		'group'	=> '
			e.filename
		',
		'order'	=> '
			e.title
		'
	);
?>