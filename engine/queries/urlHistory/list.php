<?php
	
	$sql	= array(
		'select'	=> '
				h.id,
				h.old,
				h.new,
				h.total,
				DATE_FORMAT( h.creation, "%d%/%m/%Y") AS creation,
				h.remarks
			',
		'from'	=> '
			gt8_url_history h
		',
		'foundRows'	=> 1,
		'order'	=> 'h.id DESC',
		'limit'	=> 1
	);
?>