<?php
	
	$sql	= array(
		'select'	=> '
				oc.id,
				oc.title,
				oc.page,
				oc.source,
				oc.limit,
				oc.random,
				oc.modification
			',
		'from'	=> '
			gt8_offers_config oc
		'
	);
?>