<?php
	function includeif( $filename) {
		if (file_exists($filename)) {
			include($filename);
		}
	}
?>