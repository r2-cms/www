<?php
	
	$sql	= array(
		"select"	=> "
				r.id,
				r.old,
				r.new,
				r.total,
				r.creation,
				r.remarks
		",
		"from"	=> "
				gt8_url_history r
		",
		'gridConf'	=> array(
			//format: label|width|type|length|minwidth|maxwidth
			/*0*/ 'id|40|integer||20|100',
			/*1*/ 'old|400|string|256|40|400',
			/*2*/ 'new|400|string|256|40|400',
			/*3*/ 'total|70|integer||20|100',
			/*4*/ 'Criação|200|datetime'
		),
		'gridState'	=> '0|40{{}}1|400{{}}2|400{{}}3|70{{}}4|200'
	);
	
?>