<?php
	function convertFromBytes( $size) {
		$size	= strtolower($size);
		$bytes	= (integer)$size;
		
		if ( strpos($size, 'k') !== false ) {
			$bytes	= intval($size) * 1024;
		} else if ( strpos($size, 'm') !== false ) {
			$bytes	= intval($size) * 1024 * 1024;
		} else if ( strpos($size, 'g') !== false ) {
			$bytes	= intval($size) * 1024 * 1024 * 1024;
		}
		return $bytes;
	}
?>