<?php
	require_once($_SERVER["DOCUMENT_ROOT"] ."/engine/connect.php");
	/**
	 * @function getByIP( ip)
	*/
	$GT8['blacklist']	= isset($GT8['blacklist'])? $GT8['blacklist']: array();
	function blacklist_getByIP( $ip=null, $format="OBJECT") {
		if ( empty($ip)) {
			$ip	= $_SERVER["REMOTE_ADDR"];
		}
		$ip	= RegExp($ip, '[0-9\.\-]+');
		
		/***************************** QUERY **********************************/
		$result	= mysql_query("
			SELECT
				id, ip
			FROM
				gt8_blacklist
			WHERE
				1 = 1 
				AND ip = '$ip'
				AND expires > NOW()
		") or die(mysql_error());
		//die($sql);
		
		/***************************** RESULTS ********************************/
		$stats	= array();
		if ( $format == "TABLE") {
			$stats	= '<table border="1" cellpadding="0" cellspacing="0" >
				<tr>
					<th>id</th>
					<th>ip</th>
				</tr>
			';
		}
		while( ($row = mysql_fetch_assoc( $result))) {
			if ( $format == "TABLE") {
				$s2	= '<tr>';
				foreach( $row as $name=>$value) {
					$s2	.= "<td>". $value ."</td>";
				}
				$stats	= "$stats$s2</tr>";
			} else {
				$s2	= array();
				foreach( $row as $name=>$value) {
					$s2[$name]	= $value;
				}
				$stats[]	= $s2;
			}
		}
		if ( $format == "TABLE") {
			$stats	.= "</table>";
		}
		
		return $stats;
	}
	
	$GT8['blacklist']['getByIP']	= blacklist_getByIP;
	if ( isset($_GET["print"]) ) {
		$_GET["print"]	= NULL;
		
		print($GT8['blacklist']['getByIP']( $_GET['ip'], "TABLE"));
		print($GT8['blacklist']['getByIP']( $_GET['ip'], "TABLE"));
	}
?>