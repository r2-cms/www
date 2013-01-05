<?php
	$sql	= array(
		"select"	=> "
				a.id, a.ip, u.login,
				a.browser, a.browser_v, a.os,
				a.referrer, '' AS pageViews, p.delay AS duration,
				DATE_FORMAT(p.creation, '%d/%m/%Y %H:%i:%s') AS creation,
				p.id AS id_analytics
		",
		"from"	=> "
				gt8_analytics a
				JOIN gt8_analytics_page p ON a.id = p.id_analytics
				LEFT JOIN gt8_users u ON u.id = a.id_users
		",
		'group'	=> 'a.id',
		'order'	=> '
			p.id DESC
		',
		'gridConf'	=> array(
			//format: label|width|type|length|minwidth|maxwidth
			/*0*/ 'ID|50|integer|10|20|100',
			/*1*/ 'IP|80|string|8|20|200',
			/*2*/ 'Usuário|80|string|8|20|200',
			/*3*/ 'Navegador|80|string|8|20|200',
			/*4*/ 'Vs|20|integer|8|20|200',
			/*5*/ 'SO|80|string|8|20|200',
			/*6*/ 'Origem|80|string|8|20|200',
			/*7*/ 'Visualizações|80|integer|8|20|200',
			/*8*/ 'Duração|80|integer|8|20|200',
			/*9*/ 'Data|80|datetime|8|20|200',
			/*10*/ 'ID view|80|integer|8|20|100'
		),
		//sample: 'gridState'	=> '0|70{{}}1|160{{}}Região(())3|40(())4|300{{}}Localização(())5|300(())6|300(())7|100{{}}Data(())10|160(())11|160',
		'gridState'	=> '1|70{{}}0|80{{}}2|120{{}}3|120{{}}4|40{{}}5|40{{}}6|200{{}}7|60{{}}8|120{{}}9|120{{}}10|40'
	);
	
?>
