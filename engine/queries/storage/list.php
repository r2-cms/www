<?php
	$sql	= array(
		"select"	=> "
				e.id, e.dirpath, e.path, e.filename,
				e.size, e.stock,
				e.price_cost, e.price_selling,
				e.approved, e.code, e.title, e.locked,
				IF(e.type='file', CONCAT('{$GT8['explorer']['root']}', e.path, e.filename), CONCAT('{$GT8['explorer']['root']}', e.path, e.filename, '/')) AS fullpath,
				
				u.id AS id_user, u.name AS user,
				
				s.path, s.filename, s.id AS idStorage
		",
		"from"	=> "
			gt8_storage l
			JOIN gt8_explorer e 		ON e.id = l.id_explorer_product
			JOIN gt8_explorer s 		ON s.id = l.id_explorer_storage
			JOIN gt8_users u 			ON u.id = e.id_users
		",
		'foundRows'	=> 1
	);
	
?>
