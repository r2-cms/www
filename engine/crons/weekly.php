<?php
	
	mysql_query("
		UPDATE
			gt8_cron
		SET
			weekly	= ". date('W') ."
	") or die('impossible to execute the weekly cron job');
	$_SESSION['cron']['weekly']	= date('W');
	
	mysql_query('
		UPDATE
			gt8_explorer_view
		SET
			vweek	= 0
	') or die('ERROR: cron.weekly::update');
?>