<?php
	require_once($_SERVER["DOCUMENT_ROOT"] ."/engine/connect.php");
	/**
	 * @function insert( $props)
	 * @param ip
	 * @param remarks
	 * @param expires integer indica em quantas horas o bloquio deverá expirar
	*/
	$GT8['blacklist']	= isset($GT8['blacklist'])? $GT8['blacklist']: array();
	function blacklist_insert( $props) {
		
		$ip	= RegExp( $props['ip'], '[0-9\.]+');
		$remarks	= addslashes(substr($props['remarks'], 0, 32));
		$expires	= (integer)$props['expires'];
		
		if ( !$expires) {
			$expires	= 24;
		}
		
		if ( empty($ip)) {
			$ip	= $_SERVER["REMOTE_ADDR"];
		}
		
		$idAnalytics	= (integer)$_SESSION["analytics"]["id"];
		
		/***************************** QUERY **********************************/
		mysql_query("
			INSERT INTO
				gt8_blacklist (
					ip,
					id_analytics,
					modification,
					expires,
					remarks
				)
			SELECT
				'$ip',
				$idAnalytics,
				NOW(),
				NOW() + INTERVAL $expires HOUR,
				'$remarks'
			FROM
				gt8_blacklist
			WHERE
				ip = '$ip' AND
				id_anaytics = $idAnalytics
			HAVING
				COUNT(*) = 0
		") or die(mysql_error());
		//die($sql);
		
		return $stats;
	}
	
	$GT8['blacklist']['insert']	= blacklist_insert;
	if ( isset($_GET["print"]) ) {
		$_GET["print"]	= NULL;
		
		print($GT8['blacklist']['insert'](
			array(
				'ip'	=> $_GET['ip'],
				'remarks'	=> $_GET['remarks'],
				'expires'	=> $_GET['expires']
			)
		));
	}
?>