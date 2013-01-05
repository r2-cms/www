<?php
	
	$sql	= array(
		'select'	=> '
			d.id, d.id_explorer, IF( d.off IS NOT NULL, d.off, 0) AS off,
			e.id AS id_explorer,
			e.title, e.dirpath, e.path, e.filename
		',
		'from'	=> '
			gt8_explorer e 
			LEFT JOIN gt8_desconto_boleto d ON e.id = d.id_explorer
		',
		'group'	=> '
			e.dirpath, e.filename
		'
	);
?>