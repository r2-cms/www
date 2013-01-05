<?php
	/*
		CheckPrivileges( $field = 0, $format="OBJECT", $url)
		
		return format:
			200	- OK
			403	- forbidden
			404	- not found
			JSON
				status = 200
			OBJECT
				integer
	*/
	function CheckPrivileges( $field = '*', $format="", $url=null, $atLeastOrDie=-500) {
		global $GT8;
		
		if ( !$field) {
			$field	= '*';
		}
		
		require_once( SROOT .'engine/functions/Pager.php');
		if ( !isset($url) || empty($url) ) {
			$url	= $_SERVER["PHP_SELF"];
			$url	= substr($url, 0, strpos($url, '/index.php')+1);
			$url	= str_replace(DS. $GT8['admin']['root'], '', $url);
		}
		$pager	= Pager( array(
				'sql'		=> 'privileges.list',
				'replace'	=> array(
					array('= u.id', '='.$_SESSION["login"]["id"])
				),
				'required'		=> array(
					array('p.url', $url),
					array('p.field', $field)
				),
				'foundRows'	=> 1
		));
		if ( $atLeastOrDie > -500) {
			$prv	= isset($pager['rows'][0])? (integer)$pager['rows'][0]['iprivilege']: -404;
			
			if ( $prv < $atLeastOrDie)  {
				if ( $format === 'JSON') {
					die("
						//#error: Privilégios insuficientes para esta ação!
						status = -404
						privilege = '-'
						iprivilege = 0
					");
				} else if ( $format === 'OBJECT') {
					return -404;
				} else {
					$codeError	= $prv;
					$exists	= false;
					if ( file_exists( SROOT .'engine/controllers/account/Forbidden.php')) {
						require_once( SROOT .'engine/controllers/account/Forbidden.php');
						$exists	= true;
					}
					if ( file_exists( SROOT .'engine/views/account/forbidden.inc')) {
						include( SROOT .'engine/views/account/forbidden.inc');
						$exists	= true;
					}
					if ( !$exists ) {
						die("//#error: Privilégios insuficientes para esta ação.". PHP_EOL);
					}
					die();
				}
			}
		}
		
		if ( !isset($pager['rows'][0])) {
			if ( $format === "OBJECT") {
				return -404;
			} else if ($format === "JSON") {
				die("
					//#error: coluna não registrada em privilégios. Contate o webmaster!
					status = -404
					privilege = '-'
					iprivilege = 0
				");
			}
		}
		$row	= $pager['rows'][0];
		
		if ( $format === 'JSON' ) {
			print("
				privilege	= '{$row['privilege']}'
				iprivilege	= '{$row['iprivilege']}'
			");
		}
		return $row['iprivilege'];
	}
?>