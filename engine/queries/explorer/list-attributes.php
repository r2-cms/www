<?php
	$sql	= array(
		"select"	=> "
				a.id,
				a.id_dir,
				a.attribute,
				a.type,
				l.name AS level,
				a.level AS ilevel,
				a.prefix,
				a.suffix,
				DATE_FORMAT(a.creation, '%d/%m/%Y %H:%i:%s') AS creation,
				DATE_FORMAT(a.modification, '%d/%m/%Y %H:%i:%s') AS modification
		",
		"from"	=> "
				gt8_explorer_attributes a
				INNER JOIN gt8_levels l		ON l.id = a.level
		",
		'order'	=> '
			a.attribute ASC, a.type ASC
		',
		'foundRows'	=> 1
	);
	
?>
