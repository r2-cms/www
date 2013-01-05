<?php
	$sql	= array(
		"select"	=> "
			b.id, b.id_users, b.id_dir,
			e.title,
			DATE_FORMAT(e.creation, '%Y/%m/%d %H:%i:%s') AS creation,
			e.path,
			e.filename,
			e.dirpath,
			e.files,
			e.folders
		",
		"from"	=> "
			gt8_explorer_bookmarks b
			JOIN gt8_explorer e 		ON e.id = b.id_dir
		"
	);
	
?>
