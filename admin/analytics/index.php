<?php
	set_time_limit(3);
	
	require_once("../../engine/connect.php");
	//validação dos campos
	$duration		= (integer)$_GET['duration'];
	$date			= (integer)$_GET['date'];
	$url			= mysql_real_escape_string( substr( utf8_decode($_GET['url']),0,128) );
	$scroll			= (integer)$_GET['scroll'];
	$referrer		= mysql_real_escape_string( substr( utf8_decode($_GET['referrer']),0,128) );
	$browserCode	= (integer)$_GET['browser'];
	$browserVersion	= (integer)$_GET['browser_v'];
	$OSCode			= (integer)$_GET['OS'];
	$idUser			= isset($_SESSION["login"]['id'])? $_SESSION["login"]['id']: 0;
	$ip				= $_SERVER["REMOTE_ADDR"];
	$pageUnloading	= (integer)$_GET['unloadingEvent'];
	
	if ( !isset($_SESSION['GT8']['tstart-lastpage'])) {
		$_SESSION['GT8']['tstart-lastpage']	= time();
	}
	
	if ( time()-$_SESSION['GT8']['tstart-lastpage'] > 1800) {
		unset($_SESSION["analytics"]);
		$_SESSION['GT8']['tstart']	= time();
		$_SESSION['GT8']['tstart-lastpage']	= time();
	}
	$_SESSION['GT8']['tstart-lastpage']	= time();
	
	$Analytics		= $_SESSION["analytics"];
	
	if ( isset($Analytics["id"])) {
		setcookie('session', $Analytics["id"], time() + 60 * 60 * 24 * 60, '/');
	}
	
	if ( !$Analytics) {
		mysql_query("
			INSERT INTO
				gt8_analytics(
					id_users,
					ip,
					browser,
					browser_v,
					OS,
					referrer
				)
			VALUES(
				$idUser,
				'$ip',
				$browserCode,
				$browserVersion,
				$OSCode,
				'$referrer'
			)
		") or die("DB query error!");
		
		$_SESSION["analytics"]	= array(
			"id"			=> mysql_insert_id()
		);
		$Analytics	= $_SESSION["analytics"];
		$pageUnloading	= null;
	}
	
	if ( $pageUnloading ) {
		mysql_query("
			UPDATE
				gt8_analytics_page
			SET
				`delay`	= $duration,
				`scroll`	= $scroll
			WHERE
				id	= $pageUnloading AND
				id_analytics = {$Analytics['id']}
			LIMIT 1
		");
		
	} else {
		mysql_query("
			INSERT INTO
				gt8_analytics_page (
					id_analytics,
					page,
					scroll,
					delay
				) VALUES(
					{$Analytics['id']},
					'$url',
					$scroll,
					0
				)
		");
		$_SESSION['analytics-page']	= mysql_insert_id();
		print("id = ". $_SESSION['analytics-page'] .";". PHP_EOL);
	}
	
	print("session = ". $Analytics["id"] .";". PHP_EOL);
	
	//MODULES
	if ( isset($_GET['module'])) {
		$module	= str_replace('::', '/', $_GET['module']);
		if ( file_exists(SROOT.'engine/controllers/'. $module .'.php')) {
			require_once(SROOT.'engine/controllers/'. $module .'.php');
		}
	}
?>