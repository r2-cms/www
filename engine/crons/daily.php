<?php
	
	mysql_query("
		UPDATE
			gt8_cron
		SET
			daily	= ". date('j') ."
	") or die('impossible to execute the daily cron job');
	$_SESSION['cron']['daily']	= date('j');
	//execute your jobs from bellow on
	
	mysql_query('
		UPDATE
			gt8_explorer_view
		SET
			vtoday	= 0
	') or die('ERROR: cron.explorer_view');
	
	
	//speed up analytics
	$tstart	= microtime(true);
	$lastId	= mysql_fetch_array(mysql_query("SELECT id_analytics FROM gt8_analytics_date ORDER BY id DESC LIMIT 1"));$lastId=$lastId[0]?$lastId[0]:0;
	mysql_query("
		INSERT INTO 
			gt8_analytics_date( id_analytics, date)
		SELECT
			p.id_analytics, DATE_FORMAT(p.creation,'%Y/%m/%d') AS `date`
		FROM
			gt8_analytics_page p
		WHERE
			p.id_analytics > $lastId
		GROUP BY
			YEAR(p.creation), MONTH(p.creation), DAY(p.creation)
		ORDER BY
			p.id
	") or die('ERROR: cron.analytics');
	
	$tend	= (microtime(true)-$tstart) * 1000;
	mysql_query('
		INSERT INTO gt8_analytics_performance(que4y, de5ay)
		VALUES("cron.update::analytics_date", '. $tend .')
	') or die('//#error: Performance error: '. isset($_SESSION['login']['level'])&&$_SESSION['login']['level']>7? mysql_error(): '!');
	
	//pass reset
	mysql_query("
		DELETE FROM
			gt8_users_pass_reset
		WHERE
			UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(creation) > 1800
	") or die('//#error: daily.cron.reset-pass: '. isset($_SESSION['login']['level'])&&$_SESSION['login']['level']>7? mysql_error(): '!');
?>