<?php
	function splitJoin( $str = null, $ch_split = ',', $ch_join = ',', $chk_type = 'integer' ){
		if( is_null( $str ) ){
			return true;
		}
		
		$str  = explode( ''.$ch_split.'', $str );
		$rows = array();
		
		foreach( $str as $k => $v ){
			settype( $v, $chk_type );
			if ( $chk_type == "string") {
				$v	= mysql_real_escape_string($v);
			} else {
				settype( $v, $chk_type );
			}
			array_push( $rows, $v );
		}
		return join( $ch_join, $rows );	
    }
	
?>