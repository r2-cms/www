<?php
	
	$sql	= array(
		'select'	=> '
				i.id AS id, i.price, i.qty, i.id_explorer,
				e.title, e.path, e.filename
			',
		'from'	=> '
			gt8_orders_items i
			INNER JOIN gt8_explorer e ON e.id = i.id_explorer
		'
	);
?>