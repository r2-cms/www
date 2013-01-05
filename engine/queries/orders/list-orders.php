<?php
	
	$sql	= array(
		'select'	=> '
				o.id,
				o.id_users,
				o.price_total,
				o.price_freight,
				o.id_stts,
				s.ttl AS status,
				o.creation,
				
				o.a_zip AS zip,
				o.a_stt AS stt,
				o.a_city AS city,
				o.a_district AS district,
				o.a_street AS street,
				o.a_number AS number,
				
				u.login,
				u.name,
				u.cpfcnpj,
				u.document,
				u.genre
			',
		'from'	=> '
			gt8_orders o
			INNER JOIN gt8_stts s	ON s.id = o.id_stts
			INNER JOIN gt8_users u	ON u.id = o.id_users
		',
		'order'	=> 'o.id DESC'
	);
?>