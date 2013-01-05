<?php
	$sql	= array(
		"select"	=> "
				a.id AS id_attributes,
				a.id_dir,
				a.attribute,
				a.type,
				l.name AS level,
				a.level AS ilevel,
				
				v.id,
				a.prefix,
				a.suffix,
				COALESCE(v.value, '') AS value,
				
				DATE_FORMAT(v.creation, '%d/%m/%Y %H:%i:%s') AS creation,
				DATE_FORMAT(v.modification, '%d/%m/%Y %H:%i:%s') AS modification
		",
		"from"	=> "
				gt8_explorer_attributes a
				INNER JOIN gt8_levels l		ON l.id = a.level
				LEFT JOIN (
					SELECT
						id, id_attributes, id_explorer, value, creation, modification
					FROM
						gt8_explorer_attributes_value vIn
					WHERE
						vIn.id_explorer = vIn.id_explorer
				) v ON a.id = v.id_attributes
		",
		'order'	=> '
			a.attribute ASC, a.type ASC
		',
		'foundRows'	=> 1
		//where: a.id_dir IN ( dirpath.split('/').pop())
		//replace: ('vIn.id_explorer = vIn.id_explorer', 'vIn.id_explorer = ID')
	);
	
?>
