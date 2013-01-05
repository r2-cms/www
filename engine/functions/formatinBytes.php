<?php
	function formatInBytes( $size) {
		if ( $size > pow(1024, 3)) {
			$size	= round( $size/1000000, 2) ." GB";
		} else if ( $size > pow(1024, 2)) {
			$size	= round( $size/1000000, 2) ." MB";
		} else if ( $size > 1024) {
			$size	= round( $size/1000, 2) ." KB";
		} else {
			$size	= $size ." B";
		}
		return $size;
	}
?>