<?php
	require_once($_SERVER["DOCUMENT_ROOT"] ."/engine/connect.php");
	/**
	 * @function getSumary( options, foundRows)
	 * @option format <OBJECT|TABLE>
	 * @option index 0
	 * @option limit 50
	 * @option orderBy <DATE>
	 * @option sortAsc <false|true>
	 * @option showInactives <false|true>
	 * @option idsLibrary id[,id,id,...]
	 * @option idsNotes id[,id,id,...]
	 * @option keywords
	 * @option session
	*/
	$GT8['notes']	= isset($GT8['notes'])? $GT8['notes']: array();
	function notes_getSumary( $props=null, &$foundRows=0) {
		$format			= isset($props["format"])? $props["format"]: "OBJECT";
		$index			= (integer)$props["index"];
		$limit			= (integer)$props["limit"];
		$idsLibrary		= RegExp($props["idsLibrary"].'', '[0-9\,\ ]+');
		$idsNotes		= RegExp($props["idsNotes"].'', '[0-9\,\ ]+');
		$orderBy		= isset($props["orderBy"])? $props["orderBy"]: "DATE";
		$sortAsc		= (integer)$props["sortAsc"]? '': ' DESC';
		$keywords		= isset($props["keywords"]) && strlen($props["keywords"])>1? mysql_real_escape_string($props["keywords"]): false;
		$session		= RegExp($props["session"], '[a-fA-F0-9\,]+');
		$showInactives	= (boolean)$props["showInactives"];
		
		if ( $format != "OBJECT" ) {
			global $GT8;
			//require_once( $GT8["root"] ."jsAdmin/check.php");
		}
		switch ( $format) {
			case "OBJECT":			break;
			case "TABLE":			break;
			default: $format	= "OBJECT"; break;
		}
		
		/***************************** WHERE **********************************/
		$fullsearchClause	= "";
		if ( $keywords) {
			$words	= explode( ' ', $keywords);
			$len	= count($words);
			for ( $i=0; $i<$len; $i++) {
				if ( strtolower(substr($words[$i], -1)) == "s") {
					$words[$i]	= substr($words[$i], 0, -1);
				}
			}
			for( $i=0; $i<count($words); $i++) {
				$fullsearchClause	.= " AND c.nome LIKE '%". $words[$i] ."%' ";
			}
		}
		if ( $idsLibrary) {
			$ids	= explode(',', $idsLibrary);
			$idsLibrary	= array();
			for( $i=0; $i<count($ids); $i++) {
				settype($ids[$i], 'integer');
				if ($ids[$i]) {
					$idsLibrary[]	= $ids[$i];
				}
			}
			if ( count($ids)) {
				$idsLibrary	= 'AND n.id_library IN ('. join(',', $idsLibrary) .')';
			}
		}
		if ( $idsNotes) {
			$ids	= explode(',', $idsNotes);
			$idsNotes	= array();
			for( $i=0; $i<count($ids); $i++) {
				settype($ids[$i], 'integer');
				if ($ids[$i]) {
					$idsNotes[]	= $ids[$i];
				}
			}
			if ( count($ids)) {
				$idsNotes	= 'AND n.id IN ('. join(',', $idsNotes) .')';
			}
		}
		
		$orderClause	= "";
		if ( $orderBy == "DATE") {
			$orderClause	= " ORDER BY n.creation ";
		}
		$orderClause	.= $sortAsc;
		
		if ( $showInactives) {
			$showInactives	= "";
		} else {
			$showInactives	= " AND n.inactive = 0";
		}
		
		if ( !$limit) {
			$limit	= 50;
		}
		$limit	= " LIMIT $index, ". max( 0, min($limit, 10000)) ." ";
		
		/***************************** SQL ************************************/
		$sql	= "
			SELECT
				n.id, n.id_library, n.id_analytics,
				IF(u.name<>'', u.name, n.name) AS name,
				n.mail, n.comment, n.site,
				n.positive, n.negative,
				IF ( a.id_users, CONCAT('/users/', u.login, '/imgs/profile-small.jpg'), '/API/user-imgs/default.jpg') AS img, 
				n.creation, n.creation AS tcreation,
				l.name AS library, l2.name AS parent,
				a.ip AS IP, a.browser, a.browser_v, a.OS,
				a.id_users,
				n.inactive
			FROM
				notes n
				INNER JOIN analytics a	ON a.id = n.id_analytics
				INNER JOIN library l	ON l.id = n.id_library
				LEFT JOIN library l2	ON l2.id = l.id_library
				LEFT JOIN users u 		ON u.id = a.id_users
			WHERE
				1 = 1
				$whereSession
				$fullsearchClause
				$showInactives
				$idsLibrary
				$idsNotes
			GROUP BY
				n.id
			$orderClause
			
			$limit
		";
		//die($sql);
		
		/***************************** COUNT **********************************/
		if ( $foundRows) {
			$foundRows	= 0;
			$sqlFR	= "
				SELECT
					COUNT(*) AS total
				FROM
					notes n
					INNER JOIN analytics a	ON a.id = n.id_analytics
					INNER JOIN library l	ON l.id = n.id_library
					LEFT JOIN users u 		ON u.id = a.id_users
				WHERE
					1 = 1
					$whereSession
					$fullsearchClause
					$showInactives
			";
			//die($sqlFR);
			$rowFR		= mysql_fetch_array(mysql_query($sqlFR));
			$foundRows	= $rowFR[0];
		}
		
		$result	= mysql_query( $sql) or die("SQL SELECT Error (1)". mysql_error());
		
		/***************************** RESULTS ********************************/
		$stats	= array();
		if ( $format == "TABLE") {
			$stats	= '<table border="1" cellpadding="0" cellspacing="0" >
				<tr>
					<th>id</th><th>id_users</th><th>id_library</th>
					<th>u.name</th>
					<th>mail</th><th>comment</th><th>site</th>
					<th>n.positive</th><th>negative</th>
					<th>img</th>
					<th>n.creation</th><th>tcreation</th>
					<th>library</th><th>l2.parent</th>
					<th>IP</th>
					<th>browser</th>
					<th>vrowser_v</th>
					<th>OS</th>
					<th>id_users</th>
					<th>inactive</th>
					<th>importPath</th>
				</tr>
			';
		}
		while( ($row = mysql_fetch_assoc( $result))) {
			if ( empty($row['parent'])) {
				$row['importPath']	= $row['library'];
			} else {
				$row['importPath']	= $row['parent'] .'.'. $row['library'];
			}
			
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
	
	$GT8['notes']['getSumary']	= notes_getSumary;
	if ( isset($_GET["print"]) ) {
		$_GET["print"]	= NULL;
		
		print($GT8['notes']['getSumary']( array(
			"format"	=> $_GET["format"],
			"index"		=> $_GET["index"],
			"limit"		=> $_GET["limit"],
			"orderBy"	=> $_GET["orderBy"],
			"sortAsc"	=> $_GET["sortAsc"],
			"keywords"	=> isset($_GET["keywords"]),
			"idsLibrary"=> $_GET["idsLibrary"],
			"idsNotes"	=> $_GET["idsNotes"],
			"session"	=> $_GET["session"],
			"format"	=> $_GET["format"],
			"showInactives"	=> isset($_GET["showInactives"])&&($_GET["showInactives"]==1 || $_GET["showInactives"]==true)
		)));
	}
?>