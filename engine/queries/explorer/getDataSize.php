<?php
	$sql	= array(
		"select"	=> "
			LENGTH(d.thumb) AS thumb,
			LENGTH(d.small) AS small,
			LENGTH(d.regular) AS regular,
			LENGTH(d.preview) AS preview,
			LENGTH(d.data) AS data
		",
		"from"	=> "
			gt8_explorer_data d
		",
		'foundRows'	=> 1,
		'limit'		=> 1
	);
	
?>
