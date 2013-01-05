<?php
	
	mysql_query("
		UPDATE
			gt8_cron
		SET
			monthly	= ". date('n') ."
	") or die('impossible to execute the monthly cron job');
	$_SESSION['cron']['monthly']	= date('n');
	mysql_query('
		UPDATE
			gt8_explorer_view
		SET
			vmonth	= 0
	') or die('ERROR: cron.monthly::update');
?>