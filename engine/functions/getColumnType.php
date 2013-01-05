<?php
	function getColumnType( $table, $field) {
		//type
		$result	= mysql_query("
			DESCRIBE gt8_$table
		") or die('Can not get table data type in SQL Update');
		
		$Field	= array();
		while( $row = mysql_fetch_assoc($result)) {
			$Field[]	= $row;
		}
		$fieldFound	= false;
		for ($i=0; $i<count($Field); $i++) {
			if ( $Field[$i]['Field'] == $field ) {
				$Field	= $Field[$i];
				$fieldFound	= true;
				break;
			}
		}
		if ( !$fieldFound) {
			$Field	= null;
		}
		return $Field;
	}

?>